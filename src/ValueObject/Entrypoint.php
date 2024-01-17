<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/typo3-codeception-helper".
 *
 * Copyright (C) 2023-2024 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3CodeceptionHelper\ValueObject;

use EliasHaeussler\Typo3CodeceptionHelper\Exception;
use Symfony\Component\Filesystem;

use function array_key_exists;

/**
 * Entrypoint.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class Entrypoint
{
    /**
     * @param non-empty-string $webDirectory
     * @param non-empty-string $mainEntrypoint
     * @param non-empty-string $appEntrypoint
     */
    public function __construct(
        private readonly string $webDirectory,
        private readonly string $mainEntrypoint = 'index.php',
        private readonly string $appEntrypoint = 'app.php',
    ) {}

    /**
     * @param array<string, mixed> $config
     * @param non-empty-string     $baseDirectory
     *
     * @throws Exception\ConfigIsEmpty
     * @throws Exception\ConfigIsInvalid
     */
    public static function fromConfig(array $config, string $baseDirectory): self
    {
        $webDirectory = Filesystem\Path::join($baseDirectory, self::parseConfig($config, 'web-dir'));
        $mainEntrypoint = self::parseConfig($config, 'main-entrypoint', 'index.php');
        $appEntrypoint = self::parseConfig($config, 'app-entrypoint', 'app.php');

        return new self($webDirectory, $mainEntrypoint, $appEntrypoint);
    }

    /**
     * @param array<string, mixed>  $config
     * @param non-empty-string      $name
     * @param non-empty-string|null $default
     *
     * @return non-empty-string
     *
     * @throws Exception\ConfigIsEmpty
     * @throws Exception\ConfigIsInvalid
     */
    private static function parseConfig(array $config, string $name, string $default = null): string
    {
        if (!array_key_exists($name, $config) && null !== $default) {
            return $default;
        }

        $value = $config[$name] ?? null;

        if (!is_string($value)) {
            throw new Exception\ConfigIsInvalid($name);
        }
        if ('' === $value) {
            throw new Exception\ConfigIsEmpty($name);
        }

        return $value;
    }

    /**
     * @return non-empty-string
     */
    public function getWebDirectory(): string
    {
        return $this->webDirectory;
    }

    /**
     * @return non-empty-string
     */
    public function getMainEntrypoint(): string
    {
        return Filesystem\Path::join($this->webDirectory, $this->mainEntrypoint);
    }

    /**
     * @return non-empty-string
     */
    public function getAppEntrypoint(): string
    {
        return Filesystem\Path::join($this->webDirectory, $this->appEntrypoint);
    }
}
