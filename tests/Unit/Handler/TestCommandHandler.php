<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Test\Unit\Component\CommandBus\Handler;

use Communitales\Component\CommandBus\Attribute\AsCommandHandler;
use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandResult;
use Communitales\Component\CommandBus\Handler\Result\CommandResultInterface;
use Communitales\Component\StatusBus\StatusMessage;

/**
 * Class TestCommandHandler
 */
/** @implements CommandHandlerInterface<TestCommand> */
#[AsCommandHandler(TestCommand::class)]
class TestCommandHandler implements CommandHandlerInterface
{
    /** @param TestCommand $command */
    public function handle(CommandInterface $command): CommandResultInterface
    {
        if ($command->test === 'success') {
            return CommandResult::success(
                StatusMessage::success($command->test)
            );
        }

        return CommandResult::error(
            StatusMessage::error($command->test)
        );
    }
}
