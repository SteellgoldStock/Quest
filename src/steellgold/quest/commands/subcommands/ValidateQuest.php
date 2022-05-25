<?php

namespace steellgold\quest\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use steellgold\quest\QT;

class ValidateQuest extends BaseSubCommand {

	protected function prepare(): void {
		$this->registerArgument(0, new RawStringArgument('quest', false));
		$this->registerArgument(1, new RawStringArgument('player', false));

		$this->setPermission("quest.command");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$sender->hasPermission("quest.command")){
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["permission_error"]);
			return;
		}

		if (!isset(QT::getInstance()->data[$args["quest"]])) {
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["quest_not_found"]);
			return;
		}

		if(!isset(QT::getInstance()->players_data[$args["player"]][$args["quest"]])){
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["quest_not_started"]);
			return;
		}

		if(!isset(QT::getInstance()->players_data[$args["player"]])) {
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["player_not_found"]);
			return;
		}

		if(QT::getInstance()->players_data[$args["player"]][$args["quest"]]["step"] == QT::getInstance()->data[$args["quest"]]["steps_count"]){
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["quest_already_validated"]);
			return;
		}

		$sender->sendMessage(str_replace(["{QUEST_NAME}", "{STEP}"], [QT::getInstance()->data[$args["quest"]]["name"], QT::getInstance()->players_data[$args["player"]][$args["quest"]]["step"]], QT::getInstance()->getConfig()->get("messages")["quest_validate"]));
		QT::getInstance()->players_data[$args["player"]][$args["quest"]]["step"]++;

		if(QT::getInstance()->players_data[$args["player"]][$args["quest"]]["step"] == QT::getInstance()->data[$args["quest"]]["steps_count"]){
			QT::getInstance()->players_data[$args["player"]][$args["quest"]]["status"] = true;
			$sender->sendMessage(str_replace("{QUEST_NAME}", QT::getInstance()->data[$args["quest"]]["name"], QT::getInstance()->getConfig()->get("messages")["quest_validated"]));
			return;
		}
	}
}