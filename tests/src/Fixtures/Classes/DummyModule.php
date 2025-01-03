<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/typo3-codeception-helper".
 *
 * Copyright (C) 2023-2025 Elias Häußler <elias@haeussler.dev>
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

use Codeception\Module;

/**
 * DummyModule.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 *
 * @internal
 */
final class DummyModule extends Module
{
    /**
     * @var list<array{step: string, arguments: array<mixed>}>
     */
    public array $executedSteps = [];

    /**
     * @param array<mixed> $arguments
     */
    public function __call(string $name, array $arguments): void
    {
        $this->executedSteps[] = [
            'step' => $name,
            'arguments' => $arguments,
        ];
    }
}
