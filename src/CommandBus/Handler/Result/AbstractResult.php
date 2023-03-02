<?php
/**
 * @copyright   Copyright (c) 2020 - 2023 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\CommandBus\Handler\Result;

use Communitales\Component\StatusBus\StatusMessage;

abstract class AbstractResult implements CommandHandlerResultInterface
{
    private ?StatusMessage $statusMessage;

    public function __construct(?StatusMessage $statusMessage = null)
    {
        $this->statusMessage = $statusMessage;
    }

    public function getStatusMessage(): ?StatusMessage
    {
        return $this->statusMessage;
    }
}
