<?php

/**
 * @copyright   Copyright (c) 2020 Communitales GmbH (http://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\CommandBus;

use RuntimeException;
use function sprintf;

/**
 * Class CanNotHandleCommandException
 */
class CanNotDispatchCommandException extends RuntimeException
{

    /**
     * @param string $class
     *
     * @return CanNotDispatchCommandException
     */
    public static function forClass(string $class): self
    {
        return new self(sprintf('The command bus has no handler for class "%s"', $class));
    }

}
