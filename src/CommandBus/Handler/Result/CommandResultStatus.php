<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Component\CommandBus\Handler\Result;

enum CommandResultStatus: string
{
    case Success = 'success';
    case Error = 'error';
    case Failed = 'failed';
}
