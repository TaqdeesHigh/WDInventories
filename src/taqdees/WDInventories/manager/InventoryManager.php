<?php
declare(strict_types=1);

namespace taqdees\WDInventories\manager;

use pocketmine\player\Player;
use pocketmine\utils\Config;
use taqdees\WDInventories\Main;
use taqdees\WDInventories\storage\InventoryStorage;
use taqdees\WDInventories\util\LoggerUtil;
use taqdees\WDInventories\service\InventorySerializationService;

class InventoryManager {
    /** @var InventoryStorage */
    private InventoryStorage $storage;
    
    /** @var InventorySerializationService */
    private InventorySerializationService $serializationService;

    public function __construct(Main $plugin, LoggerUtil $logger) {
        $this->storage = new InventoryStorage(
            new Config($plugin->getDataFolder() . "inventories.yml", Config::YAML)
        );
        $this->serializationService = new InventorySerializationService($logger);
    }
    
    /**
     * Save a player's inventory for a specific world
     */
    public function saveInventory(Player $player, string $worldName): void {
        $playerName = $player->getName();
        
        $inventoryData = $this->serializationService->serializePlayerInventories($player);
        $this->storage->savePlayerWorldInventory($playerName, $worldName, $inventoryData);
    }
    
    /**
     * Load a player's inventory for a specific world
     */
    public function loadInventory(Player $player, string $worldName): void {
        $playerName = $player->getName();

        $this->clearPlayerInventories($player);
        $inventoryData = $this->storage->getPlayerWorldInventory($playerName, $worldName);
        if ($inventoryData === null) {
            return;
        }
        $this->serializationService->deserializePlayerInventories($player, $inventoryData);
    }
    
    /**
     * Clear all player inventories
     */
    private function clearPlayerInventories(Player $player): void {
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->getOffHandInventory()->clearAll();
        $player->getEnderInventory()->clearAll();
    }
}