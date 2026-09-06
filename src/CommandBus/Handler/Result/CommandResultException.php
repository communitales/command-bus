<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Component\CommandBus\Handler\Result;

use RuntimeException;
use Throwable;

/**
 * An exception carrying a command result.
 * This is useful for returning a result early from a nested handler method.
 */
class CommandResultException extends RuntimeException
{
    public function __construct(
        public readonly CommandResultInterface $commandResult,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
