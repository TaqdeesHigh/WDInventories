<?php
declare(strict_types=1);

namespace taqdees\WDInventories\listener;

use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use taqdees\WDInventories\manager\InventoryManager;
use taqdees\WDInventories\util\LoggerUtil;

class PlayerEventListener implements Listener {
    /** @var InventoryManager */
    private InventoryManager $inventoryManager;
    
    /** @var LoggerUtil */
    private LoggerUtil $logger;
    
    public function __construct(InventoryManager $inventoryManager, LoggerUtil $logger) {
        $this->inventoryManager = $inventoryManager;
        $this->logger = $logger;
    }
    
    /**
     * Handle player join event
     */
    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $worldName = $player->getWorld()->getFolderName();
        $this->inventoryManager->loadInventory($player, $worldName);
        $this->logger->debug("Loaded inventory for player {$player->getName()} in world {$worldName}");
    }
    
    /**
     * Handle player quit event
     */
    public function onQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $worldName = $player->getWorld()->getFolderName();
        $this->inventoryManager->saveInventory($player, $worldName);
        $this->logger->debug("Saved inventory for player {$player->getName()} in world {$worldName}");
    }
    
    /**
     * Handle world change event
     */
    public function onWorldChange(EntityTeleportEvent $event): void {
        $entity = $event->getEntity();
        if (!$entity instanceof Player) {
            return;
        }
        
        $fromWorld = $event->getFrom()->getWorld()->getFolderName();
        $toWorld = $event->getTo()->getWorld()->getFolderName();

        if ($fromWorld === $toWorld) {
            return;
        }
        
        $playerName = $entity->getName();
        $this->logger->debug("Player {$playerName} is changing from world {$fromWorld} to {$toWorld}");
        $this->inventoryManager->saveInventory($entity, $fromWorld);
        $this->inventoryManager->loadInventory($entity, $toWorld);
    }
}