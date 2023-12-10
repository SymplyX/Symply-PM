<?php

namespace symply\utils;

use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\types\DeviceOS;

class PacketUtils
{
	/**
	 * @var int[]
	 */
	public static array $disabledPackets = [
		ProtocolInfo::MOVE_ACTOR_ABSOLUTE_PACKET,
		ProtocolInfo::MOB_ARMOR_EQUIPMENT_PACKET,
		ProtocolInfo::SET_ACTOR_DATA_PACKET,
		ProtocolInfo::SPAWN_EXPERIENCE_ORB_PACKET,
		ProtocolInfo::MAP_INFO_REQUEST_PACKET,
		ProtocolInfo::COMMAND_BLOCK_UPDATE_PACKET,
		ProtocolInfo::STRUCTURE_BLOCK_UPDATE_PACKET,
		ProtocolInfo::PURCHASE_RECEIPT_PACKET,
		ProtocolInfo::SUB_CLIENT_LOGIN_PACKET,
		ProtocolInfo::CLIENT_CACHE_BLOB_STATUS_PACKET,
		ProtocolInfo::ANVIL_DAMAGE_PACKET,
		ProtocolInfo::EMOTE_LIST_PACKET,
		ProtocolInfo::SUB_CHUNK_REQUEST_PACKET,
	];

	public const TITLE_ID_TO_DEVICE = [
		"896928775" => DeviceOS::WINDOWS_10,
		"2047319603" => DeviceOS::NINTENDO,
		"1739947436" => DeviceOS::ANDROID,
		"2044456598" => DeviceOS::PLAYSTATION,
		"1828326430" => DeviceOS::XBOX,
		"1810924247" => DeviceOS::IOS,
	];
}