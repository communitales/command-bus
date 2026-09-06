<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Test\Unit\Component\CommandBus\DependencyInjection;

use Communitales\Component\CommandBus\Attribute\AsCommandHandler;
use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\CommandBus;
use Communitales\Component\CommandBus\CommandBusBundle;
use Communitales\Component\CommandBus\DependencyInjection\CommandBusExtension;
use Communitales\Component\CommandBus\DependencyInjection\CommandHandlerCompilerPass;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandResult;
use Communitales\Component\CommandBus\Handler\Result\CommandResultInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandResultStatus;
use Exception;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

#[CoversClass(AsCommandHandler::class)]
#[CoversClass(CommandBusBundle::class)]
#[CoversClass(CommandBusExtension::class)]
#[CoversClass(CommandHandlerCompilerPass::class)]
#[UsesClass(CommandBus::class)]
#[UsesClass(CommandResult::class)]
final class CommandBusBundleTest extends TestCase
{
    /** @throws Exception */
    public function testAttributeRegistersLazyHandlerByCommandClass(): void
    {
        AttributedHandler::$instances = 0;
        $container = $this->createContainer(AttributedHandler::class);
        $container->compile();

        $this->assertSame(0, AttributedHandler::$instances);

        $commandBus = $this->getCommandBus($container);
        $this->assertSame(CommandResultStatus::Success, $commandBus->dispatch(new AttributedCommand())->getStatus());
        $this->assertSame(1, AttributedHandler::$instances);
    }

    public function testDuplicateCommandHandlersAreRejectedDuringCompilation(): void
    {
        $container = $this->createContainer(AttributedHandler::class, DuplicateAttributedHandler::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIsOrContains('has multiple handlers');

        $container->compile();
    }

    /**
     * @param class-string ...$handlerClasses
     */
    private function createContainer(string ...$handlerClasses): ContainerBuilder
    {
        $container = new ContainerBuilder();
        new CommandBusBundle()->build($container);
        new CommandBusExtension()->load([], $container);
        $container->getDefinition(CommandBus::class)->setPublic(true);

        foreach ($handlerClasses as $handlerClass) {
            $container->register($handlerClass, $handlerClass)
                ->setAutoconfigured(true);
        }

        return $container;
    }

    /** @throws Exception */
    private function getCommandBus(ContainerBuilder $container): CommandBus
    {
        $commandBus = $container->get(CommandBus::class);

        if (!$commandBus instanceof CommandBus) {
            throw new LogicException('The container did not return a CommandBus.');
        }

        return $commandBus;
    }
}

final readonly class AttributedCommand implements CommandInterface
{
}

/** @implements CommandHandlerInterface<AttributedCommand> */
#[AsCommandHandler(AttributedCommand::class)]
final class AttributedHandler implements CommandHandlerInterface
{
    public static int $instances = 0;

    public function __construct()
    {
        ++self::$instances;
    }

    /** @param AttributedCommand $command */
    public function handle(CommandInterface $command): CommandResultInterface
    {
        return CommandResult::success();
    }
}

/** @implements CommandHandlerInterface<AttributedCommand> */
#[AsCommandHandler(AttributedCommand::class)]
final class DuplicateAttributedHandler implements CommandHandlerInterface
{
    /** @param AttributedCommand $command */
    public function handle(CommandInterface $command): CommandResultInterface
    {
        return CommandResult::success();
    }
}
