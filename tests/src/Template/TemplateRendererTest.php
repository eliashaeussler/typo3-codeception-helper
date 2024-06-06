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

namespace EliasHaeussler\Typo3CodeceptionHelper\Tests\Template;

use EliasHaeussler\Typo3CodeceptionHelper as Src;
use PHPUnit\Framework;

use function dirname;
use function ob_get_clean;
use function ob_start;
use function sys_get_temp_dir;
use function tempnam;
use function trim;
use function unlink;

/**
 * TemplateRendererTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Template\TemplateRenderer::class)]
final class TemplateRendererTest extends Framework\TestCase
{
    private string $templateDirectory;
    private Src\Template\TemplateRenderer $subject;

    protected function setUp(): void
    {
        $this->templateDirectory = dirname(__DIR__).'/Fixtures/Templates';
        $this->subject = new Src\Template\TemplateRenderer($this->templateDirectory);
    }

    #[Framework\Attributes\Test]
    public function renderThrowsExceptionIfTemplateDoesNotExist(): void
    {
        $templateFile = $this->templateDirectory.'/foo.php.tpl';

        $this->expectExceptionObject(new Src\Exception\TemplateFileIsMissing($templateFile));

        $this->subject->render('foo.php.tpl');
    }

    #[Framework\Attributes\Test]
    public function renderReturnsRenderedTemplate(): void
    {
        $actual = $this->subject->render('hello-world.php.tpl', [
            'helloWorld' => 'Hello World!',
        ]);

        self::assertStringContainsString('echo "Hello World!";', $actual);
    }

    #[Framework\Attributes\Test]
    public function dumpRendersTemplateAndDumpsRenderedTemplateToGivenFile(): void
    {
        $targetFile = tempnam(sys_get_temp_dir(), 'typo3_codeception_handler_');

        self::assertIsString($targetFile);

        // Remove file as it's dumped during testing
        unlink($targetFile);

        $this->subject->dump('hello-world.php.tpl', $targetFile, [
            'helloWorld' => 'Hello World!',
        ]);

        self::assertFileExists($targetFile);

        ob_start();

        require $targetFile;

        self::assertSame('Hello World!', trim((string) ob_get_clean()));

        unlink($targetFile);
    }
}
