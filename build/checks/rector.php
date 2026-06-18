<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/typo3-codeception-helper".
 *
 * Copyright (C) 2023-2026 Elias Häußler <elias@haeussler.dev>
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

use EliasHaeussler\RectorConfig\Config\Config;
use Rector\Config\RectorConfig;
use Rector\Privatization\Rector\Property\PrivatizeFinalClassPropertyRector;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rootPath = dirname(__DIR__, 2);

    Config::create($rectorConfig, PhpVersion::PHP_82)
        ->in(
            $rootPath.'/src',
            $rootPath.'/tests',
        )
        ->not(
            $rootPath.'/c3.php',
            $rootPath.'/tests/src/Fixtures/Codeception/support/*',
        )
        ->withPHPUnit()
        ->skip(PrivatizeFinalClassPropertyRector::class, [
            $rootPath.'/src/Codeception/Extension/ApplicationEntrypointModifier.php',
        ])
        ->apply()
    ;
};
