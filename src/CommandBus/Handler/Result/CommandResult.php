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

readonly class CommandResult implements CommandResultInterface
{
    public function __construct(
        private CommandResultStatus $status,
        private ?StatusMessage $statusMessage = null
    ) {
    }

    public static function success(?StatusMessage $statusMessage = null): self
    {
        return new self(CommandResultStatus::Success, $statusMessage);
    }

    public static function error(?StatusMessage $statusMessage = null): self
    {
        return new self(CommandResultStatus::Error, $statusMessage);
    }

    public static function failed(?StatusMessage $statusMessage = null): self
    {
        return new self(CommandResultStatus::Failed, $statusMessage);
    }

    #[Override]
    public function getStatus(): CommandResultStatus
    {
        return $this->status;
    }

    #[Override]
    public function getStatusMessage(): ?StatusMessage
    {
        return $this->statusMessage;
    }
}
