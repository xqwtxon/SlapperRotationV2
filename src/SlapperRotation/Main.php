<?php

namespace SlapperRotation;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector2;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {
    const PRODUCT_KEY = "TX9XD-98N7V-6WMQ6-BX7FG-H8Q99";
    
    public function onLoad() :void{
        if ($this->getConfig()->get("product-key") == self::PRODUCT_KEY){
            $this->getServer()->getLogger()->info("[SR] Product Key was Verified!");
        } else {
            $this->getServer()->getLogger()->info("[SR] Product Key is Invalid! Get License on https://github.com/xqwtxon/SlapperRotation/#Product-Key!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }
    
    
	public function onEnable() :void{
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onPlayerMove(PlayerMoveEvent $ev) {
		$player = $ev->getPlayer();
		$from = $ev->getFrom();
		$to = $ev->getTo();
		if($from->distance($to) < 0.1) {
			return;
		}
		$maxDistance = $this->getConfig()->get("max-distance");
		foreach ($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy($maxDistance, $maxDistance, $maxDistance), $player) as $e) {
			if($e instanceof Player) {
				continue;
			}
			if(substr($e->getSaveId(), 0, 7) !== "Slapper") {
				continue;
			}
			switch ($e->getSaveId()) {
				case "SlapperFallingSand":
				case "SlapperMinecart":
				case "SlapperBoat":
				case "SlapperPrimedTNT":
				case "SlapperShulker":
					continue 2;
			}
			$xdiff = $player->getX - $e->getX;
			$zdiff = $player->getZ - $e->getZ;
			$angle = atan2($zdiff, $xdiff);
			$yaw = (($angle * 180) / M_PI) - 90;
			$ydiff = $player->y - $e->y;
			$v = new Vector2($e->getX, $e->getZ);
			$dist = $v->distance($player->getX, $player->getZ);
			$angle = atan2($dist, $ydiff);
			$pitch = (($angle * 180) / M_PI) - 90;

			if($e->getSaveId() === "SlapperHuman") {
				$pk = new MovePlayerPacket();
				$pk->entityRuntimeId = $e->getId();
				$pk->position = $e->asVector3()->add(0, $e->getEyeHeight(), 0);
				$pk->yaw = $yaw;
				$pk->pitch = $pitch;
				$pk->headYaw = $yaw;
				$pk->onGround = $e->onGround;
			} else {
				$pk = new MoveActorAbsolutePacket();
				$pk->entityRuntimeId = $e->getId();
				$pk->position = $e->asVector3();
				$pk->xRot = $pitch;
				$pk->yRot = $yaw;
				$pk->zRot = $yaw;
			}
			$player->dataPacket($pk);
		}
	}

}
