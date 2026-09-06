<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Test\Unit\Component\CommandBus;

use Communitales\Component\CommandBus\CanNotDispatchCommandException;
use Communitales\Component\CommandBus\CommandBus;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Communitales\Component\CommandBus\Handler\Result\AbstractResult;
use Communitales\Component\CommandBus\Handler\Result\SuccessResult;
use Communitales\Component\StatusBus\StatusBusInterface;
use Communitales\Component\StatusBus\StatusMessage;
use Communitales\Test\Unit\Component\CommandBus\Handler\TestCommand;
use Communitales\Test\Unit\Component\CommandBus\Handler\TestCommandHandler;
use LogicException;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Contracts\Service\ServiceCollectionInterface;

/**
 * Class CommandBusTest
 */
#[CoversClass(CommandBus::class)]
#[UsesClass(AbstractResult::class)]
#[UsesClass(CanNotDispatchCommandException::class)]
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
        $this->assertInstanceOf(SuccessResult::class, $commandBus->dispatch(new TestCommand('success')));
        $this->assertSame(1, HandlerCreationCounter::$requested);
        $this->assertSame(0, HandlerCreationCounter::$unrelated);
    }

    public function testMissingHandlerRaisesException(): void
    {
        $commandBus = new CommandBus($this->createHandlerCollection([]));

        $this->expectException(CanNotDispatchCommandException::class);
        $this->expectExceptionMessageIsOrContains(TestCommand::class);

        $commandBus->dispatch(new TestCommand('success'));
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
