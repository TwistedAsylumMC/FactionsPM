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

use pocketmine\level\Position;
use pocketmine\Server;
use SQLite3;
use function array_values;
use function strtolower;

class FactionsDatabase{

    /** @var SQLite3 */
    private $database;

    public function __construct(string $path){
        $this->database = new SQLite3($path);
        $this->database->exec("CREATE TABLE IF NOT EXISTS Factions(Id INTEGER PRIMARY KEY AUTOINCREMENT, Faction VARCHAR(15) NOT NULL, Leader VARCHAR(15) NOT NULL, Description VARCHAR(100) NOT NULL DEFAULT 'No description set', HomeX REAL DEFAULT null, HomeY REAL DEFAULT null, HomeZ REAL DEFAULT null, HomeWorld VARCHAR(255) DEFAULT null)");
        $this->database->exec("CREATE TABLE IF NOT EXISTS Players(Username VARCHAR(15) NOT NULL PRIMARY KEY, Faction INTEGER NOT NULL DEFAULT 0, FactionRank VARCHAR(15) NOT NULL DEFAULT 'Member')");
    }

    public function getDatabase() : SQLite3{
        return $this->database;
    }

    public function createFaction(string $faction, string $leader) : ?Faction{
        if($this->factionExists($faction)){
            return null;
        }
        $stmt = $this->database->prepare("INSERT INTO Factions(Faction, Leader) VALUES(:faction, :leader)");
        $stmt->bindValue(":faction", $faction);
        $stmt->bindValue(":leader", $leader);
        $stmt->execute();

        $id = $this->getFactionId($faction);

        $this->setPlayerFaction($leader, $id, "Leader");

        return new Faction($id, $faction, "No description set", $leader, [], [], null, null, null, Server::getInstance()->getDefaultLevel()->getName());
    }

    public function factionExists(string $faction) : bool{
        $stmt = $this->database->prepare("SELECT * FROM Factions WHERE LOWER(Faction)='" . strtolower($faction) . "'");
        $result = $stmt->execute()->fetchArray();

        return $result !== false;
    }

    public function getFactionId(string $faction) : ?int{
        $stmt = $this->database->prepare("SELECT * FROM Factions WHERE LOWER(Faction)='" . strtolower($faction) . "'");
        $result = $stmt->execute()->fetchArray();
        if(!$result){
            return null;
        }

        return (int) $result["Id"];
    }

    public function setPlayerFaction(string $player, int $faction, string $rank) : void{
        $stmt = $this->database->prepare("UPDATE Players SET Faction='" . $faction . "', FactionRank='" . $rank . "' WHERE Username='" . $player . "'");
        $stmt->execute();
    }

    public function deleteFaction(int $faction) : void{
        $stmt = $this->database->prepare("DELETE FROM Factions WHERE Id='" . $faction . "'");
        $stmt->execute();
        $stmt = $this->database->prepare("UPDATE Players SET Faction='0', FactionRank='Member' WHERE Faction='" . $faction . "'");
        $stmt->execute();
    }

    public function updateFactionName(int $faction, string $name) : void{
        $stmt = $this->database->prepare("UPDATE Factions SET Faction='" . $name . "' WHERE Id='" . $faction . "'");
        $stmt->execute();
    }

    public function updateFactionDescription(int $faction, string $description) : void{
        $stmt = $this->database->prepare("UPDATE Factions SET Description='" . $description . "' WHERE Id='" . $faction . "'");
        $stmt->execute();
    }

    public function updateFactionHome(int $faction, ?Position $home) : void{
        $stmt = $this->database->prepare("UPDATE Factions SET HomeX='" . ($home === null ? null : $home->getX()) . "', HomeY='" . ($home === null ? null : $home->getY()) . "', HomeZ='" . ($home === null ? null : $home->getZ()) . "', HomeWorld='" . ($home === null ? null : $home->getLevel()->getName()) . "' WHERE Id='" . $faction . "'");
        $stmt->execute();
    }

    public function getPlayerFaction(string $player) : ?Faction{
        $stmt = $this->database->prepare("SELECT Faction FROM Players WHERE Username='" . $player . "'");
        $result = $stmt->execute()->fetchArray();
        if(!$result){
            $this->registerPlayer($player);

            return null;
        }
        if($result["Faction"] === 0){
            return null;
        }

        return $this->getFaction($result["Faction"]);
    }

    public function registerPlayer(string $player) : void{
        $stmt = $this->database->prepare("INSERT OR REPLACE INTO Players(Username) VALUES(:username)");
        $stmt->bindParam(":username", $player);
        $stmt->execute();
    }

    public function getFaction(int $id) : ?Faction{
        $stmt = $this->database->prepare("SELECT * FROM Factions WHERE Id='" . $id . "'");
        $result = $stmt->execute()->fetchArray();
        if(!$result){
            return null;
        }

        return new Faction($id, $result["Faction"], $result["Description"], $result["Leader"], $this->getFactionModerators($id), $this->getFactionMembers($id), $result["HomeX"], $result["HomeY"], $result["HomeZ"], $result["HomeWorld"]);
    }

    public function getFactionModerators(int $faction) : array{
        $stmt = $this->database->prepare("SELECT Username FROM Players WHERE Faction='" . $faction . "' AND FactionRank='Moderator'");
        $result = $stmt->execute()->fetchArray();
        if(!$result){
            return [];
        }

        return array_values($result);
    }

    public function getFactionMembers(int $faction) : array{
        $stmt = $this->database->prepare("SELECT Username FROM Players WHERE Faction='" . $faction . "' AND FactionRank='Member'");
        $result = $stmt->execute()->fetchArray();
        if(!$result){
            return [];
        }

        return array_values($result);
    }
}