<?php

/**
 * @copyright   Copyright (c) 2020 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\CommandBus\Handler\Result;

use RuntimeException;
use Throwable;

/**
 * An exception representing an Result. In some cases this may be useful.
 */
class CommandResultException extends RuntimeException
{
    /**
     * @var CommandHandlerResultInterface
     */
    private CommandHandlerResultInterface $commandResult;

    /**
     * @param CommandHandlerResultInterface $commandResult
     * @param string                        $message
     * @param int                           $code
     * @param Throwable|null                $previous
     */
    public function __construct(
        CommandHandlerResultInterface $commandResult,
        $message = '',
        $code = 0,
        Throwable $previous = null
    ) {
        $this->commandResult = $commandResult;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return CommandHandlerResultInterface
     */
    public function getCommandResult(): CommandHandlerResultInterface
    {
        return $this->commandResult;
    }

}
