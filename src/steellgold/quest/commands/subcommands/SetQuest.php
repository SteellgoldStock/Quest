<?php

namespace steellgold\quest\commands\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use steellgold\quest\QT;

class SetQuest extends BaseSubCommand {

	protected function prepare(): void {
		$this->registerArgument(0, new RawStringArgument("quest", false));
		$this->registerArgument(1, new RawStringArgument("player", false));
		$this->registerArgument(2, new IntegerArgument("step", false));

		$this->setPermission("quest.command");
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

		if(!isset(QT::getInstance()->players_data[$args["player"]])) {
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["player_not_found"]);
			return;
		}

		if(!isset(QT::getInstance()->players_data[$args["player"]][$args["quest"]])){
			$sender->sendMessage(str_replace("{PLAYER}", $args["player"], QT::getInstance()->getConfig()->get("messages")["quest_player_not_found"]));
			return;
		}

		if(QT::getInstance()->players_data[$args["player"]][$args["quest"]]["step"] == $args["step"]) {
			$sender->sendMessage(str_replace(["{PLAYER}", "{STEP}", "{QUEST_NAME}"], [$args["player"],$args["step"],QT::getInstance()->data[$args["quest"]]["name"]], QT::getInstance()->getConfig()->get("messages")["player_already_step"]));
			return;
		}

		if($args["step"] > QT::getInstance()->data[$args["quest"]]["steps_count"]) {
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["quest_step_high"]);
			return;
		}

		if(QT::getInstance()->players_data[$args["player"]][$args["quest"]]["step"] > $args["step"]) {
			$sender->sendMessage(QT::getInstance()->getConfig()->get("messages")["quest_step_higher"]);
			return;
		}

		QT::getInstance()->players_data[$args["player"]][$args["quest"]]["step"] = $args["step"];
		$sender->sendMessage(str_replace(["{PLAYER}", "{STEP}", "{QUEST_NAME}"], [$args["player"],$args["step"],QT::getInstance()->data[$args["quest"]]["name"]], QT::getInstance()->getConfig()->get("messages")["quest_set_step"]));
	}
}