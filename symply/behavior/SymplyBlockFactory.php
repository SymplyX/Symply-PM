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
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\inventory\CreativeInventory;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\world\format\io\GlobalBlockStateHandlers;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use symply\behavior\blocks\IBlockCustom;
use symply\behavior\blocks\IPermutationBlock;
use function assert;
use function mb_strtoupper;

final class SymplyBlockFactory
{

	private static ?SymplyBlockFactory $instance = null;

	/** @var array<string, IBlockCustom> */
	private array $blockCustoms = [];

	private array $blocksOverwrite = [];

	/** @var BlockPaletteEntry[] */
	private array $blockPaletteEntries = [];

	/** @var ThreadSafeArray<ThreadSafeArray<Closure>> */
	private ThreadSafeArray $asyncTransmitterBlockCustom;

	/** @var ThreadSafeArray<ThreadSafeArray<Closure>> */
	private ThreadSafeArray $asyncTransmitterBlockOverwrite;

	public function __construct(private readonly bool $asyncMode = false)
	{
		if (!$this->asyncMode) {
			$this->asyncTransmitterBlockCustom = new ThreadSafeArray();
			$this->asyncTransmitterBlockOverwrite = new ThreadSafeArray();
		}
	}
	/**
	 * @param Closure(): Block&IBlockCustom $blockClosure
	 */
	public function register(Closure $blockClosure, ?Closure $serializer = null, ?Closure $deserializer = null) : void
	{
		/** @var Block&IBlockCustom $blockCustom */
		$blockCustom = $blockClosure();
		$identifier = $blockCustom->getIdInfo()->getNamespaceId();
		if (isset($this->blockCustoms[$identifier])) {
			throw new InvalidArgumentException("Block ID {$blockCustom->getIdInfo()->getNamespaceId()} is already used by another block");
		}
		$blockBuilder = $blockCustom->getBlockBuilder();
		RuntimeBlockStateRegistry::getInstance()->register($blockCustom);
		$blockIdVanilla = SymplyItemFactory::getInstance()->registerBlockItem($identifier, $blockCustom);
		$this->blockCustoms[$identifier] = $blockCustom;
		if ($blockCustom instanceof IPermutationBlock) {
			$serializer ??= static function (Block&IPermutationBlock $block) use ($identifier) : BlockStateWriter {
				$writer = BlockStateWriter::create($identifier);
				$block->serializeState($writer);
				return $writer;
			};
			$deserializer ??= static function (BlockStateReader $reader) use ($identifier) : Block {
				/**
				 * @var Block&IPermutationBlock $block
				 */
				$block = clone SymplyBlockFactory::getInstance()->getBlockCustom($identifier);
				$block->deserializeState($reader);
				return $block;
			};
		} else {
			$serializer ??= static fn() => BlockStateWriter::create($identifier);
			$deserializer ??= static fn(BlockStateReader $reader) => $blockCustom;
		}
		foreach ($blockBuilder->toBlockStateDictionaryEntry() as $blockStateDictionaryEntry){
			SymplyBlockPalette::getInstance()->insertState($blockStateDictionaryEntry);
			GlobalBlockStateHandlers::getUpgrader()->getBlockIdMetaUpgrader()->addIdMetaToStateMapping($identifier, $blockStateDictionaryEntry->getMeta(), $blockStateDictionaryEntry->generateStateData());
		}
		GlobalBlockStateHandlers::getSerializer()->map($blockCustom, $serializer);
		GlobalBlockStateHandlers::getDeserializer()->map($identifier, $deserializer);
		$item = $blockCustom->asItem();
		CreativeInventory::getInstance()->add($item);
		if (!$this->asyncMode) {
			$this->asyncTransmitterBlockCustom[] = ThreadSafeArray::fromArray([$blockClosure, $serializer, $deserializer]);
			$this->blockPaletteEntries[] = new BlockPaletteEntry($identifier, new CacheableNbt($blockBuilder->toPacket($blockIdVanilla)));
		}
	}

