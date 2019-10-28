<?php

/**
 * Copyright 2019 TheRealKizu
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace TheRealKizu;

use pocketmine\plugin\PluginBase;
use TheRealKizu\events\EventListener;

class LobbyCore extends PluginBase {

    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        $this->registerEvents();
        if (!$this->isPhar()) {
            $this->getLogger()->notice("Found plugin using folder structure! Please compile for production use!");
        }
        if ($this->getDescription()->getAuthors() !== "TheRealKizu") {
            $this->getLogger()->critical("Illegal modification of plugin! Contact @TheRealKizu on Twitter!");
            $this->getServer()->shutdown();
        }
    }

    public function onDisable() {
        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $player->transfer("mc.exynos.us.to", 19132);
        }
    }

    public function registerEvents() {
        new EventListener($this);
        $this->getLogger()->debug("Registered 1 event(s)");
    }

}