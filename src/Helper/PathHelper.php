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

namespace EliasHaeussler\Typo3CodeceptionHelper\Helper;

use Composer\InstalledVersions;
use EliasHaeussler\Typo3CodeceptionHelper\Exception;
use ReflectionClass;

use function dirname;

/**
 * PathHelper.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class PathHelper
{
    /**
     * @throws Exception\VendorDirectoryCannotBeDetermined
     */
    public static function getVendorDirectory(): string
    {
        $reflectionClass = new ReflectionClass(InstalledVersions::class);
        $filename = $reflectionClass->getFileName();

        if (false === $filename) {
            throw new Exception\VendorDirectoryCannotBeDetermined();
        }

        return dirname($filename, 2);
    }
}
