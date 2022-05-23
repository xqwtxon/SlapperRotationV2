<?php

namespace xqwtxon\SlapperRotationV2;

use xqwtxon\SlapperRotationV2\SlapperListener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase {

	public function onLoad() :void{
        $this->saveResource("config.yml");
        $this->reloadConfig();
        $this->checkConfig();
    }
    
    private function checkConfig() :void{
	$log = $this->getLogger();
	$pluginConfigResource = $this->getResource("config.yml");
	$pluginConfig = yaml_parse(stream_get_contents($pluginConfigResource));
	fclose($pluginConfigResource);
	    $config = $this->getConfig();
		
	    if($pluginConfig == false) {
	    	$log->critical("Invalid configuration.");
	    	$this->getServer()->getPluginManager()->disablePlugin($this);
	    	return;
	    }

	    if($config->get("config-version") === $pluginConfig["config-version"]) return;
	    $log->notice("Your config is outdated!");
	    $log->info("Your old config.yml is renamed as old-config.yml");
	    @rename($this->getDataFolder(). 'config.yml', 'old-config.yml');
	    $this->saveResource("config.yml");

    }

	public function onEnable() :void {
        $config = $this->getConfig();
        $log = $this->getLogger();
        $maxDistance = $config->get("max-distance");
        if (!isset($maxDistance)){
            $log->info("Max Distance cant be blank!");
            $config->set("max-distance", 8);
            return;
        }
        if ($config->get("max-distance") < 4){
            $log->info(TextFormat::RED."Your max distance is too low. Make sure your max-distance in config is not at least higher on 4!");
            $log->info("[INFO] Max-Distance was changed to 16 as default.");
            $config->set("max-distance", 8);
            return;
        }
		$this->saveDefaultConfig();
	}
}
