<?php

/**
 * @copyright   Copyright (c) 2020 - 2024 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\CommandBus\Handler\Result;

use Communitales\Component\StatusBus\StatusMessage;
use Override;

abstract class AbstractResult implements CommandHandlerResultInterface
{
    public function __construct(public readonly ?StatusMessage $statusMessage = null)
    {
    }

    #[Override]
    public function getStatusMessage(): ?StatusMessage
    {
        return $this->statusMessage;
    }
}
