<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2020 - 2026 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
