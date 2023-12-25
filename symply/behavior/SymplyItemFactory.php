<?php

/*
 *
 *  _____                       _
 * /  ___|                     | |
 * \ `--. _   _ _ __ ___  _ __ | |_   _
 *  `--. \ | | | '_ ` _ \| '_ \| | | | |
 * /\__/ / |_| | | | | | | |_) | | |_| |
 * \____/ \__, |_| |_| |_| .__/|_|\__, |
 *         __/ |         | |       __/ |
 *        |___/          |_|      |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Symply Team
 * @link http://www.symplymc.com/
 *
 *
 */

declare(strict_types=1);

namespace symply\behavior;

use Closure;
use InvalidArgumentException;
use pmmp\thread\ThreadSafeArray;
use pocketmine\block\Block;
use pocketmine\data\bedrock\item\BlockItemIdMap;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\cache\CreativeInventoryCache;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\ItemComponentPacket;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\ItemComponentPacketEntry;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use ReflectionProperty;
use symply\behavior\blocks\IBlockCustom;
use symply\behavior\items\ICustomItem;
use function array_values;
use function mb_strtoupper;
use function uasort;

final class SymplyItemFactory
{
	use SingletonTrait;

	public static int $BLOCK_ID_NEXT_MCBE = 10000;

	public static int $ITEM_ID_NEXT_MCBE = 9950;

	/** @var array<string, Item> */
	private array $itemsOverwrite = [];

	/** @var array<string, Item> */
	private array $itemCustoms = [];

	/** @var ThreadSafeArray<ThreadSafeArray<Closure>> */
	private ThreadSafeArray $asyncTransmitterItemOverwrite;

	/** @var ThreadSafeArray<ThreadSafeArray<Closure>> */
	private ThreadSafeArray $asyncTransmitterItemCustom;

	/** @var ItemComponentPacketEntry[] */
	private array $itemsComponentPacketEntries = [];

	private ?ItemComponentPacket $cache = null;

	public function __construct(private readonly bool $asyncMode = false)
	{
		if (!$this->asyncMode) {
			$this->asyncTransmitterItemCustom = new ThreadSafeArray();
			$this->asyncTransmitterItemOverwrite = new ThreadSafeArray();
		}
		CreativeInventoryCache::reset();
	}

	/**
	 * @param Closure(): Item&ICustomItem $itemClosure
	 */
	public function register(Closure $itemClosure, ?Closure $serializer = null, ?Closure $deserializer = null) : void
	{
		/**
		 * @var Item&ICustomItem $itemCustom
		 */
		$itemCustom = $itemClosure();
		$identifier = $itemCustom->getIdentifier()->getNamespaceId();
		if (isset($this->itemCustoms[$identifier])){
			throw new InvalidArgumentException("Item ID {$itemCustom->getIdentifier()->getNamespaceId()} is already used by another item");
		}
		$itemId = self::$ITEM_ID_NEXT_MCBE++;
		$this->itemCustoms[$identifier] = $itemCustom;
		$this->registerCustomItemMapping($identifier, $itemId, new ItemTypeEntry($identifier, $itemId , true));
		GlobalItemDataHandlers::getDeserializer()->map($identifier, $deserializer ??= static fn() => clone $itemCustom);
		GlobalItemDataHandlers::getSerializer()->map($itemCustom, $serializer ??= static fn() => new SavedItemData($identifier));
		StringToItemParser::getInstance()->register($identifier, static fn() => clone $itemCustom);
		CreativeInventory::getInstance()->add($itemCustom);
		if (!$this->asyncMode) {
			$this->itemsComponentPacketEntries[] = new ItemComponentPacketEntry($identifier, new CacheableNbt($itemCustom->getItemBuilder()->toPacket($itemId)));
			$this->asyncTransmitterItemCustom[] = ThreadSafeArray::fromArray([$itemClosure, $serializer, $deserializer]);
		}
	}

	/**
	 * Registers a custom item ID to the required mappings in the global ItemTypeDictionary instance.
	 */
	private function registerCustomItemMapping(string $identifier, int $itemId, ItemTypeEntry $itemTypeEntry) : void {
		$dictionary = TypeConverter::getInstance()->getItemTypeDictionary();
		$reflection = new \ReflectionClass($dictionary);
		$itemTypes = $reflection->getProperty('itemTypes');
		/** @var ItemTypeEntry[] $value */
		$value = $itemTypes->getValue($dictionary);
		$value[] = $itemTypeEntry;
		$value = array_values($value);
		uasort($value, function (ItemTypeEntry $itemTypeEntryA, ItemTypeEntry $itemTypeEntryB) {
			return $itemTypeEntryA->getNumericId() > $itemTypeEntryB->getNumericId() ? 1 : -1;
		});
		$itemTypes->setValue($dictionary, $value);
		$intToString = $reflection->getProperty("intToStringIdMap");
		/** @var int[] $value */
		$value = $intToString->getValue($dictionary);
		$intToString->setValue($dictionary, $value + [$itemId => $identifier]);

		$stringToInt = $reflection->getProperty("stringToIntMap");
		/** @var int[] $value */
		$value = $stringToInt->getValue($dictionary);
		$stringToInt->setValue($dictionary, $value + [$identifier => $itemId]);
	}

