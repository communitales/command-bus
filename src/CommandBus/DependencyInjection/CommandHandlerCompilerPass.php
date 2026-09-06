<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Component\CommandBus\DependencyInjection;

use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Override;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

use function is_a;
use function is_array;
use function is_string;
use function sprintf;

final class CommandHandlerCompilerPass implements CompilerPassInterface
{
    public const string HANDLER_TAG = 'communitales.command_handler';

    #[Override]
    public function process(ContainerBuilder $container): void
    {
        /** @var array<class-string<CommandInterface>, string> $handlersByCommand */
        $handlersByCommand = [];

        foreach ($container->findTaggedServiceIds(self::HANDLER_TAG, true) as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                if (!is_array($attributes)) {
                    throw new InvalidArgumentException(sprintf(
                        'The tag configuration of command handler "%s" must be an array.',
                        $serviceId
                    ));
                }

                $commandClass = $attributes['handles'] ?? null;

                if (!is_string($commandClass) || !is_a($commandClass, CommandInterface::class, true)) {
                    throw new InvalidArgumentException(sprintf(
                        'Command handler "%s" must declare a valid CommandInterface class in the "handles" attribute.',
                        $serviceId
                    ));
                }

                if (isset($handlersByCommand[$commandClass])) {
                    throw new InvalidArgumentException(sprintf(
                        'Command "%s" has multiple handlers: "%s" and "%s".',
                        $commandClass,
                        $handlersByCommand[$commandClass],
                        $serviceId
                    ));
                }

                $definition = $container->findDefinition($serviceId);
                $handlerClass = $definition->getClass();

                if (!is_string($handlerClass) || !is_a($handlerClass, CommandHandlerInterface::class, true)) {
                    throw new InvalidArgumentException(sprintf(
                        'Command handler service "%s" must implement "%s".',
                        $serviceId,
                        CommandHandlerInterface::class
                    ));
                }

                $handlersByCommand[$commandClass] = $serviceId;
            }
        }
    }
}
