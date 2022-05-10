<?php

namespace xqwtxon\SlapperRotationV2;

use xqwtxon\SlapperRotationV2\SlapperListener;
use pocketmine\plugin\PluginBase;
use xqwtxon\SlapperRotationV2\SlapperInfo;
use pocketmine\utils\TextFormat;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\VersionInfo;

class Main extends PluginBase implements SlapperInfo {
    public function onLoad() :void{
        $this->saveResource("config.yml");
        $config = $this->getConfig();
        $log = $this->getLogger();
        $version = SlapperInfo::PLUGIN_VERSION;
        if ($config->get("config-version") == SlapperInfo::CONFIG_VERSION){
            return;
        } else {
            $log->notice("Your config is outdated!");
            $log->info("Your old config.yml was as old-config.yml");
            @rename($this->getDataFolder(). 'config.yml', 'old-config.yml');
            $this->saveResource("config.yml");
        }
        
        if (SlapperInfo::IS_DEVELOPMENT_BUILD == true){
            $log->warning(TextFormat::RED."Your SlapperRotation is in development build! You may expect crash during the plugin. You can make issue about this plugin by visiting plugin github issue!");
        }
    }
    
    
	public function onEnable() :void {
        $config = $this->getConfig();
        $log = $this->getLogger();
            if (SlapperInfo::PROTOCOL_VERSION == ProtocolInfo::CURRENT_PROTOCOL){
                $log->info(TextFormat::GREEN."Your SlapperRotation is Compatible with your version!");
            } else {
                $log->info(TextFormat::RED."Your SlapperRotation isnt Compatible with your version!");
                $this->getServer()->getPluginManager()->disablePlugin($this);
            }
        $maxDistance = $config->get("max-distance");
        $toggle = $config->get("enabled");
        if (!isset($maxDistance)){
            $log->info("Max Distance cant be blank!");
            $config->set("max-distance", 8);
            return;
        }
        if ($config->get("max-distance") < SlapperInfo::DEFAULT_MINIMUM_DISTANCE){
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
