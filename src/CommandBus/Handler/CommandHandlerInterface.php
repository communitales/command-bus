<?php

/**
 * @copyright   Copyright (c) 2020 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\CommandBus\Handler;

use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandHandlerResultInterface;

/**
 * Interface CommandHandlerInterface
 */
interface CommandHandlerInterface
{

    /**
     * @param CommandInterface $command
     *
     * @return bool
     */
    public function canHandle(CommandInterface $command): bool;

    /**
     * @param CommandInterface $command
     *
     * @return CommandHandlerResultInterface
     */
    public function handle(CommandInterface $command): CommandHandlerResultInterface;
}
