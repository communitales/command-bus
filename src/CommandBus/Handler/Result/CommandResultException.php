<?php
/**
 * @copyright   Copyright (c) 2020 - 2023 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\CommandBus\Handler\Result;

use RuntimeException;
use Throwable;

/**
 * An exception representing a result. In some cases this may be useful.
 */
class CommandResultException extends RuntimeException
{
    private CommandHandlerResultInterface $commandResult;

    public function __construct(
        CommandHandlerResultInterface $commandResult,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        $this->commandResult = $commandResult;
        parent::__construct($message, $code, $previous);
    }

    public function getCommandResult(): CommandHandlerResultInterface
    {
        return $this->commandResult;
    }

}
