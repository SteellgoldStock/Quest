<?php

namespace steellgold\quest\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use steellgold\quest\QT;

class DeleteQuest extends BaseSubCommand {

	protected function prepare(): void {
		$this->registerArgument(0, new RawStringArgument("name", false));

		$this->setPermission("quest.command");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$sender->hasPermission("quest.command")){
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["permission_error"]);
			return;
		}

		if(!key_exists($args["name"], QT::getInstance()->data)){
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["quest_not_found"]);
			return;
		}

		$sender->sendMessage(str_replace(["{QUEST_NAME}"], [QT::getInstance()->data[$args["name"]]["name"]], QT::getInstance()->getConfig()->get("messages")["quest_deleted"]));
		unset(QT::getInstance()->data[$args["name"]]);
		foreach (QT::getInstance()->players_data as $name => $data) {
			unset(QT::getInstance()->players_data[$name][$args["name"]]);
		}
	}
}