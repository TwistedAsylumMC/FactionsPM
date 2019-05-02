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

use pocketmine\plugin\PluginBase;
use twisted\factionspm\commands\FactionsCommandMap;

class FactionsPM extends PluginBase{

    /** @var self */
    private static $instance;

    /** @var FactionsCache */
    private $cache;

    /** @var FactionsCommandMap */
    private $commandMap;

    /** @var FactionsDatabase */
    private $database;

    public function onLoad() : void{
        self::$instance = $this;
    }

    public function onEnable() : void{
        $this->cache = new FactionsCache();
        $this->commandMap = new FactionsCommandMap($this);
        $this->database = new FactionsDatabase();
        $this->getServer()->getPluginManager()->registerEvents(new FactionsListener($this), $this);
    }

    /**
     * @return self
     */
    public static function getInstance() : self{
        return self::$instance;
    }

    public function getCache() : FactionsCache{
        return $this->cache;
    }

    public function getCommandMap() : FactionsCommandMap{
        return $this->commandMap;
    }

    public function getDatabase() : FactionsDatabase{
        return $this->database;
    }
}