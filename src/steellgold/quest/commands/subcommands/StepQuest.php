<?php

namespace steellgold\quest\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use steellgold\quest\QT;

class StepQuest extends BaseSubCommand {

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

		QT::getInstance()->data[$args["quest"]]["steps_count"]++;
		$sender->sendMessage(str_replace(["{QUEST_NAME}","{STEP_COUNT}"],[QT::getInstance()->data[$args["quest"]]["name"],QT::getInstance()->data[$args["quest"]]["steps_count"]],QT::getInstance()->getConfig()->get("messages")["quest_step_added"]));
	}
}