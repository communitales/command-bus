<?php

/**
 * @copyright   Copyright (c) 2024 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Test\Unit\Component\CommandBus\Fixture;

use Override;
use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandHandlerResultInterface;
use Communitales\Component\CommandBus\Handler\Result\SuccessResult;
use LogicException;

class BrokenCommandHandler implements CommandHandlerInterface
{
    public function __construct()
    {
        throw new LogicException('Constructor is broken for testing');
    }

    #[Override]
    public function canHandle(CommandInterface $command): bool
    {
        return false;
    }

    #[Override]
    public function handle(CommandInterface $command): CommandHandlerResultInterface
    {
        return new SuccessResult();
    }
}
