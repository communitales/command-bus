<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Test\Unit\Component\CommandBus;

use Communitales\Component\CommandBus\CommandBus;
use Communitales\Test\Unit\Component\CommandBus\Fixture\BrokenCommandHandler;
use Exception;
use Generator;
use IteratorAggregate;
use LogicException;
use Override;
use PHPUnit\Framework\TestCase;

/**
 * Class CommandBusTest
 */
final class CommandBusTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testConstructorException(): void
    {
        $iterator = new /** @implements IteratorAggregate<BrokenCommandHandler> */ class () implements IteratorAggregate {
            #[Override]
            public function getIterator(): Generator
            {
                yield new BrokenCommandHandler();
            }
        };

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageIsOrContains('Constructor is broken for testing');

        new CommandBus($iterator);
    }
}
