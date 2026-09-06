<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Test\Unit\Component\CommandBus;

use Closure;
use Communitales\Component\CommandBus\CanNotDispatchCommandException;
use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\CommandBus;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandResult;
use Communitales\Component\CommandBus\Handler\Result\CommandResultException;
use Communitales\Component\CommandBus\Handler\Result\CommandResultInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandResultStatus;
use Communitales\Component\Log\ExceptionLoggerInterface;
use Communitales\Component\StatusBus\StatusBusInterface;
use Communitales\Component\StatusBus\StatusMessage;
use Communitales\Test\Unit\Component\CommandBus\Handler\TestCommand;
use Communitales\Test\Unit\Component\CommandBus\Handler\TestCommandHandler;
use Doctrine\DBAL\Exception as DbalException;
use LogicException;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Contracts\Service\ServiceCollectionInterface;

/**
 * Class CommandBusTest
 */
#[CoversClass(CommandBus::class)]
#[UsesClass(CanNotDispatchCommandException::class)]
#[UsesClass(CommandResult::class)]
#[UsesClass(CommandResultException::class)]
final class CommandBusTest extends TestCase
{
    public function testHandlersAreLoadedLazilyByCommandClass(): void
    {
        HandlerCreationCounter::$requested = 0;
        HandlerCreationCounter::$unrelated = 0;

        $handlers = $this->createHandlerCollection([
            TestCommand::class => static function (): TestCommandHandler {
                ++HandlerCreationCounter::$requested;

                return new TestCommandHandler();
            },
            'unrelated' => static function (): never {
                ++HandlerCreationCounter::$unrelated;

                throw new LogicException('An unrelated handler must not be created.');
            },
        ]);

        $commandBus = new CommandBus($handlers);

        $this->assertSame(0, HandlerCreationCounter::$requested);
        $this->assertSame(0, HandlerCreationCounter::$unrelated);
        $this->assertSame(CommandResultStatus::Success, $commandBus->dispatch(new TestCommand('success'))->getStatus());
        $this->assertSame(1, HandlerCreationCounter::$requested);
        $this->assertSame(0, HandlerCreationCounter::$unrelated);
    }

    public function testMissingHandlerReturnsFailedResultAndLogsException(): void
    {
        $exceptionLogger = $this->createMock(ExceptionLoggerInterface::class);
        $exceptionLogger
            ->expects($this->once())
            ->method('logException')
            ->with($this->isInstanceOf(CanNotDispatchCommandException::class));
        $commandBus = new CommandBus($this->createHandlerCollection([]), $exceptionLogger);

        $result = $commandBus->dispatch(new TestCommand('success'));

        $this->assertSame(CommandResultStatus::Failed, $result->getStatus());
        $this->assertSame('status_message.fatal_error', $result->getStatusMessage()?->getMessage());
    }

    public function testDatabaseExceptionReturnsFailedResultAndUsesDatabaseMessage(): void
    {
        $exception = new TestDbalException('Database unavailable');
        $exceptionLogger = $this->createMock(ExceptionLoggerInterface::class);
        $exceptionLogger
            ->expects($this->once())
            ->method('logException')
            ->with($this->identicalTo($exception));
        $handlers = $this->createHandlerCollection([
            TestCommand::class => static fn (): CallbackCommandHandler => new CallbackCommandHandler(
                static fn (CommandInterface $command): never => throw $exception
            ),
        ]);
        $commandBus = new CommandBus($handlers, $exceptionLogger);

        $result = $commandBus->dispatch(new TestCommand('success'));

        $this->assertSame(CommandResultStatus::Failed, $result->getStatus());
        $this->assertSame('status_message.database_error', $result->getStatusMessage()?->getMessage());
    }

