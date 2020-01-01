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

namespace TheRealKizu\LobbyCore;

use pocketmine\plugin\PluginBase;

use TheRealKizu\LobbyCore\commands\FlyCommand;
use TheRealKizu\LobbyCore\events\EventListener;

class LobbyCore extends PluginBase {

    private static $instance;

    public function onLoad() {
        self::$instance;
        $this->getLogger()->notice("LobbyCore is initializing...");
    }

    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        $this->registerEvents();
        $this->registerCommands();
        $this->getLogger()->notice("LobbyCore has been initialized! Made with love by TheRealKizu.");

        if (!$this->isPhar()) {
            $this->getLogger()->notice("Found plugin using folder structure! Please compile for production use!");
        }

        if ($this->getDescription()->getAuthors() !== "TheRealKizu" || $this->getDescription()->getName() !== "LobbyCore") {
            $this->getLogger()->emergency("Fatal error! Illegal modification/use of SkyBlockUI by TheRealKizu (TheRealKizu#3267 or @TheRealKizu)!");
            $this->getServer()->shutdown();
        }
    }

    public function onDisable() {
        $this->getLogger()->notice("LobbyCore disabled!");

        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $player->transfer("play.pixelbe.cf", 19132);
        }
    }

    public function registerEvents() {
        new EventListener($this);
        $this->getLogger()->debug("Registered 1 event(s)");
    }

    public function registerCommands() {
        $this->getServer()->getCommandMap()->registerAll("LobbyCore", [
            new FlyCommand($this),
        ]);
        $this->getLogger()->debug("Registered 1 command(s)");
    }

    public function getInstance() {
        return self::$instance;
    }

}