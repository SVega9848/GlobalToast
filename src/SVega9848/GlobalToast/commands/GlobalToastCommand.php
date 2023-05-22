<?php

declare(strict_types=1);

namespace SVega9848\GlobalToast\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\ToastRequestPacket;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use SVega9848\GlobalToast\Main;

class GlobalToastCommand extends Command implements PluginOwned {
	public function __construct(private Main $main) {
		parent::__construct("globaltoast", "Broadcast toast announcements to everyone in the server!");
		$this->setPermission("globaltoast.use");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		if (!$this->testPermission($sender)) {
			return TRUE;
		}

		if (empty($args)) {
			$sender->sendMessage(TextFormat::colorize("&7[&c!&7] &cYou must type something! Example: /globaltoast <message> [player]"));
			return TRUE;
		}

		$message = implode(" ", $args);

		if (isset($args[count($args) - 1])) {
			$targetName = array_pop($args);

			if (strtolower($targetName) === "everyone") {
				$targetPlayer = "everyone";
			} else {
				$targetPlayer = $this->main->getServer()->getPlayerExact($targetName);

				if ($targetPlayer === NULL) {
					$sender->sendMessage(TextFormat::colorize("&7[&c!&7] &cThe specified player is not online."));
					return TRUE;
				}
			}
		} elseif ($sender instanceof Player) {
			$targetPlayer = $sender;
		} else {
			$sender->sendMessage(TextFormat::colorize("&7[&c!&7] &cYou must specify a player when running this command from the console."));
			return TRUE;
		}

		$packet = ToastRequestPacket::create(
			TextFormat::colorize($this->main->getConfig()->get("toast-title")),
			TextFormat::colorize($message)
		);

		if ($targetPlayer === "everyone") {
			foreach ($this->main->getServer()->getOnlinePlayers() as $player) {
				$player->getNetworkSession()->sendDataPacket(clone $packet);
			}
		} else {
			$targetPlayer->getNetworkSession()->sendDataPacket($packet);
		}

		$sender->sendMessage(TextFormat::colorize("&7[&a!&7] &aToast has been sent."));

		return TRUE;
	}

	public function getOwningPlugin(): Plugin {
		return $this->main;
	}
}
