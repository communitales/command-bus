<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Component\CommandBus;

use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandResultInterface;

/**
 * Interface CommandBusInterface
 *
 * @see https://symfony.com/blog/new-in-symfony-3-3-service-locators
 */
interface CommandBusInterface
{
    public function dispatch(
        CommandInterface $command,
        bool $displayStatusMessage = true
    ): CommandResultInterface;
}
