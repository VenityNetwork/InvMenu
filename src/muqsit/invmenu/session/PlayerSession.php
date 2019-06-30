<?php

/*
 *  ___            __  __
 * |_ _|_ ____   _|  \/  | ___ _ __  _   _
 *  | || '_ \ \ / / |\/| |/ _ \ '_ \| | | |
 *  | || | | \ V /| |  | |  __/ | | | |_| |
 * |___|_| |_|\_/ |_|  |_|\___|_| |_|\__,_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Muqsit
 * @link http://github.com/Muqsit
 *
*/

declare(strict_types=1);

namespace muqsit\invmenu\session;

use muqsit\invmenu\InvMenu;
use pocketmine\network\mcpe\protocol\NetworkStackLatencyPacket;
use pocketmine\player\Player;

class PlayerSession{

	/** @var Player */
	protected $player;

	/** @var MenuExtradata */
	protected $menu_extradata;

	/** @var InvMenu|null */
	protected $current_menu;

	/** @var int|null */
	protected $notification_id;

	public function __construct(Player $player){
		$this->player = $player;
		$this->menu_extradata = new MenuExtradata();
	}

	public function getMenuExtradata() : MenuExtradata{
		return $this->menu_extradata;
	}

	/**
	 * @internal use InvMenu::send() instead.
	 * @param InvMenu|null $menu
	 */
	public function setCurrentMenu(?InvMenu $menu) : void{
		$this->current_menu = $menu;
		$this->notification_id = time() * 1000; // TODO: remove the x1000 hack when fixed

		$pk = new NetworkStackLatencyPacket();
		$pk->timestamp = $this->notification_id;
		$pk->needResponse = true;
		$this->player->sendDataPacket($pk);
	}

	public function notify(int $notification_id) : void{
		if($notification_id === $this->notification_id){
			$this->notification_id = null;
			$this->current_menu->sendInventory($this->player, $this->menu_extradata);
		}
	}

	public function getCurrentMenu() : ?InvMenu{
		return $this->current_menu;
	}

	/**
	 * @internal use Player::removeCurrentWindow() instead
	 * @return void
	 */
	public function removeCurrentMenu() : void{
		$this->current_menu = null;
		$this->notification_id = null;
	}
}
