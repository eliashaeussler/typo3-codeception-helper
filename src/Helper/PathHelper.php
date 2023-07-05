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

namespace EliasHaeussler\Typo3CodeceptionHelper\Helper;

use Symfony\Component\Filesystem;

use function file_exists;
use function pathinfo;
use function sprintf;
use function uniqid;

/**
 * PathHelper.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class PathHelper
{
    /**
     * @param non-empty-string $base
     * @param non-empty-string $directory
     *
     * @return non-empty-string
     */
    public static function findUniqueFilename(string $base, string $directory): string
    {
        $basename = pathinfo($base, PATHINFO_BASENAME);
        $extension = pathinfo($base, PATHINFO_EXTENSION);

        do {
            $possibleFilename = sprintf('%s.%s', uniqid($basename.'.'), $extension);
        } while (file_exists(Filesystem\Path::join($directory, $possibleFilename)));

        return $possibleFilename;
    }
}
