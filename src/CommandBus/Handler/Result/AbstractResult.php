<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
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