	/**
	 * @param Closure $blockClosure
	 * @param Closure|false|null $serializer
	 * @param Closure|false|null $deserializer
	 * @throws ReflectionException
	 */
	public function overwriteBlockPMMP(Closure $blockClosure,  null|Closure|false $serializer = null, null|Closure|false $deserializer = null) : void
	{
		/**
		 * @var Block $block
		 */
		$block = $blockClosure();
		$runtimeBlockStateRegistry = RuntimeBlockStateRegistry::getInstance();
		try {
			$runtimeBlockStateRegistry->register($block);
		} catch (InvalidArgumentException) {
			$typeIndexProperty = new ReflectionProperty($runtimeBlockStateRegistry, "typeIndex");
			$value = $typeIndexProperty->getValue($runtimeBlockStateRegistry);
			$value[$block->getTypeId()] = $block;
			$typeIndexProperty->setValue($runtimeBlockStateRegistry, $value);

			$fillStaticArraysMethod = new ReflectionMethod($runtimeBlockStateRegistry, "fillStaticArrays");
			foreach ($block->generateStatePermutations() as $v) {
				$fillStaticArraysMethod->invoke($runtimeBlockStateRegistry, $v->getStateId(), $v);
			}
		}

		try {
			$vanillaBlocksNoConstruct = (new \ReflectionClass(VanillaBlocks::class))->newInstanceWithoutConstructor();
			$name = null;
			foreach (VanillaBlocks::getAll() as $index => $vanillaBlock) {
				if ($block->getTypeId() === $vanillaBlock->getTypeId()) {
					$name = $index;
					break;
				}
			}
			if (!$name)
				return;
			(function () use ($block, $name) {
				self::verifyName($name);
				$upperName = mb_strtoupper($name);
				self::$members[$upperName] = $block;
			})->call($vanillaBlocksNoConstruct);
		} catch (\Throwable) {

		}
		$namespaceId = GlobalBlockStateHandlers::getSerializer()->serializeBlock($block)->getName();
		CreativeInventory::getInstance()->remove($block->asItem());
		$this->blocksOverwrite[$namespaceId] = $block;
		CreativeInventory::getInstance()->add($block->asItem());

		$serializer ??= static fn() => BlockStateWriter::create($namespaceId);
		$deserializer ??= static function () use ($namespaceId) {
			$block = SymplyBlockFactory::getInstance()->getBlockOverwrite($namespaceId);
			assert($block instanceof Block);
			return (clone $block);
		};
		$instanceDeserializer = GlobalBlockStateHandlers::getDeserializer();
		$instanceSerializer = GlobalBlockStateHandlers::getSerializer();
		if ($deserializer !== false) {
			try {
				$instanceDeserializer->map($namespaceId, $deserializer);
			} catch (InvalidArgumentException) {
				$deserializerProperty = new ReflectionProperty($instanceDeserializer, "deserializeFuncs");
				$value = $deserializerProperty->getValue($instanceDeserializer);
				$value[$namespaceId] = $deserializer;
				$deserializerProperty->setValue($instanceDeserializer, $value);
			}
		}
		if ($serializer !== false) {
			try {
				$instanceSerializer->map($block, $serializer);
			} catch (InvalidArgumentException) {
				$serializerProperty = new ReflectionProperty($instanceSerializer, "serializers");
				$value = $serializerProperty->getValue($instanceSerializer);
				$value[$block->getTypeId()] = $serializer;
				$serializerProperty->setValue($instanceSerializer, $value);
			}
		}
		if (!$this->asyncMode)
			$this->asyncTransmitterBlockOverwrite[] = ThreadSafeArray::fromArray([$blockClosure, $serializer, $deserializer]);
	}

	public function getBlockPaletteEntries() : array
	{
		return $this->blockPaletteEntries;
	}

	/**
	 * @return ThreadSafeArray<ThreadSafeArray<Closure>>
	 */
	public function getAsyncTransmitterBlockCustom() : ThreadSafeArray
	{
		return $this->asyncTransmitterBlockCustom;
	}

	public function getAsyncTransmitterBlockOverwrite() : ThreadSafeArray
	{
		return $this->asyncTransmitterBlockOverwrite;
	}

	/**
	 * @return Block&IBlockCustom[]
	 */
	public function getBlockCustoms() : array
	{
		return $this->blockCustoms;
	}

	/**
	 * @return null|Block&IBlockCustom
	 */
	public function getBlockCustom(string $identifier) : IBlockCustom|null
	{
		return $this->blockCustoms[$identifier] ?? null;
	}

	public function getBlocksOverwrite() : array
	{
		return $this->blocksOverwrite;
	}

	public function getBlockOverwrite(string $id) : Block{
		return $this->blocksOverwrite[$id];
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
