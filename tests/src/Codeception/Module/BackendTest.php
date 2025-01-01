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

namespace EliasHaeussler\Typo3CodeceptionHelper\Tests\Codeception\Module;

use Codeception\Lib;
use EliasHaeussler\Typo3CodeceptionHelper as Src;
use EliasHaeussler\Typo3CodeceptionHelper\Tests;
use PHPUnit\Framework;

use function count;

/**
 * BackendTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class BackendTest extends Framework\TestCase
{
    private Tests\Fixtures\Classes\DummyModule $webDriver;
    private Src\Codeception\Module\Backend $subject;

    protected function setUp(): void
    {
        $moduleContainer = new Lib\ModuleContainer(new Lib\Di(), []);

        $this->webDriver = new Tests\Fixtures\Classes\DummyModule($moduleContainer);
        $this->subject = new Src\Codeception\Module\Backend($moduleContainer);

        $moduleContainer->mock('WebDriver', $this->webDriver);
    }

    #[Framework\Attributes\Test]
    public function loginPerformsBackendLogin(): void
    {
        $expected = [
            'amOnPage',
            'waitForElementVisible',
            'waitForElementVisible',
            'fillField',
            'fillField',
            'click',
            'waitForElementNotVisible',
            'seeCookie',
        ];

        $this->subject->login('admin', 'password');

        self::assertCount(count($expected), $this->webDriver->executedSteps);

        foreach ($expected as $i => $action) {
            self::assertSame($action, $this->webDriver->executedSteps[$i]['step']);
        }
    }

    #[Framework\Attributes\Test]
    public function loginAsThrowsExceptionIfGivenUserIsNotConfigured(): void
    {
        $this->expectExceptionObject(
            new Framework\AssertionFailedError('A user with username "foo" is not configured.'),
        );

        $this->subject->loginAs('foo');
    }

    #[Framework\Attributes\Test]
    public function loginAsPerformsBackendLoginForGivenUser(): void
    {
        $this->subject->loginAs('admin');

        $stepsWithoutPassword = $this->webDriver->executedSteps;

        $this->webDriver->executedSteps = [];

        $this->subject->login('admin', 'password');

        $stepsWithPassword = $this->webDriver->executedSteps;

        self::assertEquals($stepsWithoutPassword, $stepsWithPassword);
    }

    #[Framework\Attributes\Test]
    public function openModuleOpensBackendModule(): void
    {
        $expected = [
            'waitForElementClickable',
            'click',
            'switchToIFrame',
        ];

        $this->subject->openModule('foo');

        self::assertCount(count($expected), $this->webDriver->executedSteps);

        foreach ($expected as $i => $action) {
            self::assertSame($action, $this->webDriver->executedSteps[$i]['step']);
        }
    }
}
