<?php

namespace steellgold\quest\commands\subcommands;

use CortexPE\Commando\args\BooleanArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use steellgold\quest\QT;

class StartQuest extends BaseSubCommand {

	protected function prepare(): void {
		$this->registerArgument(0, new RawStringArgument("quest",false));
		$this->registerArgument(1, new RawStringArgument("player",false));
		$this->registerArgument(2, new BooleanArgument("force",true));

		$this->setPermission("quest.command");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!isset($args["force"])) $args["force"] = false;

		if(!$sender->hasPermission("quest.command")){
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["permission_error"]);
			return;
		}

		if(!isset(QT::getInstance()->data[$args["quest"]])){
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["quest_not_found"]);
			return;
		}

		if(key_exists($args["quest"], QT::getInstance()->players_data[$args["player"]])){
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["quest_already_started"]);
			return;
		}

		$i = 0;
		if(QT::getInstance()->players_data[$args["player"]] !== []){
			foreach (QT::getInstance()->players_data[$args["player"]] as $quest => $data){
				if($data["status"]) $i++;
			}
		}

		$force = false;
		if($args["force"]){
			$force = true;
		}

		if(count(QT::getInstance()->players_data[$args["player"]]) >= QT::getInstance()->max_quests){
			if($force){
				QT::getInstance()->players_data[$args["player"]][$args["quest"]] = [
					"status" => false,
					"step" => 0,
				];
				$sender->sendMessage(str_replace(["{PLAYER}","{QUEST_NAME}"],[$args["player"],$args["quest"]],QT::getInstance()->getConfig()->get("messages")["quest_max_force_started"]));
				return;
			}

			$sender->sendMessage(str_replace("{MAX}",QT::getInstance()->max_quests,QT::getInstance()->getConfig()->get("messages")["quest_max_started"]));
		}else{
			QT::getInstance()->players_data[$args["player"]][$args["quest"]] = [
				"status" => false,
				"step" => 0,
			];
			$sender->sendMessage(str_replace(["{QUEST_NAME}","{PLAYER}"], [QT::getInstance()->data[$args["quest"]]["name"],$args["player"]], QT::getInstance()->getConfig()->get("messages")["quest_start"]));
		}
	}
}