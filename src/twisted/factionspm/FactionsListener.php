<?php
/*
 *   _______       _     _           _ 
 *  |__   __|     (_)   | |         | |
 *     | |_      ___ ___| |_ ___  __| |
 *     | \ \ /\ / / / __| __/ _ \/ _` |
 *     | |\ V  V /| \__ \ |_  __/ (_| |
 *     |_| \_/\_/ |_|___/\__\___|\__,_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author TwistedAsylumMC
 * @link https://twistedasylummc.me
 *
*/
declare(strict_types=1);

namespace twisted\factionspm;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use function array_flip;

class FactionsListener implements Listener{

    /** @var FactionsPM */
    private $plugin;

    /** @var FactionsCache */
    private $cache;

    public function __construct(FactionsPM $plugin){
        $this->plugin = $plugin;
        $this->cache = $plugin->getCache();
    }

    public function onEntityDamage(EntityDamageEvent $event) : void{
        $entity = $event->getEntity();
        if($event instanceof EntityDamageByEntityEvent){
            $cache = $this->plugin->getCache();
            $damager = $event->getDamager();
            if($entity instanceof Player && $damager instanceof Player){
                if(($faction = $cache->getPlayerFactionFromCache($entity)) !== 0 && $faction === $cache->getPlayerFactionFromCache($damager)){
                    $event->setCancelled();
                    $damager->sendMessage(TextFormat::RED . "You cannot attack your own faction");

                    return;
                }
            }
        }
    }

    public function onPlayerJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $faction = $this->plugin->getDatabase()->getPlayerFaction($player->getName());
        if($faction !== null && $this->cache->getFactionFromCache($faction->getId()) === null){
            $this->cache->addFactionToCache($faction);
        }
        $this->cache->addPlayerFactionToCache($player, $faction === null ? 0 : $faction->getId());
    }

    public function onPlayerQuit(PlayerQuitEvent $event) : void{
        $player = $event->getPlayer();
        $factions = $this->cache->getPlayerFactionsInCache();
        $faction = $factions[$player->getId()] ?? 0;
        if($factions !== 0 && !isset(array_flip($factions)[$faction])){
            $this->cache->removeFactionFromCache($faction);
        }
        $this->cache->removePlayerFactionFromCache($player);
    }
}