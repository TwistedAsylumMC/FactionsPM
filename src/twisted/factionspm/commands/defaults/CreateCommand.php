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
use function preg_match;
use function strlen;

class CreateCommand extends FactionsCommand{

    public function __construct(){
        parent::__construct(["create", "new"], "Create a new faction", "Use: '/f create <faction>'");
    }

    public function execute(CommandSender $sender, array $args, FactionsPM $plugin) : void{
        if(!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "Use command in game");

            return;
        }
        $cache = $plugin->getCache();
        if($cache->getPlayerFactionFromCache($sender) !== 0){
            $sender->sendMessage(TextFormat::RED . "You are already in a faction");

            return;
        }
        if(empty($args)){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());

            return;
        }
        if(preg_match('/^[a-z0-9]+$/i', $args[0]) <= 0){
            $sender->sendMessage(TextFormat::RED . "Faction name must be alphanumeric");

            return;
        }
        if(strlen($args[0]) < 3 || strlen($args[0]) > 15){
            $sender->sendMessage(TextFormat::RED . "Faction name can only be 3-15 characters long");

            return;
        }
        if(($faction = $plugin->getDatabase()->createFaction($args[0], $sender->getName())) === null){
            $sender->sendMessage(TextFormat::RED . "Faction already exists");

            return;
        }
        $cache->addFactionToCache($faction);
        $cache->addPlayerFactionToCache($sender, $faction->getId());
        $sender->sendMessage(TextFormat::GREEN . "Your faction has been created");
    }
}