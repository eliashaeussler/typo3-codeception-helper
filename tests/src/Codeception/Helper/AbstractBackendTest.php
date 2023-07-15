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

namespace EliasHaeussler\Typo3CodeceptionHelper\Tests\Codeception\Helper;

use EliasHaeussler\Typo3CodeceptionHelper as Src;
use EliasHaeussler\Typo3CodeceptionHelper\Tests;
use PHPUnit\Framework;

use function count;

/**
 * AbstractBackendTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class AbstractBackendTest extends Framework\TestCase
{
    private Tests\Fixtures\Classes\DummyScenario $scenario;
    private Tests\Fixtures\Classes\DummyBackend $subject;

    protected function setUp(): void
    {
        $this->scenario = new Tests\Fixtures\Classes\DummyScenario();
        $this->subject = new Tests\Fixtures\Classes\DummyBackend(
            new Tests\Fixtures\Codeception\support\AcceptanceTester($this->scenario),
        );
    }

    #[Framework\Attributes\Test]
    public function constructorThrowsExceptionIfWebDriverModuleIsNotEnabled(): void
    {
        $tester = new Tests\Fixtures\Codeception\support\AcceptanceBrokenTester($this->scenario);

        $this->expectExceptionObject(new Src\Exception\ModuleIsNotEnabled('WebDriver'));

        new Tests\Fixtures\Classes\DummyBackend($tester);
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

        $this->subject->login();

        self::assertCount(count($expected), $this->scenario->executedSteps);

        foreach ($expected as $i => $action) {
            self::assertSame($action, $this->scenario->executedSteps[$i]->getAction());
        }
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

        self::assertCount(count($expected), $this->scenario->executedSteps);

        foreach ($expected as $i => $action) {
            self::assertSame($action, $this->scenario->executedSteps[$i]->getAction());
        }
    }
}
