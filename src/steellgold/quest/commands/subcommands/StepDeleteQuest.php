<?php

namespace steellgold\quest\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use steellgold\quest\QT;

class StepDeleteQuest extends BaseSubCommand {

	protected function prepare(): void {
		$this->registerArgument(0, new RawStringArgument("quest", false));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$sender->hasPermission("quest.command")) {
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["permission_error"]);
			return;
		}

		if (!isset(QT::getInstance()->data[$args["quest"]])) {
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["quest_not_found"]);
			return;
		}

		if(QT::getInstance()->data[$args["quest"]]["steps_count"] == 0){
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["quest_no_steps"]);
			return;
		}

		QT::getInstance()->data[$args["quest"]]["steps_count"] = QT::getInstance()->data[$args["quest"]]["steps_count"] - 1;
		$sender->sendMessage(str_replace(["{STEP_COUNT}","{QUEST_NAME}"],[QT::getInstance()->data[$args["quest"]]["steps_count"],QT::getInstance()->data[$args["quest"]]["name"]],QT::getInstance()->getConfig()->get("messages")["quest_step_deleted"]));
	}
}