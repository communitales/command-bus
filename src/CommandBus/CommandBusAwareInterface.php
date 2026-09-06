<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Component\CommandBus;

interface CommandBusAwareInterface
{
    public function setCommandBus(CommandBusInterface $commandBus): void;
}
