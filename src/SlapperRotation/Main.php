<?php

namespace SlapperRotation;

use SlapperRotation\SlapperListener;
use pocketmine\plugin\PluginBase;
use SlapperRotation\SlapperInfo;
use pocketmine\utils\TextFormat;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\VersionInfo;

class Main extends PluginBase implements SlapperInfo {
    public function onLoad() :void{
        $this->saveResource("config.yml");
        $config = $this->getConfig();
        $log = $this->getServer()->getLogger();
        $prefix = $config->get("prefix");
        $version = SlapperInfo::PLUGIN_VERSION;
        $log->notice($prefix.TextFormat::YELLOW."You are running §aSlapperRotation {$version} §eby xqwtxon!");
        if ($config->get("config-version") == SlapperInfo::CONFIG_VERSION){
            $log->info($prefix."Loaded SlapperRotation!");
        } else {
            $log->info($prefix."Your config is outdated!");
            $log->info($prefix."Your old config.yml was as old-config.yml");
            @rename($this->getDataFolder(). 'config.yml', 'old-config.yml');
            $this->saveResource("config.yml");
        }
        
        if (SlapperInfo::IS_DEVELOPMENT_BUILD == true){
            $log->warning($prefix.TextFormat::RED."Your SlapperRotation is in development build! You may expect crash during the plugin. You can make issue about this plugin by visiting plugin github issue!");
        }
    }
    
    
	public function onEnable() :void{
	    $config = $this->getConfig();
        $log = $this->getServer()->getLogger();
        $prefix = $config->get("prefix");
        
            if (SlapperInfo::PROTOCOL_VERSION == ProtocolInfo::CURRENT_PROTOCOL){
                $log->info($prefix.TextFormat::GREEN."Your SlapperRotation is Compatible with your version!");
            } else {
                $log->info($prefix.TextFormat::RED."Your SlapperRotation isnt Compatible with your version!");
                $this->getServer()->getPluginManager()->disablePlugin($this);
            }
        
        
        if ($config->get("max-distance") == 4){
            $log->info($prefix.TextFormat::RED."Your max distance is too low. Make sure your max-distance in config is not at least higher on 4!");
            $log->info($prefix."Max-Distance was changed to 16 as default.");
            $config->set("max-distance", 16);
            return;
        } else {
            $this->getServer()->getPluginManager()->registerEvents(new SlapperListener($this), $this);
        }
		$this->saveDefaultConfig();
	}
	
	public function onDisable() :void {
	    $config = $this->getConfig();
        $log = $this->getServer()->getLogger();
        $prefix = $config->get("prefix");
        $log->info($prefix.TextFormat::RED."Successfully disabled the plugin!");
	}
}
