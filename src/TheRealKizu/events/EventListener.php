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

namespace TheRealKizu\events;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use TheRealKizu\LobbyCore;

class EventListener implements Listener {

    /**
     * @var LobbyCore
     */
    private $main;

    public function __construct(LobbyCore $main) {
        $this->main = $main;
        $this->main->getServer()->getPluginManager()->registerEvents($this, $main);
    }

    public function onJoin(PlayerJoinEvent $joinEvent) {
        $p = $joinEvent->getPlayer();
        $cfg = new Config($this->main->getDataFolder() . "config.yml", Config::YAML);
        if ($cfg->get("enable-joinandleave") === "false") return;
        if ($cfg->get("enable-joinandleave") === "disable") {
            $joinEvent->setJoinMessage("");
        }
        if ($cfg->get("enable-joinandleave") === "true") {
            $joinMsg = str_replace(["{name}", "&"], [$joinEvent->getPlayer()->getName(), "§"], $cfg->get("join-message"));
            $joinEvent->setJoinMessage($joinMsg);
        }
    }
}