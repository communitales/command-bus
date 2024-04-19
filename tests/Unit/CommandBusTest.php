<?php

/**
 * @copyright   Copyright (c) 2024 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Test\Unit\Component\CommandBus;

use Communitales\Component\CommandBus\CommandBus;
use Communitales\Test\Unit\Component\CommandBus\Fixture\BrokenCommandHandler;
use Communitales\Test\Unit\Component\CommandBus\Fixture\RewindableGenerator;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Class CommandBusTest
 */
class CommandBusTest extends TestCase
{
    public function testContructorException(): void
    {
        $iterator = new RewindableGenerator(static function () {
            yield 0 => (new BrokenCommandHandler());
        }, 1);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Constructor is broken for testing');

        new CommandBus($iterator);
    }
}