    public function testUnexpectedExceptionReturnsFailedResultAndLogsException(): void
    {
        $exception = new RuntimeException('Unexpected failure');
        $exceptionLogger = $this->createMock(ExceptionLoggerInterface::class);
        $exceptionLogger
            ->expects($this->once())
            ->method('logException')
            ->with($this->identicalTo($exception));
        $handlers = $this->createHandlerCollection([
            TestCommand::class => static fn (): CallbackCommandHandler => new CallbackCommandHandler(
                static fn (CommandInterface $command): never => throw $exception
            ),
        ]);
        $commandBus = new CommandBus($handlers, $exceptionLogger);

        $result = $commandBus->dispatch(new TestCommand('success'));

        $this->assertSame(CommandResultStatus::Failed, $result->getStatus());
        $this->assertSame('status_message.fatal_error', $result->getStatusMessage()?->getMessage());
    }

    public function testCommandResultExceptionReturnsItsResult(): void
    {
        $expectedResult = CommandResult::error(StatusMessage::error('invalid'));
        $exceptionLogger = $this->createMock(ExceptionLoggerInterface::class);
        $exceptionLogger->expects($this->never())->method('logException');
        $handlers = $this->createHandlerCollection([
            TestCommand::class => static fn (): CallbackCommandHandler => new CallbackCommandHandler(
                static fn (CommandInterface $command): never => throw new CommandResultException($expectedResult)
            ),
        ]);
        $commandBus = new CommandBus($handlers, $exceptionLogger);

        $this->assertSame($expectedResult, $commandBus->dispatch(new TestCommand('error')));
    }

    public function testResultStatusMessageIsPublished(): void
    {
        $statusBus = new CollectingStatusBus();
        $handlers = $this->createHandlerCollection([
            TestCommand::class => static fn (): TestCommandHandler => new TestCommandHandler(),
        ]);
        $commandBus = new CommandBus($handlers, statusBus: $statusBus);

        $commandBus->dispatch(new TestCommand('success'));

        $this->assertCount(1, $statusBus->messages);
        $this->assertSame('success', $statusBus->messages[0]->getMessage());
    }

    public function testFailuresInStatusPublishingAndLoggingNeverEscapeDispatch(): void
    {
        $exceptionLogger = $this->createMock(ExceptionLoggerInterface::class);
        $exceptionLogger
            ->expects($this->once())
            ->method('logException')
            ->willThrowException(new RuntimeException('Logger unavailable'));
        $handlers = $this->createHandlerCollection([
            TestCommand::class => static fn (): TestCommandHandler => new TestCommandHandler(),
        ]);
        $commandBus = new CommandBus($handlers, $exceptionLogger, new ThrowingStatusBus());

        $result = $commandBus->dispatch(new TestCommand('success'));

        $this->assertSame(CommandResultStatus::Success, $result->getStatus());
    }

    /**
     * @param array<string, callable(): CommandHandlerInterface<*>> $factories
     *
     * @return ServiceCollectionInterface<CommandHandlerInterface<*>>
     */
    private function createHandlerCollection(array $factories): ServiceCollectionInterface
    {
        return new CommandHandlerServiceLocator($factories);
    }
}

final class HandlerCreationCounter
{
    public static int $requested = 0;

    public static int $unrelated = 0;
}

/** @extends ServiceLocator<CommandHandlerInterface<*>> */
final class CommandHandlerServiceLocator extends ServiceLocator
{
}

final class CollectingStatusBus implements StatusBusInterface
{
    /** @var list<StatusMessage> */
    public array $messages = [];

    #[Override]
    public function publish(StatusMessage $message): void
    {
        $this->messages[] = $message;
    }
}

final class ThrowingStatusBus implements StatusBusInterface
{
    #[Override]
    public function publish(StatusMessage $message): never
    {
        throw new RuntimeException('Status bus unavailable');
    }
}

/** @implements CommandHandlerInterface<CommandInterface> */
final readonly class CallbackCommandHandler implements CommandHandlerInterface
{
    /** @param Closure(CommandInterface): CommandResultInterface $callback */
    public function __construct(private Closure $callback)
    {
    }

    #[Override]
    public function handle(CommandInterface $command): CommandResultInterface
    {
        return ($this->callback)($command);
    }
}

final class TestDbalException extends RuntimeException implements DbalException
{
}
