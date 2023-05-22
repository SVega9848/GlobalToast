<?php

declare(strict_types=1);

namespace SVega9848\GlobalToast;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use SVega9848\GlobalToast\commands\GlobalToastCommand;

class Main extends PluginBase implements Listener {
	public Config $config;

	public function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getCommandMap()->register("globaltoast", new GlobalToastCommand($this));
		$this->config = $this->getConfig();
	}
}
