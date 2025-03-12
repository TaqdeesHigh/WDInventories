<?php
declare(strict_types=1);

namespace taqdees\WDInventories\storage;

use pocketmine\utils\Config;

class InventoryStorage {
    /** @var Config */
    private Config $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }
    
    /**
     * Save player's inventory data for a specific world
     */
    public function savePlayerWorldInventory(string $playerName, string $worldName, array $inventoryData): void {
        $inventories = $this->config->getAll();
        
        if (!isset($inventories[$playerName])) {
            $inventories[$playerName] = [];
        }
        
        $inventories[$playerName][$worldName] = $inventoryData;
        $this->config->setAll($inventories);
        $this->config->save();
    }
    
    /**
     * Get player's inventory data for a specific world
     */
    public function getPlayerWorldInventory(string $playerName, string $worldName): ?array {
        $inventories = $this->config->getAll();
        
        if (!isset($inventories[$playerName]) || !isset($inventories[$playerName][$worldName])) {
            return null;
        }
        
        return $inventories[$playerName][$worldName];
    }
}