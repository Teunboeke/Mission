<?php

/**
 * Copyright (c) 2020 PJZ9n.
 *
 * This file is part of Mission.
 *
 * Mission is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Mission is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Mission. If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace pjz9n\mission\form\executor;

use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use pjz9n\mission\form\Elements;
use pjz9n\mission\language\LanguageHolder;
use pjz9n\mission\mission\executor\Executor;
use pjz9n\mission\mission\executor\Executors;
use pjz9n\mission\mission\Mission;
use pjz9n\mission\pmformsaddon\AbstractCustomForm;
use pocketmine\Player;
use ReflectionException;

class ExecutorAddTypeSelectForm extends AbstractCustomForm
{
    /** @var Mission */
    private $mission;

    /** @var string[] class string */
    private $executorTypes;

    public function __construct(Mission $mission)
    {
        $this->executorTypes = array_values(Executors::getAll());
        $options = array_map(function (string $executorClass): string {
            /** @var Executor $executorClass for ide */
            return $executorClass::getType();
        }, $this->executorTypes);
        parent::__construct(
            LanguageHolder::get()->translateString("executor.edit.add"),
            [
                new Dropdown("executorType", LanguageHolder::get()->translateString("executor.type"), $options),
                Elements::getCancellToggle(),
            ]
        );
        $this->mission = $mission;
    }

    /**
     * @throws ReflectionException
     */
    public function onSubmit(Player $player, CustomFormResponse $response): void
    {
        if ($response->getBool("cancel")) {
            $player->sendForm(new ExecutorListForm($this->mission));
            return;
        }
        $selectedExecutorType = $this->executorTypes[$response->getInt("executorType")];
        $player->sendForm(new ExecutorAddForm($this->mission, $selectedExecutorType));
    }
}
