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
use pocketmine\utils\TextFormat;
use twisted\factionspm\commands\FactionsCommand;
use twisted\factionspm\FactionsPM;
use function implode;

class VersionCommand extends FactionsCommand{

    public function __construct(){
        parent::__construct(["version", "ver"], "Display the plugin version", "Use: '/f version'");
    }

    public function execute(CommandSender $sender, array $args, FactionsPM $plugin) : void{
        $description = $plugin->getDescription();
        $sender->sendMessage(TextFormat::GREEN . $description->getFullName() . " by " . implode(", ", $description->getAuthors()) . ". " . $description->getWebsite());
    }
}