<?php

/**
 * @copyright   Copyright (c) 2020 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\CommandBus;

/**
 * Trait CommandBusAwareTrait
 */
trait CommandBusAwareTrait
{

    /**
     * @var CommandBusInterface
     */
    protected CommandBusInterface $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function setCommandBus(CommandBusInterface $commandBus): void
    {
        $this->commandBus = $commandBus;
    }

}
