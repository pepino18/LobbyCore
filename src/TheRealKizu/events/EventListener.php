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

use pocketmine\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\math\Vector3;
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
        if ($cfg->get("enable-joinandleave") === "disable") return;
        if ($cfg->get("enable-joinandleave") === "false") {
            $joinEvent->setJoinMessage("");
        }
        if ($cfg->get("enable-joinandleave") === "true") {
            $joinMsg = str_replace(["{name}", "&"], [$joinEvent->getPlayer()->getName(), "§"], $cfg->get("join-message"));
            $joinEvent->setJoinMessage($joinMsg);
        }
    }

    public function onQuit(PlayerQuitEvent $quitEvent) {
        $p = $quitEvent->getPlayer();
        $cfg = new Config($this->main->getDataFolder() . "config.yml", Config::YAML);
        if ($cfg->get("enable-joinandleave") === "disable") return;
        if ($cfg->get("enable-joinandleave") === "false") {
            $quitEvent->setQuitMessage("");
        }
        if ($cfg->get("enable-joinandleave") === "true") {
            $quitMsg = str_replace(["{name}", "&"], [$quitEvent->getPlayer()->getName(), "§"], $cfg->get("leave-message"));
            $quitEvent->setQuitMessage($quitMsg);
        }
    }

    public function onNotProxyJoin(PlayerPreLoginEvent $preLoginEvent) {
        $p = $preLoginEvent->getPlayer();
        $cfg = new Config($this->main->getDataFolder() . "config.yml", Config::YAML);
        if ($cfg->get("enable-proxyjoin") === "false") return;
        if ($cfg->get("enable-proxyjoin") === "true") {
            if ($p->getAddress() !== $cfg->get("proxy-address")) {
                $kickMsg = str_replace(["&"], ["§"], $cfg->get("proxy-kickmessage"));
                $p->kick($kickMsg);
            }
        }
    }
    
    //ProjectileHP Code by LichKing112 <3
    public function onDamage(EntityDamageEvent $e){
        if ($e->getCause() === EntityDamageByEntityEvent::CAUSE_PROJECTILE){
            $player = $e->getDamager();
            $level = $player->getLevel();
            if ($player instanceof Player){
                $health = $e->getEntity()->getHealth();
                $entity = $e->getEntity()->getNameTag();
                $player->sendMessage("§c$entity §eis on §c$health HP!");
                $level->addSound(new AnvilFallSound($player->asVector3()));
            }
        }
    }
}
