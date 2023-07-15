<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/typo3-codeception-helper".
 *
 * Copyright (C) 2023 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace EliasHaeussler\Typo3CodeceptionHelper\Tests\Fixtures\Classes;

use Codeception\ResultAggregator;
use Codeception\Test;
use Codeception\TestInterface;

/**
 * DummyTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 *
 * @internal
 */
final class DummyTest implements TestInterface
{
    public function count(): int
    {
        return 0;
    }

    public function run(): void
    {
        // Intentionally left blank.
    }

    public function getMetadata(): Test\Metadata
    {
        return new Test\Metadata();
    }

    public function getResultAggregator(): ResultAggregator
    {
        return new ResultAggregator();
    }
}