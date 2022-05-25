<?php

namespace steellgold\quest;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use JsonException;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use steellgold\quest\commands\QuestCommand;

class QT extends PluginBase {

	public static QT $instance;

	public array $data;

	public array $players_data;

	public int $max_quests;

	/**
	 * @var bool
	 * true INTEGER
	 * false STRING
	 */
	public bool $quest_identifier_type;

	/**
	 * @throws HookAlreadyRegistered
	 */
	protected function onEnable(): void {
		self::$instance = $this;
		$this->saveResource("config.yml");

		$this->quest_identifier_type = $this->getConfig()->get("quest_identifier_type") == "integer";

		$this->data = [];
		$this->players_data = [];
		$this->max_quests = $this->getConfig()->get("max_quests",1);

		PermissionManager::getInstance()->addPermission(new Permission("quest.command", "Allows the user to use /quest"));

		foreach ((new Config($this->getDataFolder() . "data.json", Config::JSON, []))->getAll() as $key => $value) {
			$this->data[$key] = $value;
		}

		foreach ((new Config($this->getDataFolder() . "players_data.json", Config::JSON, []))->getAll() as $key => $value) {
			$this->players_data[$key] = $value;
		}

		if (!PacketHooker::isRegistered()) {
			PacketHooker::register($this);
		}

		$this->getServer()->getPluginManager()->registerEvents(new class implements Listener {

			public function onPlayerJoin(PlayerJoinEvent $event): void {
				$player = $event->getPlayer();
				if (!isset(QT::getInstance()->players_data[$player->getName()])) {
					QT::getInstance()->players_data[$player->getName()] = [];
				}
			}
		}, $this);

		$this->getServer()->getCommandMap()->register("quest", new QuestCommand($this));
	}

	/**
	 * @throws JsonException
	 */
	protected function onDisable(): void {
		$data = new Config($this->getDataFolder() . "data.json", Config::JSON, $this->data);
		foreach ($this->data as $key => $value) {
			$data->set($key, $value);
			$data->save();
		}

		$players_data = new Config($this->getDataFolder() . "players_data.json", Config::JSON, $this->players_data);
		foreach ($this->players_data as $key => $value) {
			$players_data->set($key, $value);
			$players_data->save();
		}
	}

	public static function getInstance(): QT {
		return self::$instance;
	}
}