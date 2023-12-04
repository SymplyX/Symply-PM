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

namespace symply\utils;

use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\resourcepacks\ResourcePackException;
use function array_keys;
use function array_map;
use function array_product;
use function count;
use function curl_close;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt;
use function current;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function next;
use function preg_match;
use function range;
use function reset;
use const CURLINFO_CONTENT_LENGTH_DOWNLOAD;
use const CURLINFO_HTTP_CODE;
use const CURLOPT_HEADER;
use const CURLOPT_NOBODY;
use const CURLOPT_RETURNTRANSFER;

class Utils
{
	private const URL_PATTERN = '/^(https?|ftp):\/\/[^\s\/$.?#].[^\s]*$/i';
	static public function isUrl(string $path) : bool
	{
		return (preg_match(self::URL_PATTERN, $path) != 0);
	}

	/**
	 * @throws ResourcePackException
	 */
	static public function getSizeOfResourcesPack(string $url) : int
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_NOBODY, TRUE);
		curl_exec($ch);
		$fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
		$httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpResponseCode != 200){
			throw new ResourcePackException("Unable to retrieve the size of the resource pack.");
		}
		return (int) ($fileSize ?? 0);
	}

	/**
	 * Attempts to return the correct Tag for the provided type.
	 */
	public static function getTagType($type) : ?Tag {
		return match (true) {
			is_array($type) => self::getArrayTag($type),
			is_bool($type) => new ByteTag($type ? 1 : 0),
			is_float($type) => new FloatTag($type),
			is_int($type) => new IntTag($type),
			is_string($type) => new StringTag($type),
			$type instanceof CompoundTag => $type,
			default => null,
		};
	}

	private static function getArrayTag(array $array) : Tag {
		if(array_keys($array) === range(0, count($array) - 1)) {
			return new ListTag(array_map(fn($value) => self::getTagType($value), $array));
		}
		$tag = CompoundTag::create();
		foreach($array as $key => $value){
			$tag->setTag($key, self::getTagType($value));
		}
		return $tag;
	}

	public static function getCartesianProduct(array $arrays) : array {
		$result = [];
		$count = count($arrays) - 1;
		$combinations = array_product(array_map(static fn(array $array) => count($array), $arrays));
		for($i = 0; $i < $combinations; $i++){
			$result[] = array_map(static fn(array $array) => current($array), $arrays);
			for($j = $count; $j >= 0; $j--){
				if(next($arrays[$j])) {
					break;
				}
				reset($arrays[$j]);
			}
		}
		return $result;
	}
}
