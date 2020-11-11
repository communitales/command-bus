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
 * Class AbstractResult
 */
abstract class AbstractResult implements CommandHandlerResultInterface
{

    /**
     * @var StatusMessage|null
     */
    private ?StatusMessage $statusMessage;

    /**
     * @param StatusMessage|null $statusMessage
     */
    public function __construct(?StatusMessage $statusMessage = null)
    {
        $this->statusMessage = $statusMessage;
    }

    /**
     * @return StatusMessage|null
     */
    public function getStatusMessage(): ?StatusMessage
    {
        return $this->statusMessage;
    }

}
