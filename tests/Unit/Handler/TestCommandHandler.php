<?php

/**
 * @copyright   Copyright (c) 2024 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Test\Unit\Component\CommandBus\Handler;

use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerTrait;
use Communitales\Component\CommandBus\Handler\Result\CommandHandlerResultInterface;
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
        return new SuccessResult(
            StatusMessage::createSuccessMessage(
                new TranslatableMessage(
                    $command->test
                )
            )
        );
    }

}
