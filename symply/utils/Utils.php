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

use pocketmine\resourcepacks\ResourcePackException;
use function array_map;
use function array_product;
use function count;
use function curl_close;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt;
use function current;
use function next;
use function preg_match;
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
		$data = curl_exec($ch);
		$fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
		$httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpResponseCode != 200){
			throw new ResourcePackException("Unable to retrieve the size of the resource pack.");
		}
		return (int) ($fileSize ?? 0);
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
