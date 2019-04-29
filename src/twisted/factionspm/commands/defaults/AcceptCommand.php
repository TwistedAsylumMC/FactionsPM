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

class AcceptCommand extends FactionsCommand{

    public function __construct(){
        parent::__construct(["accept", "join"], "Accept a faction invitation", "Use: '/f accept <faction>'");
    }

    public function execute(CommandSender $sender, array $args, FactionsPM $plugin) : void{
        if(!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "Use command in game");

            return;
        }
        $cache = $plugin->getCache();
        if($cache->getFactionFromCache($cache->getPlayerFactionFromCache($sender)) !== null){
            $sender->sendMessage(TextFormat::RED . "You are already in a faction");

            return;
        }
        if(empty($args)){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());

            return;
        }
        if(($faction = $cache->getFactionFromInvite($args[0], $sender->getName())) === null){
            $sender->sendMessage(TextFormat::RED . "You have not been invited to this faction");

            return;
        }
        $plugin->getDatabase()->setPlayerFaction($sender->getName(), $faction->getId(), "Member");
        $cache->addPlayerFactionToCache($sender, $faction->getId());
        $cache->removeFactionInvite($faction->getName(), $sender->getName());
        $faction->broadcastMessage(TextFormat::GREEN . $sender->getName() . " has joined the faction");
        $faction->addMember($sender->getName());
        $sender->sendMessage(TextFormat::GREEN . "You have joined " . $faction->getName());
    }
}