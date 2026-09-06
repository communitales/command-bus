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
use Communitales\Component\CommandBus\Handler\Result\CommandHandlerResultInterface;
use Communitales\Component\CommandBus\Handler\Result\ErrorResult;
use Communitales\Component\CommandBus\Handler\Result\SuccessResult;
use Communitales\Component\StatusBus\StatusMessage;

/**
 * Class TestCommandHandler
 */
/** @implements CommandHandlerInterface<TestCommand> */
#[AsCommandHandler(TestCommand::class)]
class TestCommandHandler implements CommandHandlerInterface
{
    /** @param TestCommand $command */
    public function handle(CommandInterface $command): CommandHandlerResultInterface
    {
        if ($command->test === 'success') {
            return new SuccessResult(
                StatusMessage::success($command->test)
            );
        }

        return new ErrorResult(
            StatusMessage::error($command->test)
        );
    }
}
