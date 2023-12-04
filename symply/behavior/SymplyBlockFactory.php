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
use pocketmine\block\Block;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\network\mcpe\convert\BlockStateDictionary;
use pocketmine\network\mcpe\convert\BlockTranslator;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use ReflectionException;
use symply\behavior\block\BlockCustom;
use symply\behavior\block\Permutation;
use function array_values;
use function assert;
use function hash;
use function ksort;

class SymplyBlockFactory
{
	use SingletonTrait;

	public function __construct(private readonly bool $asyncMode = false)
	{
	}

	/** @var array<string, BlockCustom> */
	private array $blocks = [];

	/** @var BlockPaletteEntry[] */
	private array $blockPaletteEntries = [];

	/** @var Closure[] */
	private array $asyncTransmitter = [];

	/**
	 * @param Closure(): BlockCustom $blockClosure
	 * @throws ReflectionException
	 */
	public function register(Closure $blockClosure) : void
	{
		$blockCustom = $blockClosure();
		$identifier = $blockCustom->getIdInfo()->getNamespaceId();
		if (isset($this->blocks[$identifier])){
			throw new \InvalidArgumentException("Block ID {$blockCustom->getIdInfo()->getNamespaceId()} is already used by another block");
		}
		RuntimeBlockStateRegistry::getInstance()->register($blockCustom);
		$this->blocks[$identifier] = $blockCustom;
		if ($blockCustom instanceof Permutation){
			$serializer = static function() use ($blockCustom) : BlockStateWriter{
				$writer = BlockStateWriter::create($blockCustom->getIdInfo()->getNamespaceId());
				$blockCustom->serializeState($writer);
				return $writer;
			};
			$deserializer = static function(BlockStateReader $reader) use($identifier) : Block{
				$block = SymplyBlockFactory::getInstance()->getBlock($reader->readString($identifier));
				assert($block instanceof Permutation);
				$block->deserializeState($reader);
				return $block;
			};
		}else{
			$serializer = static fn() => BlockStateWriter::create($blockCustom->getIdInfo()->getNamespaceId());
			$deserializer = static fn() => $blockCustom;
		}
		GlobalBlockStateHandlers::getSerializer()->map($blockCustom, $serializer);
		GlobalBlockStateHandlers::getDeserializer()->map($blockCustom->getIdInfo()->getNamespaceId(), $deserializer);
		if (!$this->asyncMode) {
			$this->blockPaletteEntries[] = new BlockPaletteEntry($identifier, new CacheableNbt($blockCustom->toPacket()));
			$this->asyncTransmitter[] = $blockClosure;
		}
		$this->initializePalette($blockCustom);
	}

	/**
	 * @throws ReflectionException
	 */
	private function initializePalette(BlockCustom $blockCustom) : void
	{
		$typeConverter = TypeConverter::getInstance();
		$identifier = $blockCustom->getIdInfo()->getNamespaceId();
		$tags = array_values($typeConverter->getBlockTranslator()->getBlockStateDictionary()->getStates());

		$oldBlockId = $blockCustom->getIdInfo()->getOldBlockId();
		foreach ($blockCustom->toBlockStateDictionaryEntry() as $blockStateData){
			GlobalBlockStateHandlers::getUpgrader()->getBlockIdMetaUpgrader()->addIdMetaToStateMapping($identifier, $blockStateData->getMeta(), $blockStateData->generateStateData());
			GlobalBlockStateHandlers::getUpgrader()->getBlockIdMetaUpgrader()->addIntIdToStringIdMapping($oldBlockId, $identifier);
			$tags[] = $blockStateData;
		}
		foreach ($tags as $state) {
			$listStates[hash("fnv164", $state->getStateName(), true)][] = $state;
		}
		ksort($listStates);
		$sortedStates = [];
		$n = 0;
		foreach ($listStates as $states) {
			foreach ($states as $state) {
				$sortedStates[$n++] = $state;
			}
		}
		$blockTranslatorProperty = new \ReflectionProperty($typeConverter, "blockTranslator");
		$blockTranslatorProperty->setValue($typeConverter, new BlockTranslator(
			new BlockStateDictionary($sortedStates),
			GlobalBlockStateHandlers::getSerializer()
		));
	}

	/**
	 * @return Closure[]
	 */
	public function getAsyncTransmitter() : array
	{
		return $this->asyncTransmitter;
	}

	public function getBlocks() : array
	{
		return $this->blocks;
	}

	public function getBlock(string $identifier) : ?BlockCustom{
		return $this->blocks[$identifier] ?? null;
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
