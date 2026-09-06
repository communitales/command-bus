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
 * An exception representing an CommandHandlerResult.
 * This is useful if you want to return a result from a sub method of a handler method.
 */
class CommandResultException extends RuntimeException
{
    public function __construct(
        public readonly CommandHandlerResultInterface $commandResult,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
