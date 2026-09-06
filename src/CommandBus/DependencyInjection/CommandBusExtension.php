<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Component\CommandBus\DependencyInjection;

use Communitales\Component\CommandBus\CommandBus;
use Communitales\Component\CommandBus\CommandBusInterface;
use Override;
use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class CommandBusExtension extends Extension
{
    /**
     * @param array<array<array-key, mixed>> $configs
     */
    #[Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        $handlers = new ServiceLocatorArgument(new TaggedIteratorArgument(
            CommandHandlerCompilerPass::HANDLER_TAG,
            'handles'
        ));

        $container->register(CommandBus::class)
            ->setAutowired(true)
            ->setArgument('$handlers', $handlers);

        $container->setAlias(CommandBusInterface::class, CommandBus::class);
    }
}
