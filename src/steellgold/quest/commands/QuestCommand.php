<?php

namespace steellgold\quest\commands;

use CortexPE\Commando\args\TargetArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use steellgold\quest\commands\subcommands\CopyQuest;
use steellgold\quest\commands\subcommands\CreateQuest;
use steellgold\quest\commands\subcommands\DeleteQuest;
use steellgold\quest\commands\subcommands\DescriptionQuest;
use steellgold\quest\commands\subcommands\EditQuest;
use steellgold\quest\commands\subcommands\ListQuests;
use steellgold\quest\commands\subcommands\SetQuest;
use steellgold\quest\commands\subcommands\StartQuest;
use steellgold\quest\commands\subcommands\StepDeleteQuest;
use steellgold\quest\commands\subcommands\StepQuest;
use steellgold\quest\commands\subcommands\ValidateQuest;
use steellgold\quest\forms\QuestForm;
use steellgold\quest\QT;

class QuestCommand extends BaseCommand {

	public function __construct(Plugin $plugin) {
		parent::__construct($plugin, "quest", "Quest Command", []);
	}

	/**
	 * @throws ArgumentOrderException
	 */
	protected function prepare(): void {
		$this->registerSubCommand(new CreateQuest("create", "Créer une nouvelle quête"));
		$this->registerSubCommand(new DeleteQuest("delete", "List avaible quests"));
		$this->registerSubCommand(new ListQuests("list", "Liste les quêtes du serveur"));
		$this->registerSubCommand(new EditQuest("edit", "Edité une quête"));
		$this->registerSubCommand(new CopyQuest("copy", "Copier une quête existante sur un autre nom"));
		$this->registerSubCommand(new StepQuest("step", "Modifier une étape de quête"));
		$this->registerSubCommand(new StepDeleteQuest("stepdelete", "List avaible quests"));
		$this->registerSubCommand(new StartQuest("start", "List avaible quests"));
		$this->registerSubCommand(new ValidateQuest("validate", "List avaible quests"));
		$this->registerSubCommand(new SetQuest("set", "List avaible quests"));
		$this->registerSubCommand(new DescriptionQuest("description", "Edit description of quest"));

		$this->registerArgument(0, new TargetArgument("player", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		$sender = Server::getInstance()->getPlayerByPrefix($sender->getName());
		if(!isset($args["player"])){
			$sender->sendForm(QuestForm::questForm($sender->getName()));
			return;
		}

		if ($args["player"] !== $sender->getName()) {
			if ($sender->hasPermission("quest.command")) {
				if (key_exists($args["player"], QT::getInstance()->players_data)) {
					QuestForm::questForm($args["player"]);
				}else{
					$sender->sendMessage(str_replace("{PLAYER}", $args["player"], QT::getInstance()->getConfig()->get("messages")["quest_player_not_found"]));
				}
			}else{
				if((boolean)QT::getInstance()->getConfig()->get("players_can_see_others_player_quests")){
					$sender->sendForm(QuestForm::questForm($args["player"]));
				}
			}
		}else{
			$sender->sendForm(QuestForm::questForm($sender->getName()));
		}
	}
}