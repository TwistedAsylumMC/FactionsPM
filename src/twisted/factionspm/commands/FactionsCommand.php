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

use pocketmine\command\CommandSender;
use twisted\factionspm\FactionsPM;
use function array_map;
use function array_shift;

abstract class FactionsCommand{

    /** @var string */
    private $name;

    /** @var array */
    private $alases;

    /** @var string */
    private $description;

    /** @var string */
    private $usage;

    public function __construct(array $aliases, string $description = "", string $usage = ""){
        $aliases = array_map("\strtolower", $aliases);
        $this->name = array_shift($aliases);
        $this->alases = $aliases;
        $this->description = $description;
        $this->usage = $usage;
    }

    public function getName() : string{
        return $this->name;
    }

    public function getAlases() : array{
        return $this->alases;
    }

    public function getDescription() : string{
        return $this->description;
    }

    public function getUsage() : string{
        return $this->usage;
    }

    abstract public function execute(CommandSender $sender, array $args, FactionsPM $plugin) : void;
}