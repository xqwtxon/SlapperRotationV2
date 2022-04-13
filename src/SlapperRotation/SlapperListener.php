<?php

namespace SlapperRotation;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector2;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\player\Player;
use SlapperRotation\Main as Loader;

class SlapperListener implements Listener {
    
    public function __construct(private Loader $plugin){
        //NOOP
    }
    public function onPlayerMove(PlayerMoveEvent $ev) {
		$player = $ev->getPlayer();
		$from = $ev->getFrom();
		$to = $ev->getTo();
		if($from->distance($to) < 0.1) {
			return;
		}
		$maxDistance = $this->plugin->getConfig()->get("max-distance");
		foreach ($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy($maxDistance, $maxDistance, $maxDistance), $player) as $e) {
			if($e instanceof Player) {
				continue;
			}
			if(substr($e->getId(), 0, 7) !== "Slapper") {
				continue;
			}
			switch ($e->getId()) {
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