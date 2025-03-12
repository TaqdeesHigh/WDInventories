<?php
declare(strict_types=1);

namespace taqdees\WDInventories;

use pocketmine\plugin\PluginBase;
use taqdees\WDInventories\listener\PlayerEventListener;
use taqdees\WDInventories\manager\InventoryManager;
use taqdees\WDInventories\util\LoggerUtil;

class Main extends PluginBase {
    /** @var InventoryManager */
    private InventoryManager $inventoryManager;
    
    /** @var LoggerUtil */
    private LoggerUtil $loggerUtil;

    public function onEnable(): void {
        $this->loggerUtil = new LoggerUtil($this->getLogger());
        if (!file_exists($this->getDataFolder())) {
            mkdir($this->getDataFolder());
        }
        $this->inventoryManager = new InventoryManager($this, $this->loggerUtil);
        $playerEventListener = new PlayerEventListener($this->inventoryManager, $this->loggerUtil);
        $this->getServer()->getPluginManager()->registerEvents($playerEventListener, $this);
    }
    
    public function onDisable(): void {
        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $worldName = $player->getWorld()->getFolderName();
            $this->inventoryManager->saveInventory($player, $worldName);
        }
    }
    
    public function getInventoryManager(): InventoryManager {
        return $this->inventoryManager;
    }
}