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

namespace TheRealKizu\LobbyCore\events;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use TheRealKizu\LobbyCore\API;
use TheRealKizu\LobbyCore\LobbyCore;

class EventListener implements Listener {

    /**
     * @var LobbyCore
     */
    private $main;

    public function __construct(LobbyCore $main) {
        $this->main = $main;
        $this->main->getServer()->getPluginManager()->registerEvents($this, $main);
    }

    /**
     * @param PlayerJoinEvent $joinEvent
     */
    public function onJoin(PlayerJoinEvent $joinEvent) {
        $p = $joinEvent->getPlayer();
        //Join Message
        $cfg = new Config($this->main->getDataFolder() . "config.yml", Config::YAML);
        if ($cfg->get("enable-joinandleave") === "disable") return;
        if ($cfg->get("enable-joinandleave") === "false") {
            $joinEvent->setJoinMessage("");
        }
        if ($cfg->get("enable-joinandleave") === "true") {
            $joinMsg = str_replace(["{name}"], [$joinEvent->getPlayer()->getName()], $cfg->get("join-message"));
            $joinEvent->setJoinMessage(API::translateColors($joinMsg));
        }

        //Lobby Items
        $inv = $p->getInventory();

        //TODO: Understand this part!
        $navigator = Item::get(Item::COMPASS);
        $navigator->setCustomName("§r§aNavigator");

        $inv->setItem(4, $navigator);
    }

    /**
     * @param PlayerQuitEvent $quitEvent
     */
    public function onQuit(PlayerQuitEvent $quitEvent) {
        $p = $quitEvent->getPlayer();
        $cfg = new Config($this->main->getDataFolder() . "config.yml", Config::YAML);
        if ($cfg->get("enable-joinandleave") === "disable") return;
        if ($cfg->get("enable-joinandleave") === "false") {
            $quitEvent->setQuitMessage("");
        }
        if ($cfg->get("enable-joinandleave") === "true") {
            $quitMsg = str_replace(["{name}"], [$quitEvent->getPlayer()->getName()], $cfg->get("leave-message"));
            $quitEvent->setQuitMessage(API::translateColors($quitMsg));
        }
    }

    /**
     * @param PlayerPreLoginEvent $preLoginEvent
     */
    public function onNotProxyJoin(PlayerPreLoginEvent $preLoginEvent) {
        $p = $preLoginEvent->getPlayer();
        $cfg = new Config($this->main->getDataFolder() . "config.yml", Config::YAML);
        if ($cfg->get("enable-proxyjoin") === "false") return;
        if ($cfg->get("enable-proxyjoin") === "true") {
            if ($p->getAddress() !== $cfg->get("proxy-address")) {
                //$kickMsg = str_replace(["&"], ["§"], $cfg->get("proxy-kickmessage"));
                $p->kick(API::translateColors($cfg->get("proxy-kickmessage")));
            }
        }
    }
    
    //ProjectileHP Code by LichKing112 <3
    /**
     * @param EntityDamageEvent $e
     */
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

    //Interact Event aka The Largest Event :P
    /**
     * @param PlayerInteractEvent $interactEvent
     */
    public function onInteract(PlayerInteractEvent $interactEvent) {
        $p = $interactEvent->getPlayer();
        $item = $interactEvent->getItem();
        $itemname = $item->getName();
        if ($itemname === "§r§aNavigator") {
            $p->sendMessage(TextFormat::GREEN . "This feature is under development!");
        }
    }

    // ----- [LOBBY PROTECTION] -----
    /**
     * @param BlockBreakEvent $blockBreakEvent
     */
    public function onBlockBreak(BlockBreakEvent $blockBreakEvent) {
        $p = $blockBreakEvent->getPlayer();
        if ($p->getLevel() === $this->main->getServer()->getDefaultLevel()) {
            $blockBreakEvent->setCancelled(true);
            if ($p->hasPermission("lc.break") || $p->isOp()) {
                $blockBreakEvent->setCancelled(false);
            }
        }
    }

    /**
     * @param BlockPlaceEvent $blockPlaceEvent
     */
    public function onBlockPlace(BlockPlaceEvent $blockPlaceEvent) {
        $p = $blockPlaceEvent->getPlayer();
        if ($p->getLevel() === $this->main->getServer()->getDefaultLevel()) {
            $blockPlaceEvent->setCancelled(true);
            if ($p->hasPermission("lc.place") || $p->isOp()) {
                $blockPlaceEvent->setCancelled(false);
            }
        }
    }
}
