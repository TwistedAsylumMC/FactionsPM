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
use function implode;
use function strlen;

class DescriptionCommand extends FactionsCommand{

    public function __construct(){
        parent::__construct(["description", "desc"], "Change your faction's description", "Use: '/f description <description>'");
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
        $description = implode(" ", $args);
        if(strlen($description) > 100){
            $sender->sendMessage(TextFormat::RED . "Description cannot be more than 100 characters");

            return;
        }
        $plugin->getDatabase()->updateFactionDescription($faction->getId(), $description);
        $faction->setDescription($description);
        $faction->broadcastMessage(TextFormat::GREEN . $sender->getName() . " has updated the faction's description to '" . $description . "'");
    }
}