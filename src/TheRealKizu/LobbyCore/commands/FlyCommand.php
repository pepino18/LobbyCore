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

namespace TheRealKizu\LobbyCore\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

use TheRealKizu\LobbyCore\LobbyCore;

class FlyCommand extends PluginCommand {

    /**
     * @var LobbyCore
     */
    private $main;

    public function __construct(LobbyCore $main) {
        parent::__construct("fly", $main);
        $this->main = $main;
        $this->setDescription("Enable/Disable Fly Status!");
        $this->setPermission("lc.fly.command");
        $this->setUsage("/fly");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (!$this->testPermission($sender)) {
            return true;
        }

        if ($sender instanceof Player) {
            if ($sender->isFlying()) {
                $sender->setAllowFlight(false);
                $sender->setFlying(false);
                $sender->sendMessage(TextFormat::GREEN . "Fly mode disabled");
            } else {
                $sender->setAllowFlight(true);
                $sender->setFlying(true);
                $sender->sendMessage(TextFormat::GREEN . "Fly mode enabled");
            }
        }
        return true;
    }
}