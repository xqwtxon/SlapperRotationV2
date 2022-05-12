<?php

namespace xqwtxon\SlapperRotationV2;

use xqwtxon\SlapperRotationV2\SlapperListener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase {
    public function onLoad() :void{
        $this->saveResource("config.yml");
        $config = $this->getConfig();
        $log = $this->getLogger();
        $version = "1.0.4";
        if ($config->get("config-version") == $version){
            return;
        } else {
            $log->notice("Your config is outdated!");
            $log->info("Your old config.yml was as old-config.yml");
            @rename($this->getDataFolder(). 'config.yml', 'old-config.yml');
            $this->saveResource("config.yml");
        }
    }
    
    
	public function onEnable() :void {
        $config = $this->getConfig();
        $log = $this->getLogger();
        $maxDistance = $config->get("max-distance");
        $toggle = $config->get("enabled");
        if (!isset($maxDistance)){
            $log->info("Max Distance cant be blank!");
            $config->set("max-distance", 8);
            return;
        }
        if (!is_int($config->get("max-distance"))){
            $log->info("Max Distance is not string or bool. Please provide it as integer!");
            $config->set("max-distance", 8);
            return;
       } 
        if ($config->get("max-distance") < 4){
            $log->info(TextFormat::RED."Your max distance is too low. Make sure your max-distance in config is not at least higher on 4!");
            $log->info("[INFO] Max-Distance was changed to 16 as default.");
            $config->set("max-distance", 8);
            return;
        }
            if ($toggle == true){
            $this->getServer()->getPluginManager()->registerEvents(new SlapperListener($this), $this);
            return;
            }
            
            if ($toggle == false){
                $log->warning("The SlapperRotation is disabled by configuration.");
            }
		$this->saveDefaultConfig();
	}
}
