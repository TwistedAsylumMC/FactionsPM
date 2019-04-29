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
use function array_chunk;
use function array_pop;
use function count;
use function explode;
use function implode;
use function is_numeric;
use function ksort;
use function min;
use function strtolower;
use const SORT_FLAG_CASE;
use const SORT_NATURAL;

class HelpCommand extends FactionsCommand{

    public function __construct(){
        parent::__construct(["help"], "Get a list of faction commands", "Use: '/f help [command|page]'");
    }

    public function execute(CommandSender $sender, array $args, FactionsPM $plugin) : void{
        if(count($args) === 0){
            $command = "";
            $pageNumber = 1;
        }elseif(is_numeric($args[count($args) - 1])){
            $pageNumber = (int) array_pop($args);
            if($pageNumber <= 0){
                $pageNumber = 1;
            }
            $command = implode(" ", $args);
        }else{
            $command = implode(" ", $args);
            $pageNumber = 1;
        }

        $pageHeight = $sender->getScreenLineHeight();

        if($command === ""){
            /** @var FactionsCommand[][] $commands */
            $commands = $plugin->getCommandMap()->getCommands();
            ksort($commands, SORT_NATURAL | SORT_FLAG_CASE);
            $commands = array_chunk($commands, $pageHeight);
            $pageNumber = (int) min(count($commands), $pageNumber);
            if($pageNumber < 1){
                $pageNumber = 1;
            }
            $sender->sendMessage(TextFormat::GREEN . "Show faction help for page " . $pageNumber . "/" . count($commands));
            if(isset($commands[$pageNumber - 1])){
                foreach($commands[$pageNumber - 1] as $command){
                    $sender->sendMessage(TextFormat::GREEN . "/" . $command->getName() . ": " . $command->getDescription());
                }
            }

            return;
        }
        if(($cmd = $plugin->getCommandMap()->getCommand(strtolower($command))) instanceof FactionsCommand){
            $message = TextFormat::GREEN . "--------- " . " Help: /" . $cmd->getName() . " ---------\n";
            $message .= TextFormat::GREEN . "Description: " . $cmd->getDescription() . "\n";
            $message .= TextFormat::GREEN . "Usage: " . implode("\n" . TextFormat::WHITE, explode("\n", $cmd->getUsage())) . "\n";
            $sender->sendMessage($message);

            return;
        }
        $sender->sendMessage(TextFormat::RED . "No help for " . strtolower($command));
    }
}