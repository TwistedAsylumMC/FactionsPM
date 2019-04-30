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

namespace twisted\factionspm\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use twisted\factionspm\commands\defaults\AcceptCommand;
use twisted\factionspm\commands\defaults\CreateCommand;
use twisted\factionspm\commands\defaults\DescriptionCommand;
use twisted\factionspm\commands\defaults\DisbandCommand;
use twisted\factionspm\commands\defaults\HelpCommand;
use twisted\factionspm\commands\defaults\HomeCommand;
use twisted\factionspm\commands\defaults\InviteCommand;
use twisted\factionspm\commands\defaults\KickCommand;
use twisted\factionspm\commands\defaults\LeaveCommand;
use twisted\factionspm\commands\defaults\NameCommand;
use twisted\factionspm\commands\defaults\SetHomeCommand;
use twisted\factionspm\commands\defaults\UnInviteCommand;
use twisted\factionspm\commands\defaults\UnsetHomeCommand;
use twisted\factionspm\commands\defaults\VersionCommand;
use twisted\factionspm\FactionsPM;
use function array_shift;
use function in_array;
use function strtolower;

class FactionsCommandMap extends Command{

    /** @var FactionsCommand[] */
    private $commands = [];

    /** @var FactionsPM */
    private $plugin;

    public function __construct(FactionsPM $plugin){
        $this->plugin = $plugin;
        parent::__construct("factions", "Main factions command", "Use '/f help'", ["f"]);
        $this->plugin->getServer()->getCommandMap()->register("FactionsPM", $this);
        $this->registerCommand(new AcceptCommand());
        $this->registerCommand(new CreateCommand());
        $this->registerCommand(new DescriptionCommand());
        $this->registerCommand(new DisbandCommand());
        $this->registerCommand(new HelpCommand());
        $this->registerCommand(new HomeCommand());
        $this->registerCommand(new InviteCommand());
        $this->registerCommand(new KickCommand());
        $this->registerCommand(new LeaveCommand());
        $this->registerCommand(new NameCommand());
        $this->registerCommand(new SetHomeCommand());
        $this->registerCommand(new UnInviteCommand());
        $this->registerCommand(new UnsetHomeCommand());
        $this->registerCommand(new VersionCommand());
    }

    public function registerCommand(FactionsCommand $command) : void{
        $this->commands[strtolower($command->getName())] = $command;
    }

    /**
     * @return FactionsCommand[]
     */
    public function getCommands() : array{
        return $this->commands;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(isset($args[0]) && ($command = $this->getCommand(array_shift($args))) !== null){
            $command->execute($sender, $args, $this->plugin);

            return;
        }
        $sender->sendMessage(TextFormat::RED . $this->getUsage());
    }

    public function getCommand(string $alias) : ?FactionsCommand{
        foreach($this->commands as $nam => $command){
            if(strtolower($alias) === $command->getName() || in_array(strtolower($alias), $command->getAlases(), true)){
                return $command;
            }
        }

        return null;
    }
}