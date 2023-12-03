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

namespace symply\resourcepacks;

use pocketmine\resourcepacks\ResourcePack;

class URLResourcePack implements ResourcePack
{
	protected string $realName;
	public function __construct(
		protected string $name,
		protected string $uuid,
		protected string $version,
		protected string $url,
		protected int $size,
		private string $encryptionKey = ""
	) {
	   $this->realName = "{$this->uuid}_{$this->version}";
	}

	public function getRealName() : string
	{
		return $this->realName;
	}

	public function getPackName() : string
	{
		return $this->name;
	}

	public function getPackId() : string
	{
		return $this->uuid;
	}

	public function getPackSize() : int
	{
		return $this->size;
	}

	public function getPackVersion() : string
	{
		return $this->version;
	}

	public function getSha256() : string
	{
		return "";
	}

	/**
	 * @throws InvalidPackChunkURLException
	 */
	public function getPackChunk(int $start, int $length) : string
	{
		throw new InvalidPackChunkURLException("Invalid URL for pack chunk in {$this->getPackId()}. Unable to use the getPackChunk method.");
	}

	public function getUrl() : string{
		return $this->url;
	}

	public function getEncryptionKey() : string
	{
		return $this->encryptionKey;
	}
	public function hasEncryptionKey() : bool
	{
		return $this->encryptionKey !== "";
	}
	public function getContentId() : string
	{
		return $this->hasEncryptionKey() ? $this->getPackId() : "";
	}

	public function setEncryptionKey(string $key) : void
	{
		$this->encryptionKey = $key;
	}
}
