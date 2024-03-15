<?php
/**
 * @copyright   Copyright (c) 2020 - 2024 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
