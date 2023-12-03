<?php

namespace pocketmine\ranks;

class Rank {
	public function __construct(
		protected string $token,
		protected string $chat,
		protected string $nameTag,
	) {}

	public function getToken() : string{
		return $this->token;
	}

	public function setToken(string $token) : void{
		$this->token = $token;
	}

	public function getChat() : string{
		return $this->chat;
	}

	public function setChat(string $chat) : void{
		$this->chat = $chat;
	}

	public function getNameTag() : string{
		return $this->nameTag;
	}

	public function setNameTag(string $nameTag) : void{
		$this->nameTag = $nameTag;
	}
}