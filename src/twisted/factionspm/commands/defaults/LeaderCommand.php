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

class LeaderCommand extends FactionsCommand{

    public function __construct(){
        parent::__construct(["leader"], "Change your faction's leader", "Use: '/f leader <player>'");
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
        if(empty($args)){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());

            return;
        }
        if(!$faction->isLeader($sender->getName())){
            $sender->sendMessage(TextFormat::RED . "You must be leader to change your faction's leader");

            return;
        }
        $target = $plugin->getServer()->getPlayer($args[0]) ?? $plugin->getServer()->getOfflinePlayer($args[0]);
        if($target->getName() === $sender->getName()){
            $sender->sendMessage(TextFormat::RED . "You are already leader");

            return;
        }
        if(!$faction->isInFaction($target->getName())){
            $sender->sendMessage(TextFormat::RED . "Player is not in your faction");

            return;
        }
        $database = $plugin->getDatabase();
        $database->updateFactionLeader($faction->getId(), $target->getName());
        $database->setPlayerFaction($sender->getName(), $faction->getId(), "Moderator");
        $database->setPlayerFaction($target->getName(), $faction->getId(), "Leader");
        $faction->setLeader($target->getName());
        if($faction->isModerator($target->getName())){
            $faction->removeModerator($target->getName());
        }else{
            $faction->removeMember($target->getName());
        }
        $faction->addModerator($sender->getName());
        $faction->broadcastMessage(TextFormat::GREEN . $sender->getName() . " has given the faction's leadership to " . $target->getName());
    }
}