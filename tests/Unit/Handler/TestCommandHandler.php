<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Test\Unit\Component\CommandBus\Handler;

use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerTrait;
use Communitales\Component\CommandBus\Handler\Result\CommandHandlerResultInterface;
use Communitales\Component\CommandBus\Handler\Result\ErrorResult;
use Communitales\Component\CommandBus\Handler\Result\SuccessResult;
use Communitales\Component\StatusBus\StatusMessage;
use Override;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * Class TestCommandHandler
 */
class TestCommandHandler implements CommandHandlerInterface
{
    use CommandHandlerTrait;

    #[Override]
    public function canHandle(CommandInterface $command): bool
    {
        return $command instanceof TestCommand;
    }

    public function test(TestCommand $command): CommandHandlerResultInterface
    {
        if ($command->test === 'success') {
            return new SuccessResult(
                StatusMessage::createSuccessMessage(
                    new TranslatableMessage(
                        $command->test
                    )
                )
            );
        }

        return new ErrorResult(
            StatusMessage::createErrorMessage(
                $command->test
            )
        );
    }
}
