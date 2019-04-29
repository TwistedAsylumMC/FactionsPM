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

namespace twisted\factionspm\commands\defaults;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use twisted\factionspm\commands\FactionsCommand;
use twisted\factionspm\FactionsPM;

class DisbandCommand extends FactionsCommand{

    public function __construct(){
        parent::__construct(["disband", "delete"], "Disband your faction", "Use: '/f disband'");
    }

    public function execute(CommandSender $sender, array $args, FactionsPM $plugin) : void{
        if(!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "Use command in game");

            return;
        }
        $cache = $plugin->getCache();
        if(($faction = $cache->getFactionFromCache($cache->getPlayerFactionFromCache($sender))) === null){
            $sender->sendMessage(TextFormat::RED . "You are not in a faction");

            return;
        }
        if(!$faction->isLeader($sender->getName())){
            $sender->sendMessage(TextFormat::RED . "Only leaders can disband their faction");

            return;
        }
        $plugin->getDatabase()->deleteFaction($faction->getId());
        $cache->removeFactionFromCache($faction->getId());
        foreach($faction->getOnlineMembers() as $member){
            if(($member = $plugin->getServer()->getPlayer($member)) !== null){
                $cache->removePlayerFactionFromCache($member);
            }
        }
        $faction->broadcastMessage(TextFormat::RED . $sender->getName() . " has disbanded the faction");
    }
}