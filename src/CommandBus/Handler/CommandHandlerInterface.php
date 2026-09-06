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

/** @template-contravariant TCommand of CommandInterface */
interface CommandHandlerInterface
{
    /** @param TCommand $command */
    public function handle(CommandInterface $command): CommandHandlerResultInterface;
}
