<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Test\Unit\Component\CommandBus\Fixture;

use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandHandlerResultInterface;
use Communitales\Component\CommandBus\Handler\Result\SuccessResult;
use LogicException;
use Override;

class BrokenCommandHandler implements CommandHandlerInterface
{
    public function __construct()
    {
        throw new LogicException('Constructor is broken for testing');
    }

    #[Override]
    public function canHandle(CommandInterface $command): bool
    {
        return false;
    }

    #[Override]
    public function handle(CommandInterface $command): CommandHandlerResultInterface
    {
        return new SuccessResult();
    }
}
