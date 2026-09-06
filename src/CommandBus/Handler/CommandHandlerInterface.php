<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Component\CommandBus\Handler;

use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandHandlerResultInterface;

/**
 * Interface CommandHandlerInterface
 */
interface CommandHandlerInterface
{
    public function canHandle(CommandInterface $command): bool;

    public function handle(CommandInterface $command): CommandHandlerResultInterface;
}
