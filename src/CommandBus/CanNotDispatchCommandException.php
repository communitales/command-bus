<?php
/**
 * @copyright   Copyright (c) 2020 - 2023 Communitales GmbH (http://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\CommandBus;

use RuntimeException;
use function sprintf;

class CanNotDispatchCommandException extends RuntimeException
{
    public static function forClass(string $class): self
    {
        return new self(sprintf('The command bus has no handler for class "%s"', $class));
    }
}
