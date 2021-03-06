<?php

/*

	$$\      $$\                     $$\       $$\        
	$$ | $\  $$ |                    $$ |      $$ |       
	$$ |$$$\ $$ | $$$$$$\   $$$$$$\  $$ | $$$$$$$ |       
	$$ $$ $$\$$ |$$  __$$\ $$  __$$\ $$ |$$  __$$ |       
	$$$$  _$$$$ |$$ /  $$ |$$ |  \__|$$ |$$ /  $$ |       
	$$$  / \$$$ |$$ |  $$ |$$ |      $$ |$$ |  $$ |       
	$$  /   \$$ |\$$$$$$  |$$ |      $$ |\$$$$$$$ |       
	\__/     \__| \______/ \__|      \__| \_______|       
	                                                      
	                                                      
	                                                      
	$$$$$$$\  $$\                 $$\                 $$\ 
	$$  __$$\ \__|                $$ |                $$ |
	$$ |  $$ |$$\ $$$$$$$\   $$$$$$$ | $$$$$$\   $$$$$$$ |
	$$$$$$$\ |$$ |$$  __$$\ $$  __$$ |$$  __$$\ $$  __$$ |
	$$  __$$\ $$ |$$ |  $$ |$$ /  $$ |$$$$$$$$ |$$ /  $$ |
	$$ |  $$ |$$ |$$ |  $$ |$$ |  $$ |$$   ____|$$ |  $$ |
	$$$$$$$  |$$ |$$ |  $$ |\$$$$$$$ |\$$$$$$$\ \$$$$$$$ |
	\_______/ \__|\__|  \__| \_______| \_______| \_______|
	                                                      
	                                                      
	                                                      
	 $$$$$$\  $$\                                         
	$$  __$$\ $$ |                                        
	$$ /  \__|$$$$$$$\   $$$$$$\   $$$$$$\   $$$$$$$\     
	\$$$$$$\  $$  __$$\ $$  __$$\ $$  __$$\ $$  _____|    
	 \____$$\ $$ |  $$ |$$ /  $$ |$$ /  $$ |\$$$$$$\      
	$$\   $$ |$$ |  $$ |$$ |  $$ |$$ |  $$ | \____$$\     
	\$$$$$$  |$$ |  $$ |\$$$$$$  |$$$$$$$  |$$$$$$$  |    
	 \______/ \__|  \__| \______/ $$  ____/ \_______/     
	                              $$ |                    
	                              $$ |                    
	                              \__|                    

	@ClouriaNetwork | GNU General Public License v2.1

*/

declare(strict_types=1);
namespace Clouria\WorldBindedShops;

use pocketmine\{
	Server,
	level\Level,
	tile\Spawnable,
	nbt\tag\CompoundTag,
	item\ItemFactory
};

use Clouria\WorldBindedShops\custom\CustomItemDisplayer;

class ShopTile extends Spawnable {

	private $data;

	public function __construct(Level $level, CompoundTag $nbt, array $data = null) {
		if (isset($data)) $this->data = $data;
		parent::__construct($level, $nbt);
	}

	protected function readSaveData(CompoundTag $nbt) : void {
		$data[0] = $this->x;
		$data[1] = $this->y;
		$data[2] = $this->z;
		$data[3] = $this->level->getFolderName();
		if ($nbt->hasTag('item')) $data[4] = $nbt->getShort('item');
		if ($nbt->hasTag('meta')) $data[5] = $nbt->getInt('meta');
		if ($nbt->hasTag('itemName')) $data[6] = $nbt->getString('itemName');
		if ($nbt->hasTag('amount')) $data[7] = $nbt->getInt('amount');
		if ($nbt->hasTag('price')) $data[8] = $nbt->getDouble('price');
		if ($nbt->hasTag('side')) $data[9] = $nbt->getByte('side');
		$this->data = $data;
	}

	protected function writeSaveData(CompoundTag $nbt) : void {
		$nbt->setShort('item', (int)($this->data['item'] ?? $this->data[4]));
		$nbt->setInt('meta', (int)($this->data['meta'] ?? $this->data[5]));
		$nbt->setString('itemName', (string)($this->data['itemName'] ?? $this->data[6]));
		$nbt->setInt('amount', (int)($this->data['amount'] ?? $this->data[7]));
		$nbt->setDouble('price', (float)$this->data['price'] ?? $this->data[8]);
		$nbt->setByte('side', (int)($this->data['side'] ?? $this->data[9]));
	}

	public function asItemDisplayer() : CustmomItemDisplayer {
		$shop = $this->data;
		$level = $shop['level'] ?? $shop[3];
		if(!isset($levels[$level])) $levels[$level] = $this->getServer()->getLevelByName($level);
		$display = $pos;
		if(isset($shop[9]) && $shop[9] !== -1) $display = $pos->getSide($shop[9]);

		return new CustomItemDisplayer($display, ItemFactory::get((int)($shop['item'] ?? $shop[4]), (int)($shop['meta'] ?? $shop[5]), (int)($shop['amount'] ?? $shop[7])), $pos);
	}

}