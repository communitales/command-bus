<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Component\CommandBus\Handler\Result;

use Communitales\Component\StatusBus\StatusMessage;

interface CommandResultInterface
{
    public function getStatus(): CommandResultStatus;

    public function getStatusMessage(): ?StatusMessage;
}