	/**
	 * Registers the required mappings for the block to become an item that can be placed etc. It is assigned an ID that
	 * correlates to its block ID.
	 */
	public function registerBlockItem(string $identifier, Block&IBlockCustom $block) : void {
		$itemId = 255 - $this->getBlockIdNextMCBE();
		$this->registerCustomItemMapping($identifier, $itemId, new ItemTypeEntry($identifier, $itemId, false));
		StringToItemParser::getInstance()->registerBlock($identifier, fn() => clone $block);

		$blockItemIdMap = BlockItemIdMap::getInstance();
		$reflection = new \ReflectionClass($blockItemIdMap);

		$itemToBlockId = $reflection->getProperty("itemToBlockId");
		/** @var string[] $value */
		$value = $itemToBlockId->getValue($blockItemIdMap);
		$itemToBlockId->setValue($blockItemIdMap, $value + [$identifier => $identifier]);
	}

	/**
	 * @param Closure(): Item $itemClosure
	 * @throws \ReflectionException
	 */
	public function overwriteItemPMMP(Closure $itemClosure, null|Closure|false $serializer = null, null|Closure|false $deserializer = null) : void
	{
		/**
		 * @var Item $item
		 */
		$item = $itemClosure();
		try {
			$vanillaItemsNoConstructor = (new \ReflectionClass(VanillaItems::class))->newInstanceWithoutConstructor();
			$name = null;
			foreach (VanillaItems::getAll() as $index => $vanillaItem) {
				if ($item->getTypeId() === $vanillaItem->getTypeId()) {
					$name = $index;
					break;
				}
			}
			if (!$name)
				return;
			(function () use ($item, $name) {
				self::verifyName($name);
				$upperName = mb_strtoupper($name);
				self::$members[$upperName] = $item;
			})->call($vanillaItemsNoConstructor);
		} catch (\Throwable) {

		}
		$namespaceId = GlobalItemDataHandlers::getSerializer()->serializeType($item)->getName();
		CreativeInventory::getInstance()->remove($item);
		$this->itemsOverwrite[$namespaceId] = $item;
		CreativeInventory::getInstance()->add($item);

		$serializer ??= static fn() => new SavedItemData($namespaceId);
		$deserializer ??= static function () use ($namespaceId) {
			return (clone SymplyItemFactory::getInstance()->getItemOverwrite($namespaceId));
		};
		$instanceDeserializer = GlobalItemDataHandlers::getDeserializer();
		$instanceSerializer = GlobalItemDataHandlers::getSerializer();
		if ($deserializer !== false) {
			try {
				$instanceDeserializer->map($namespaceId, $deserializer);
			} catch (InvalidArgumentException) {
				$deserializerProperty = new ReflectionProperty($instanceDeserializer, "deserializers");
				$value = $deserializerProperty->getValue($instanceDeserializer);
				$value[$namespaceId] = $deserializer;
				$deserializerProperty->setValue($instanceDeserializer, $value);
			}
		}
		if ($serializer !== false) {
			try {
				if ($item instanceof ItemBlock){
					$instanceSerializer->mapBlock($item->getBlock(), $serializer);
				}else{
					$instanceSerializer->map($item, $serializer);
				}
			} catch (InvalidArgumentException) {
				if ($item instanceof ItemBlock) {
					$serializerProperty = new ReflectionProperty($instanceSerializer, "blockItemSerializers");
					$value = $serializerProperty->getValue($instanceSerializer);
					$value[$item->getBlock()->getTypeId()] = $serializer;
					$serializerProperty->setValue($instanceSerializer, $value);
				}else{
					$serializerProperty = new ReflectionProperty($instanceSerializer, "itemSerializers");
					$value = $serializerProperty->getValue($instanceSerializer);
					$value[$item->getTypeId()] = $serializer;
					$serializerProperty->setValue($instanceSerializer, $value);
				}
			}
		}
		if (!$this->asyncMode)
			$this->asyncTransmitterItemOverwrite[] = ThreadSafeArray::fromArray([$itemClosure, $serializer, $deserializer]);
	}

	public function getBlockIdNextMCBE() : int{
		return self::$BLOCK_ID_NEXT_MCBE++;
	}
	/**
	 * @return ThreadSafeArray<ThreadSafeArray<Closure>>
	 */
	public function getAsyncTransmitterItemCustom() : ThreadSafeArray
	{
		return $this->asyncTransmitterItemCustom;
	}

	public function getAsyncTransmitterItemOverwrite() : ThreadSafeArray
	{
		return $this->asyncTransmitterItemOverwrite;
	}

	public function getItemsOverwrite() : array
	{
		return $this->itemsOverwrite;
	}

	public function getItemOverwrite(string $id) : ?Item{
		return $this->itemsOverwrite[$id] ?? null;
	}

	public function getItemsComponentPacketEntries() : array
	{
		return $this->itemsComponentPacketEntries;
	}

	public function getItemsComponentPacket() : ItemComponentPacket{
		return $this->cache ??= ItemComponentPacket::create($this->getItemsComponentPacketEntries());
	}

	/**
	 * @return Item[]
	 */
	public function getItemCustoms() : array
	{
		return $this->itemCustoms;
	}

	public function getItem(string $identifier) : ?Item{
		return $this->itemCustoms[$identifier] ?? null;
	}

	public static function getInstanceModeAsync() : self{
		return self::getInstance(true);
	}

	public static function getInstance(bool $asyncMode = false) : self
	{
		if (self::$instance === null){
			self::$instance = new self($asyncMode);
		}
		return self::$instance;
	}
}
