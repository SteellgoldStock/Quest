<?php

namespace steellgold\quest\forms;

use dktapps\pmforms\FormIcon;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use pocketmine\Server;
use steellgold\quest\QT;

class QuestForm {

	private array $quests;

	public static function questForm(string $player) : MenuForm {

		$quests = [];
		$quests_buttons_in = [];
		$quests_buttons_completed = [];
		$i = 0;
		foreach (QT::getInstance()->players_data[$player] as $quest => $quest_data) {
			$quests[$i] = $quest;
			$i++;

			if(isset(QT::getInstance()->getConfig()->get("images")[$quest])){
				if(QT::getInstance()->getConfig()->get("images")[$quest] !== null) {
					$i = QT::getInstance()->getConfig()->get("images")[$quest];
					$form_icon = new FormIcon($i["path"],$i["type"] == "path" ? FormIcon::IMAGE_TYPE_PATH : FormIcon::IMAGE_TYPE_URL);
				}else $form_icon = null;
			}else{
				$form_icon = null;
			}

			if(!$quest_data['status']) $quests_buttons_in[$quest] = new MenuOption(QT::getInstance()->data[$quest]["name"], $form_icon ?? null);
			else $quests_buttons_completed[$quest] = new MenuOption(QT::getInstance()->data[$quest]["name"], $form_icon ?? null);
		}

		return new MenuForm(
			str_replace("{PLAYER}", $player, QT::getInstance()->getConfig()->get("form")["title"]),
			QT::getInstance()->getConfig()->get("form")["description"],
			[new MenuOption(str_replace("{COUNT}",count($quests_buttons_in),QT::getInstance()->getConfig()->get("form")["button_in"])), new MenuOption(str_replace("{COUNT}",count($quests_buttons_completed),QT::getInstance()->getConfig()->get("form")["button_completed"]))],

			function(Player $submitter, int $selected) use ($quests, $quests_buttons_in, $quests_buttons_completed) : void{
				if($selected == 0) $submitter->sendForm(QuestForm::questStatusPage($submitter->getName(),"title_in","description_in",$quests_buttons_in));
				else $submitter->sendForm(QuestForm::questStatusPage($submitter->getName(),"title_completed","description_completed",$quests_buttons_completed));
			}
		);
	}

	public static function questStatusPage(string $player, string $title = "title_in", string $description = "description_in", array $quests = []) : MenuForm {
		return new MenuForm(
			str_replace("{PLAYER}", $player, QT::getInstance()->getConfig()->get("form")[$title]),
			str_replace("{PLAYER}", $player, QT::getInstance()->getConfig()->get("form")[$description]),
			$quests,

			function (Player $submitter, int $selected) use ($quests) : void {
				$i = 0;
				$questsList = [];
				foreach ($quests as $quest => $data){
					$questsList[$i] = [$quest, $data];
					$i++;
				}

				$submitter->sendForm(QuestForm::questPage($submitter->getName(),$questsList[$selected][0]));
			}
		);
	}

	public static function questPage(string $player, string $quest_name) : MenuForm {
		$quest = QT::getInstance()->data[$quest_name];
		$player_quest = QT::getInstance()->players_data[$player][$quest_name];

		$no_steps = QT::getInstance()->getConfig()->get("form")["step_text_none"];
		$steps = str_replace(["{STEP_COUNT}","{MAX_STEP_COUNT}"],[$player_quest["step"],$quest["steps_count"]],QT::getInstance()->getConfig()->get("form")["steps_text"]);

		$text = QT::getInstance()->data[$quest_name]["steps_count"] > 0 ? $steps : $no_steps;

		return new MenuForm(
			str_replace("{PLAYER}",$player,QT::getInstance()->getConfig()->get("form")["title"]),
			str_replace(["{NAME}","{STEP_TEXT}","{DESCRIPTION}"],[$quest["name"] . (Server::getInstance()->isOp($player) ? " (§e{$quest_name}§f)" : ""),$text,QT::getInstance()->data[$quest_name]["description"]],QT::getInstance()->getConfig()->get("form")["description_quest_page"]), [
				new MenuOption("Retour", new FormIcon("textures/blocks/barrier", FormIcon::IMAGE_TYPE_PATH)),
			],

			function (Player $submitter, int $selected) : void {
				if($selected == 0) $submitter->sendForm(QuestForm::questForm($submitter->getName()));
			}
		);
	}
}