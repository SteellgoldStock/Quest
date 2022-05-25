<?php

namespace steellgold\quest\commands\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use steellgold\quest\QT;

class ListQuests extends BaseSubCommand{

	/**
	 * @throws ArgumentOrderException
	 */
	protected function prepare(): void {
		$this->setPermission("quest.command");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		$i = 1;
		$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["quest_list_header"]);
		if(count(QT::getInstance()->data) > 0){
			foreach (QT::getInstance()->data as $quest => $data) {
				$sender->sendMessage(str_replace([
					"{NUMBER}",
					"{QUEST_NAME}",
					"{QUEST_ID}"
				], [
					$i,
					$data["name"],
					$quest
				],
					QT::getInstance()->getConfig()->get("messages")["quest_list_line"]
				));
				$i++;
			}
		}else{
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["quest_list_empty"]);
		}
		$sender->sendMessage(str_replace("{COUNT}",count(QT::getInstance()->data),QT::getInstance()->getConfig()->get("messages")["quest_list_footer"]));
	}
}