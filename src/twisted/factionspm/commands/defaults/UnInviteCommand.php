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

class UnInviteCommand extends FactionsCommand{

    public function __construct(){
        parent::__construct(["uninvite"], "Uninvite a player from your faction", "Use: '/f uninvite <player>'");
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
        if(!$faction->isLeader($sender->getName()) && !$faction->isModerator($sender->getName())){
            $sender->sendMessage(TextFormat::RED . "You must be a faction moderator to use this command");

            return;
        }
        if(empty($args)){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());

            return;
        }
        if(($target = $plugin->getServer()->getPlayer($args[0])) === null){
            $sender->sendMessage(TextFormat::RED . "Player not found");

            return;
        }
        if(!$cache->hasFactionInvite($faction->getName(), $target->getName())){
            $sender->sendMessage(TextFormat::RED . $target->getName() . " has not been invited to your faction");

            return;
        }
        $cache->removeFactionInvite($faction->getName(), $target->getName());
        $faction->broadcastMessage(TextFormat::RED . $sender->getName() . " has uninvited " . $target->getName() . " from your faction");
        $target->sendMessage(TextFormat::RED . $sender->getName() . " has uninvited you from " . $faction->getName());
    }
}