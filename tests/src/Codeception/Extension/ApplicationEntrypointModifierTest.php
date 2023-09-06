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

namespace EliasHaeussler\Typo3CodeceptionHelper\Tests\Codeception\Extension;

use Codeception\Configuration;
use EliasHaeussler\Typo3CodeceptionHelper as Src;
use PHPUnit\Framework;
use Symfony\Component\Filesystem;
use Symfony\Component\Finder;

use function dirname;
use function sleep;

/**
 * ApplicationEntrypointModifierTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Codeception\Extension\ApplicationEntrypointModifier::class)]
final class ApplicationEntrypointModifierTest extends Framework\TestCase
{
    /**
     * @var non-empty-string
     */
    private string $publicDirectory;
    private Filesystem\Filesystem $filesystem;
    private Src\Codeception\Extension\ApplicationEntrypointModifier $subject;

    protected function setUp(): void
    {
        Configuration::config(
            dirname(__DIR__, 2).'/Fixtures/Codeception/codeception.yml',
        );

        $this->publicDirectory = dirname(__DIR__, 2).'/Fixtures/Codeception/public';
        $this->filesystem = new Filesystem\Filesystem();
        $this->subject = new Src\Codeception\Extension\ApplicationEntrypointModifier(
            [
                'entrypoints' => [
                    [
                        'web-dir' => 'public',
                    ],
                ],
            ],
            [],
        );

        // Create public directory
        $this->filesystem->mirror(
            dirname(__DIR__, 2).'/Fixtures/Codeception/private',
            $this->publicDirectory,
        );
    }

    #[Framework\Attributes\Test]
    public function constructorInitializesEntrypoints(): void
    {
        $expected = new Src\ValueObject\Entrypoint(
            $this->publicDirectory,
            'index.php',
            'app.php',
        );

        self::assertEquals([$expected], $this->subject->getEntrypoints());
    }

    #[Framework\Attributes\Test]
    public function beforeSuiteCreatesEntrypointIfItDoesNotExist(): void
    {
        $this->subject->beforeSuite();

        self::assertCount(2, $this->createFinder());
    }

    #[Framework\Attributes\Test]
    public function beforeSuiteDoesNothingIfEntrypointAlreadyExistsAndIsIdentical(): void
    {
        $this->subject->beforeSuite();

        $lastMod = $this->getLastModificationTimes();

        sleep(1);

        $this->subject->beforeSuite();

        self::assertSame(
            $lastMod,
            $this->getLastModificationTimes(),
        );
    }

    #[Framework\Attributes\Test]
    public function beforeSuiteRecreatesEntrypointIfContentsHaveChangedInTheMeantime(): void
    {
        $this->subject->beforeSuite();
        $this->filesystem->dumpFile($this->publicDirectory.'/index.php', 'foo');

        $lastMod = $this->getLastModificationTimes();

        sleep(1);

        $this->subject->beforeSuite();

        self::assertNotSame(
            $lastMod,
            $this->getLastModificationTimes(),
        );
    }

    protected function tearDown(): void
    {
        // Remove public directory
        $this->filesystem->remove($this->publicDirectory);
    }

    /**
     * @return array<string, int|false>
     */
    private function getLastModificationTimes(): array
    {
        $lastMod = [];

        foreach ($this->createFinder() as $file) {
            $lastMod[$file->getFilename()] = $file->getMTime();
        }

        return $lastMod;
    }

    private function createFinder(): Finder\Finder
    {
        return Finder\Finder::create()->in($this->publicDirectory);
    }
}
