<?php

/**
 * @copyright   Copyright (c) 2020 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\CommandBus\Handler;

use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\Result\ErrorResult;
use Communitales\Component\StatusBus\StatusMessage;
use function get_class;
use function sprintf;

/**
 * Trait CommandHandlerTrait
 */
trait CommandHandlerTrait
{

    /**
     * @param CommandInterface $command
     *
     * @return ErrorResult
     */
    private function canNotHandle(CommandInterface $command): ErrorResult
    {
        return new ErrorResult(
            StatusMessage::createErrorMessage(
                sprintf('Handler %s can not handle %s', get_class($this), get_class($command))
            )
        );
    }
}
