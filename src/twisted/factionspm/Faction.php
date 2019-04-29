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

use pocketmine\Server;
use function array_flip;
use function array_map;
use function array_merge;
use function in_array;
use function strtolower;

class Faction{

    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var string */
    private $leader;

    /** @var string[] */
    private $moderators;

    /** @var string[] */
    private $members;

    public function __construct(int $id, string $name, string $description, string $leader, array $moderators, array $members){
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->leader = $leader;
        $this->moderators = $moderators;
        $this->members = $members;
    }

    public function getId() : int{
        return $this->id;
    }

    public function getName() : string{
        return $this->name;
    }

    public function setName(string $name) : void{
        $this->name = $name;
    }

    public function getDescription() : string{
        return $this->description;
    }

    public function setDescription(string $description) : void{
        $this->description = $description;
    }

    public function getLeader() : string{
        return $this->leader;
    }

    public function setLeader(string $leader) : void{
        $this->leader = $leader;
    }

    public function isLeader(string $player) : bool{
        return strtolower($player) === strtolower($this->leader);
    }

    public function addModerator(string $moderator) : void{
        $this->moderators[] = $moderator;
    }

    public function isModerator(string $player) : bool{
        return in_array(strtolower($player), array_map("\strtolower", $this->moderators), true);
    }

    public function getModerators() : array{
        return $this->moderators;
    }

    public function setModerators(array $moderators) : void{
        $this->moderators = $moderators;
    }

    public function removeModerator(string $moderator) : void{
        $moderators = array_flip($this->moderators);
        unset($moderators[$moderator]);
        $this->moderators = array_flip($moderators);
    }

    public function addMember(string $member) : void{
        $this->members[] = $member;
    }

    public function isMember(string $player) : bool{
        return in_array(strtolower($player), array_map("\strtolower", $this->members), true);
    }

    public function getMembers() : array{
        return $this->members;
    }

    public function setMembers(array $members) : void{
        $this->members = $members;
    }

    public function removeMember(string $member) : void{
        $members = array_flip($this->members);
        unset($members[$member]);
        $this->members = array_flip($members);
    }

    public function isInFaction(string $player) : bool{
        return in_array(strtolower($player), array_map("\strtolower", $this->getAllMembers()), true);
    }

    public function getAllMembers() : array{
        return array_merge([$this->leader], array_merge($this->moderators, $this->members));
    }

    public function getOnlineMembers() : array{
        $online = [];
        foreach($this->getAllMembers() as $member){
            if(Server::getInstance()->getPlayer($member) !== null){
                $online[] = $member;
            }
        }

        return $online;
    }

    public function getOfflineMembers() : array{
        $offline = [];
        foreach($this->getAllMembers() as $member){
            if(Server::getInstance()->getPlayer($member) === null){
                $offline[] = $member;
            }
        }

        return $offline;
    }

    public function broadcastMessage(string $message) : void{
        foreach($this->getAllMembers() as $member){
            if(($member = Server::getInstance()->getPlayer($member)) !== null){
                $member->sendMessage($message);
            }
        }
    }
}