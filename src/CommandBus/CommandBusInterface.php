<?php
/**
 * @copyright   Copyright (c) 2020 - 2023 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\CommandBus;

use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandHandlerResultInterface;

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
    ): CommandHandlerResultInterface;
}
