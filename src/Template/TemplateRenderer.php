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

namespace EliasHaeussler\Typo3CodeceptionHelper\Template;

use EliasHaeussler\Typo3CodeceptionHelper\Exception;
use Symfony\Component\Filesystem;

use function dirname;
use function file_get_contents;
use function is_string;
use function strtr;

/**
 * TemplateRenderer.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class TemplateRenderer
{
    /**
     * @var non-empty-string
     */
    private readonly string $templateDirectory;

    /**
     * @param non-empty-string|null $templateDirectory
     */
    public function __construct(
        ?string $templateDirectory = null,
        private readonly Filesystem\Filesystem $filesystem = new Filesystem\Filesystem(),
    ) {
        $this->templateDirectory = $templateDirectory ?? dirname(__DIR__, 2).'/templates';
    }

    /**
     * @param non-empty-string     $templateFile
     * @param array<string, mixed> $variables
     *
     * @return non-empty-string
     *
     * @throws Exception\TemplateFileIsInvalid
     * @throws Exception\TemplateFileIsMissing
     */
    public function render(string $templateFile, array $variables = []): string
    {
        if (!$this->filesystem->isAbsolutePath($templateFile)) {
            $templateFile = Filesystem\Path::join($this->templateDirectory, $templateFile);
        }

        if (!$this->filesystem->exists($templateFile)) {
            throw new Exception\TemplateFileIsMissing($templateFile);
        }

        $template = file_get_contents($templateFile);

        if (!is_string($template)) {
            throw new Exception\TemplateFileIsInvalid($templateFile);
        }

        /** @var non-empty-string $content */
        $content = strtr($template, $this->buildPlaceholderVariables($variables));

        return $content;
    }

    /**
     * @param non-empty-string     $templateFile
     * @param non-empty-string     $targetFile
     * @param array<string, mixed> $variables
     *
     * @return non-empty-string
     */
    public function dump(string $templateFile, string $targetFile, array $variables = []): string
    {
        $contents = $this->render($templateFile, $variables);

        $this->filesystem->dumpFile($targetFile, $contents);

        return $contents;
    }

    /**
     * @param array<string, mixed> $variables
     *
     * @return array<string, mixed>
     */
    private function buildPlaceholderVariables(array $variables): array
    {
        $placeholders = [];

        foreach ($variables as $key => $value) {
            $placeholders['{$'.$key.'}'] = $value;
        }

        return $placeholders;
    }
}
