<?php

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Component\CommandBus;

use RuntimeException;

use function sprintf;

class CanNotDispatchCommandException extends RuntimeException
{
    public static function forClass(string $class): self
    {
        return new self(sprintf('The command bus has no handler for class "%s"', $class));
    }
}
