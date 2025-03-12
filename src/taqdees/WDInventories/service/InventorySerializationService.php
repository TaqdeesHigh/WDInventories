<?php
declare(strict_types=1);

namespace taqdees\WDInventories\service;

use pocketmine\item\Item;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\player\Player;
use taqdees\WDInventories\util\LoggerUtil;

class InventorySerializationService {
    /** @var BigEndianNbtSerializer */
    private BigEndianNbtSerializer $nbtSerializer;
    
    /** @var LoggerUtil */
    private LoggerUtil $logger;

    public function __construct(LoggerUtil $logger) {
        $this->nbtSerializer = new BigEndianNbtSerializer();
        $this->logger = $logger;
    }
    
    /**
     * Serialize all player inventories
     */
    public function serializePlayerInventories(Player $player): array {
        $inventoryData = [
            'items' => [],
            'armorItems' => [],
            'offHandItem' => null,
            'enderChestInventory' => []
        ];
        
        foreach ($player->getInventory()->getContents() as $slot => $item) {
            $serialized = $this->serializeItem($item);
            if ($serialized !== null) {
                $inventoryData['items'][$slot] = $serialized;
            }
        }
        
        foreach ($player->getArmorInventory()->getContents() as $slot => $item) {
            $serialized = $this->serializeItem($item);
            if ($serialized !== null) {
                $inventoryData['armorItems'][$slot] = $serialized;
            }
        }
        $offHandItem = $player->getOffHandInventory()->getItem(0);
        if (!$offHandItem->isNull()) {
            $inventoryData['offHandItem'] = $this->serializeItem($offHandItem);
        }
        foreach ($player->getEnderInventory()->getContents() as $slot => $item) {
            $serialized = $this->serializeItem($item);
            if ($serialized !== null) {
                $inventoryData['enderChestInventory'][$slot] = $serialized;
            }
        }
        
        return $inventoryData;
    }
    
    /**
     * Deserialize and apply inventories to a player
     */
    public function deserializePlayerInventories(Player $player, array $inventoryData): void {
        if (isset($inventoryData['items']) && is_array($inventoryData['items'])) {
            foreach ($inventoryData['items'] as $slot => $itemData) {
                $item = $this->deserializeItem($itemData);
                if ($item !== null) {
                    $player->getInventory()->setItem((int)$slot, $item);
                }
            }
        }
        if (isset($inventoryData['armorItems']) && is_array($inventoryData['armorItems'])) {
            foreach ($inventoryData['armorItems'] as $slot => $itemData) {
                $item = $this->deserializeItem($itemData);
                if ($item !== null) {
                    $player->getArmorInventory()->setItem((int)$slot, $item);
                }
            }
        }
        if (isset($inventoryData['offHandItem'])) {
            $item = $this->deserializeItem($inventoryData['offHandItem']);
            if ($item !== null) {
                $player->getOffHandInventory()->setItem(0, $item);
            }
        }
        if (isset($inventoryData['enderChestInventory']) && is_array($inventoryData['enderChestInventory'])) {
            foreach ($inventoryData['enderChestInventory'] as $slot => $itemData) {
                $item = $this->deserializeItem($itemData);
                if ($item !== null) {
                    $player->getEnderInventory()->setItem((int)$slot, $item);
                }
            }
        }
    }
    
    /**
     * Serialize an item to string
     */
    private function serializeItem(Item $item): ?string {
        if ($item->isNull()) {
            return null;
        }
        
        // Serialize the item to an NBT compound tag
        $nbt = $item->nbtSerialize();
        $encodedNbt = $this->nbtSerializer->write(new TreeRoot($nbt));
        return base64_encode($encodedNbt);
    }
    
    /**
     * Deserialize string back to an item
     */
    private function deserializeItem(?string $data): ?Item {
        if ($data === null) {
            return null;
        }
        
        try {
            $decodedNbt = base64_decode($data);
            $nbt = $this->nbtSerializer->read(substr($decodedNbt, 0))->mustGetCompoundTag();
            return Item::nbtDeserialize($nbt);
        } catch (\Throwable $e) {
            $this->logger->error("Failed to deserialize item: " . $e->getMessage());
            return null;
        }
    }
}