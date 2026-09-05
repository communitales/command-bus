<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2020 - 2026 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Test\Unit\Component\CommandBus;

use Communitales\Component\CommandBus\CommandBus;
use Communitales\Test\Unit\Component\CommandBus\Fixture\BrokenCommandHandler;
use Communitales\Test\Unit\Component\CommandBus\Fixture\RewindableGenerator;
use Exception;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Class CommandBusTest
 */
class CommandBusTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testConstructorException(): void
    {
        $iterator = new RewindableGenerator(static function () {
            yield 0 => (new BrokenCommandHandler());
        }, 1);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageIsOrContains('Constructor is broken for testing');

        new CommandBus($iterator);
    }
}
