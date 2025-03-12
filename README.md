# WDInventories

WDInventories is a PocketMine-MP plugin that separates player inventories per world. This ensures that players have different inventories in different worlds, making it ideal for multi-world servers.

## API
If you're a developer, you can interact with WDInventories using:

```php
/** @var InventoryManager */
$inventoryManager = $plugin->getInventoryManager();

// Save a player's inventory manually
$inventoryManager->saveInventory($player, $worldName);

// Load a player's inventory manually
$inventoryManager->loadInventory($player, $worldName);
```

## License
This project is licensed under the MIT License.

## Developer
- **Taqdees**
