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
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\inventory\CreativeInventory;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use ReflectionException;
use symply\behavior\block\IBlockCustom;
use symply\behavior\block\IPermutationBlock;
use function assert;

final class SymplyBlockFactory
{

	private static ?SymplyBlockFactory $instance = null;

	/** @var array<string, IBlockCustom> */
	private array $blocks = [];

	/** @var BlockPaletteEntry[] */
	private array $blockPaletteEntries = [];

	/** @var ThreadSafeArray<ThreadSafeArray<Closure>> */
	private ThreadSafeArray $asyncTransmitter;

	public function __construct(private readonly bool $asyncMode = false)
	{
		$this->asyncTransmitter = new ThreadSafeArray();
	}
	/**
	 * @param Closure(): Block&IBlockCustom $blockClosure
	 */
	public function register(Closure $blockClosure, ?Closure $serializer = null, ?Closure $deserializer = null) : void
	{
		/** @var Block&IBlockCustom $blockCustom */
		$blockCustom = $blockClosure();
		$identifier = $blockCustom->getIdInfo()->getNamespaceId();
		if (isset($this->blocks[$identifier])) {
			throw new InvalidArgumentException("Block ID {$blockCustom->getIdInfo()->getNamespaceId()} is already used by another block");
		}
		$blockBuilder = $blockCustom->getBlockBuilder();
		RuntimeBlockStateRegistry::getInstance()->register($blockCustom);
		SymplyItemFactory::getInstance()->registerBlockItem($identifier, $blockCustom);
		$this->blocks[$identifier] = $blockCustom;
		if ($blockCustom instanceof IPermutationBlock) {
			$serializer ??= static function (Block&IPermutationBlock $block) : BlockStateWriter {
				$writer = BlockStateWriter::create($block->getIdInfo()->getNamespaceId());
				$block->serializeState($writer);
				return $writer;
			};
			$deserializer ??= static function (BlockStateReader $reader) use ($identifier) : Block {
				$block = SymplyBlockFactory::getInstance()->getBlock($identifier);
				assert($block instanceof IPermutationBlock);
				$block->deserializeState($reader);
				return $block;
			};
		} else {
			$serializer ??= static fn() => BlockStateWriter::create($blockCustom->getIdInfo()->getNamespaceId());
			$deserializer ??= static fn(BlockStateReader $reader) => $blockCustom;
		}
		$oldId = $blockCustom->getIdInfo()->getOldId();
		foreach ($blockBuilder->toBlockStateDictionaryEntry() as $blockStateDictionaryEntry){
			SymplyBlockPalette::getInstance()->insertState($blockStateDictionaryEntry);
			GlobalBlockStateHandlers::getUpgrader()->getBlockIdMetaUpgrader()->addIdMetaToStateMapping($identifier, $blockStateDictionaryEntry->getMeta(), $blockStateDictionaryEntry->generateStateData());
			GlobalBlockStateHandlers::getUpgrader()->getBlockIdMetaUpgrader()->addIntIdToStringIdMapping($oldId, $identifier);
		}
		GlobalBlockStateHandlers::getSerializer()->map($blockCustom, $serializer);
		GlobalBlockStateHandlers::getDeserializer()->map($identifier, $deserializer);
		$item = $blockCustom->asItem();
		CreativeInventory::getInstance()->add($item);
		if (!$this->asyncMode) {
			$this->asyncTransmitter[] = ThreadSafeArray::fromArray([$blockClosure, $serializer, $deserializer]);
			$this->blockPaletteEntries[] = new BlockPaletteEntry($identifier, new CacheableNbt($blockBuilder->toPacket()));
		}
	}

	public function getBlockPaletteEntries() : array
	{
		return $this->blockPaletteEntries;
	}

	/**
	 * @return ThreadSafeArray<ThreadSafeArray<Closure>>
	 */
	public function getAsyncTransmitter() : ThreadSafeArray
	{
		return $this->asyncTransmitter;
	}

	/**
	 * @return Block&IBlockCustom[]
	 */
	public function getBlocks() : array
	{
		return $this->blocks;
	}

	/**
	 * @return null|Block&IBlockCustom
	 */
	public function getBlock(string $identifier) : IBlockCustom|null
	{
		return $this->blocks[$identifier] ?? null;
	}

	/**
	 * @throws ReflectionException
	 */
	public static function getInstanceModeAsync() : self
	{
		return self::getInstance(true);
	}

	public static function getInstance(bool $asyncMode = false) : self
	{
		if (self::$instance === null) {
			self::$instance = new self($asyncMode);
		}
		return self::$instance;
	}

	public static function setInstance(self $instance) : void
	{
		self::$instance = $instance;
	}

	public static function reset() : void
	{
		self::$instance = null;
	}
}
