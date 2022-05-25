<?php

namespace steellgold\quest\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use steellgold\quest\QT;

class DescriptionQuest extends BaseSubCommand {

	/**
	 * @throws ArgumentOrderException
	 */
	protected function prepare(): void {
		$this->registerArgument(0, new RawStringArgument("quest", false));
		$this->registerArgument(1, new TextArgument("description", false));

		$this->setPermission("quest.command");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if (!$sender->hasPermission("quest.command")) {
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["permission_error"]);
			return;
		}

		if (!isset(QT::getInstance()->data[$args["quest"]])) {
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["quest_not_found"]);
			return;
		}

		QT::getInstance()->data[$args["quest"]]["description"] = $args["description"];
		$sender->sendMessage(str_replace(["{QUEST_NAME}","{DESCRIPTION}"],[QT::getInstance()->data[$args["quest"]]["name"],$args["description"]],QT::getInstance()->getConfig()->get("messages")["quest_description_set"]));
	}
}