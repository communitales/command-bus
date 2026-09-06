<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Test\Unit\Component\CommandBus\Handler;

use Communitales\Component\CommandBus\Command\CommandInterface;

/**
 * Class TestCommand
 */
readonly class TestCommand implements CommandInterface
{
    public function __construct(public string $test)
    {
    }
}
