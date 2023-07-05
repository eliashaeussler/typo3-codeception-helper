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

namespace EliasHaeussler\Typo3CodeceptionHelper\Tests\src\Helper;

use EliasHaeussler\Typo3CodeceptionHelper as Src;
use PHPUnit\Framework;
use Symfony\Component\Filesystem;

use function dirname;
use function pathinfo;
use function sys_get_temp_dir;

/**
 * PathHelperTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Helper\PathHelper::class)]
final class PathHelperTest extends Framework\TestCase
{
    /**
     * @var non-empty-string
     */
    private string $testDirectory;
    private Filesystem\Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->testDirectory = sys_get_temp_dir().'/typo3-codeception-helper-tests';
        $this->filesystem = new Filesystem\Filesystem();

        $this->filesystem->remove($this->testDirectory);
    }

    #[Framework\Attributes\Test]
    public function findUniqueTemporaryFilenameReturnsUniqueFilenameWithinGivenDirectory(): void
    {
        $actual = Src\Helper\PathHelper::findUniqueTemporaryFilename(__DIR__, 'php');

        self::assertFileDoesNotExist($actual);
    }

    #[Framework\Attributes\Test]
    public function findUniqueTemporaryFilenameCanHandleEmptyFileExtensions(): void
    {
        $actual = Src\Helper\PathHelper::findUniqueTemporaryFilename(__DIR__);

        self::assertSame('', pathinfo($actual, PATHINFO_EXTENSION));
    }

    #[Framework\Attributes\Test]
    public function findTemporaryFilesReturnsEmptyArrayIfGivenDirectoryDoesNotExist(): void
    {
        self::assertSame([], Src\Helper\PathHelper::findTemporaryFiles($this->testDirectory));
    }

    #[Framework\Attributes\Test]
    public function findTemporaryFilesReturnsFinderForAllTemporaryFilesWithinGivenDirectory(): void
    {
        $this->filesystem->mkdir($this->testDirectory);

        self::assertCount(0, Src\Helper\PathHelper::findTemporaryFiles($this->testDirectory));

        $this->filesystem->dumpFile($this->testDirectory.'/_codeception_helper_include_foo.txt', 'foo');
        $this->filesystem->dumpFile($this->testDirectory.'/_codeception_helper_include_baz.txt', 'baz');

        self::assertCount(2, Src\Helper\PathHelper::findTemporaryFiles($this->testDirectory));
    }

    #[Framework\Attributes\Test]
    public function getVendorDirectoryReturnsVendorDirectory(): void
    {
        self::assertSame(
            dirname(__DIR__, 3).'/vendor',
            Src\Helper\PathHelper::getVendorDirectory(),
        );
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->testDirectory);
    }
}
