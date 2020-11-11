<?php

/**
 * @copyright   Copyright (c) 2020 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\CommandBus\Handler\Result;

use Communitales\Component\StatusBus\StatusMessage;

/**
 * Interface CommandHandlerResultInterface
 */
interface CommandHandlerResultInterface
{

    /**
     * @return StatusMessage|null
     */
    public function getStatusMessage(): ?StatusMessage;

}
