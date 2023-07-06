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

namespace EliasHaeussler\Typo3CodeceptionHelper\Codeception\Extension;

use Codeception\Configuration;
use Codeception\Events;
use Codeception\Extension;
use EliasHaeussler\Typo3CodeceptionHelper\Exception;
use EliasHaeussler\Typo3CodeceptionHelper\Helper;
use EliasHaeussler\Typo3CodeceptionHelper\Template;
use EliasHaeussler\Typo3CodeceptionHelper\ValueObject;
use Symfony\Component\Filesystem;

use function rtrim;

/**
 * ApplicationEntrypointModifier.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class ApplicationEntrypointModifier extends Extension
{
    /**
     * @var array<string, string>
     */
    protected static array $events = [
        Events::SUITE_BEFORE => 'beforeSuite',
    ];

    /**
     * @var array{entrypoints: list<array<string, mixed>>}
     */
    protected array $config = [
        'entrypoints' => [],
    ];

    /**
     * @var list<ValueObject\Entrypoint>
     */
    private array $entrypoints = [];

    /**
     * @param array<string, mixed> $config
     * @param array<string, mixed> $options
     */
    public function __construct(
        array $config,
        array $options,
        private readonly Template\TemplateRenderer $templateRenderer = new Template\TemplateRenderer(),
        private readonly Filesystem\Filesystem $filesystem = new Filesystem\Filesystem(),
    ) {
        parent::__construct($config, $options);
    }

    /**
     * @throws Exception\ConfigIsEmpty
     * @throws Exception\ConfigIsInvalid
     */
    public function _initialize(): void
    {
        foreach ($this->config['entrypoints'] as $entrypoint) {
            $this->entrypoints[] = ValueObject\Entrypoint::fromConfig($entrypoint, Configuration::projectDir());
        }
    }

    public function beforeSuite(): void
    {
        foreach ($this->entrypoints as $entrypoint) {
            if ($this->entrypointNeedsUpdate($entrypoint)) {
                $this->createEntrypoint($entrypoint, true);
            }
        }
    }

    /**
     * @return list<ValueObject\Entrypoint>
     */
    public function getEntrypoints(): array
    {
        return $this->entrypoints;
    }

    private function entrypointNeedsUpdate(ValueObject\Entrypoint $entrypoint): bool
    {
        if (!$this->filesystem->exists($entrypoint->getAppEntrypoint())) {
            return true;
        }

        return sha1_file($entrypoint->getMainEntrypoint()) !== sha1($this->createEntrypoint($entrypoint));
    }

    private function createEntrypoint(ValueObject\Entrypoint $entrypoint, bool $dump = false): string
    {
        $templateFile = 'entrypoint.php.tpl';
        $variables = [
            'projectDir' => rtrim(Configuration::projectDir(), DIRECTORY_SEPARATOR),
            'vendorDir' => Helper\PathHelper::getVendorDirectory(),
            'appEntrypoint' => $entrypoint->getAppEntrypoint(),
        ];

        if (!$dump) {
            return $this->templateRenderer->render($templateFile, $variables);
        }

        $this->filesystem->rename($entrypoint->getMainEntrypoint(), $entrypoint->getAppEntrypoint(), true);

        return $this->templateRenderer->dump($templateFile, $entrypoint->getMainEntrypoint(), $variables);
    }
}
