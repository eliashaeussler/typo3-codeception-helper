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

namespace EliasHaeussler\Typo3CodeceptionHelper\Tests\ValueObject;

use EliasHaeussler\Typo3CodeceptionHelper as Src;
use PHPUnit\Framework;

/**
 * Entrypoint.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\ValueObject\Entrypoint::class)]
final class EntrypointTest extends Framework\TestCase
{
    private Src\ValueObject\Entrypoint $subject;

    protected function setUp(): void
    {
        $this->subject = new Src\ValueObject\Entrypoint('public');
    }

    #[Framework\Attributes\Test]
    public function fromConfigThrowsExceptionIfWebDirectoryIsNotConfigured(): void
    {
        $this->expectExceptionObject(new Src\Exception\ConfigIsInvalid('web-dir'));

        Src\ValueObject\Entrypoint::fromConfig([], __DIR__);
    }

    #[Framework\Attributes\Test]
    public function fromConfigThrowsExceptionIfWebDirectoryIsEmpty(): void
    {
        $this->expectExceptionObject(new Src\Exception\ConfigIsEmpty('web-dir'));

        Src\ValueObject\Entrypoint::fromConfig(
            [
                'web-dir' => '',
            ],
            __DIR__,
        );
    }

    #[Framework\Attributes\Test]
    public function fromConfigThrowsExceptionIfMainEntrypointIsNotConfigured(): void
    {
        $this->expectExceptionObject(new Src\Exception\ConfigIsInvalid('main-entrypoint'));

        Src\ValueObject\Entrypoint::fromConfig(
            [
                'web-dir' => 'public',
                'main-entrypoint' => null,
            ],
            __DIR__,
        );
    }

    #[Framework\Attributes\Test]
    public function fromConfigThrowsExceptionIfMainEntrypointIsEmpty(): void
    {
        $this->expectExceptionObject(new Src\Exception\ConfigIsEmpty('main-entrypoint'));

        Src\ValueObject\Entrypoint::fromConfig(
            [
                'web-dir' => 'public',
                'main-entrypoint' => '',
            ],
            __DIR__,
        );
    }

    #[Framework\Attributes\Test]
    public function fromConfigThrowsExceptionIfAppEntrypointIsNotConfigured(): void
    {
        $this->expectExceptionObject(new Src\Exception\ConfigIsInvalid('app-entrypoint'));

        Src\ValueObject\Entrypoint::fromConfig(
            [
                'web-dir' => 'public',
                'main-entrypoint' => 'index.php',
                'app-entrypoint' => null,
            ],
            __DIR__,
        );
    }

    #[Framework\Attributes\Test]
    public function fromConfigThrowsExceptionIfAppEntrypointIsEmpty(): void
    {
        $this->expectExceptionObject(new Src\Exception\ConfigIsEmpty('app-entrypoint'));

        Src\ValueObject\Entrypoint::fromConfig(
            [
                'web-dir' => 'public',
                'main-entrypoint' => 'index.php',
                'app-entrypoint' => '',
            ],
            __DIR__,
        );
    }

    #[Framework\Attributes\Test]
    public function fromConfigUsesDefaultValuesForEntrypoints(): void
    {
        $expected = new Src\ValueObject\Entrypoint(
            __DIR__.'/public',
            __DIR__.'/public/index.php',
            __DIR__.'/public/app.php',
        );

        self::assertEquals(
            $expected,
            Src\ValueObject\Entrypoint::fromConfig(
                [
                    'web-dir' => 'public',
                ],
                __DIR__,
            ),
        );
    }

    #[Framework\Attributes\Test]
    public function fromConfigReturnsEntrypointFromGivenConfig(): void
    {
        $expected = new Src\ValueObject\Entrypoint(
            __DIR__.'/public',
            __DIR__.'/public/index.php',
            __DIR__.'/public/app.php',
        );

        self::assertEquals(
            $expected,
            Src\ValueObject\Entrypoint::fromConfig(
                [
                    'web-dir' => 'public',
                    'main-entrypoint' => 'index.php',
                    'app-entrypoint' => 'app.php',
                ],
                __DIR__,
            ),
        );
    }

    #[Framework\Attributes\Test]
    public function getWebDirectoryReturnsWebDirectory(): void
    {
        self::assertSame('public', $this->subject->getWebDirectory());
    }

    #[Framework\Attributes\Test]
    public function getMainEntrypointReturnsMainEntrypoint(): void
    {
        self::assertSame('index.php', $this->subject->getMainEntrypoint());
    }

    #[Framework\Attributes\Test]
    public function getAppEntrypointReturnsAppEntrypoint(): void
    {
        self::assertSame('app.php', $this->subject->getAppEntrypoint());
    }
}
