<?php

namespace steellgold\quest\commands\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use steellgold\quest\QT;

class CreateQuest extends BaseSubCommand {

	/**
	 * @throws ArgumentOrderException
	 */
	protected function prepare(): void {
		$this->registerArgument(0, new TextArgument("name",false));

		$this->setPermission("quest.command");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$sender->hasPermission("quest.command")){
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["permission_error"]);
			return;
		}

		$identifier = QT::getInstance()->quest_identifier_type ? mt_rand(999,9999) : strtolower(str_replace([" "],["_"],$args["new_name"]));
		if(key_exists($identifier,QT::getInstance()->data)){
			$sender->sendMessage(str_replace("{QUEST_NAME}",$identifier,QT::getInstance()->getConfig()->get("messages")["quest_exists"]));
			return;
		}

		QT::getInstance()->data[$identifier] = ["steps_count" => 0, "name" => $args["name"],"description" => QT::getInstance()->getConfig()->get("default_quest_description")];
		$sender->sendMessage(str_replace(["{QUEST_NAME}","{QUEST_ID}"],[$args["name"],$identifier],QT::getInstance()->getConfig()->get("messages")["quest_created"]));
	}
}