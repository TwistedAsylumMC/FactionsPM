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

namespace twisted\factionspm;

use pocketmine\Player;
use function strtolower;
use function time;

class FactionsCache{

    /** @var Faction[] */
    private $factions = [];

    /** @var array[] */
    private $factionInvites = [];

    /** @var int[] */
    private $players = [];

    public function addFactionToCache(Faction $faction) : void{
        $this->factions[$faction->getId()] = $faction;
    }

    public function getFactionsInCache() : array{
        return $this->factions;
    }

    public function getFactionFromCache(int $id) : ?Faction{
        return $this->factions[$id] ?? null;
    }

    public function removeFactionFromCache(int $id) : void{
        unset($this->factions[$id]);
    }

    public function addFactionInvite(Faction $faction, string $invited) : void{
        $this->factionInvites[strtolower($faction->getName())][$invited] = [
            "time" => time() + 60,
            "faction" => $faction
        ];
    }

    public function getFactionFromInvite(string $faction, string $invited) : ?Faction{
        if(!$this->hasFactionInvite($faction, $invited)){
            return null;
        }

        return $this->factionInvites[strtolower($faction)][$invited]["faction"] ?? null;
    }

    public function hasFactionInvite(string $faction, string $invited) : bool{
        return ($this->factionInvites[strtolower($faction)][$invited]["time"] ?? 0) >= time();
    }

    public function removeFactionInvite(string $faction, string $invited) : void{
        unset($this->factionInvites[strtolower($faction)][$invited]);
    }

    public function addPlayerFactionToCache(Player $player, int $faction) : void{
        $this->players[$player->getId()] = $faction;
    }

    public function getPlayerFactionsInCache() : array{
        return $this->players;
    }

    public function getPlayerFactionFromCache(Player $player) : int{
        return $this->players[$player->getId()] ?? 0;
    }

    public function removePlayerFactionFromCache(Player $player) : void{
        unset($this->players[$player->getId()]);
    }
}