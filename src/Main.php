<?php

declare(strict_types=1);

namespace nin\Connector;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    private string $ip;
    private int $port;
    private bool $disableJoinMessage;
    private bool $disableConnectorMessage;

    protected function onEnable(): void {
        $this->saveDefaultConfig();
        $config = $this->getConfig();
        $this->ip = $config->get("ip", "0.0.0.0");
        $this->port = $config->get("port", 0);
        $this->disableJoinMessage = $config->get("disable_join_message", false);
        $this->disableConnectorMessage = $config->get("disable_connector_message", false);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onPlayerChat(PlayerChatEvent $event): void {
        $message = $event->getMessage();
        $player = $event->getPlayer();

        if (strtolower($message) === '!reconnect') {
            if ($this->ip !== "0.0.0.0" && $this->port > 0) {
                $player->transfer($this->ip, $this->port);
            } else {
                $player->sendMessage("§cReconnect configuration is not properly set.");
            }
            $event->cancel();
        }

        if (strtolower($message) === '!forcereconnect') {
            if ($player->hasPermission("pocketmine.command.op")) {
                foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
                    if ($this->ip !== "0.0.0.0" && $this->port > 0) {
                        $onlinePlayer->transfer($this->ip, $this->port);
                    } else {
                        $player->sendMessage("§cReconnect configuration is not properly set.");
                    }
                }
            } else {
                $player->sendMessage("§cYou do not have permission to use this command.");
            }
            $event->cancel();
        }
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $version = $this->getDescription()->getVersion();

        if (!$this->disableConnectorMessage) {
            $message = "§7[§eConnector§7] §cThis server uses Connector v{$version}\n§eConnector supports API 5.18.0 or 5.18.1\n§uActive Commands\n§a!reconnect §r- §7Connect to the server from the config\n§a!forcereconnect §r- §7Force all players to reconnect";
            $player->sendMessage($message);
        }

        if ($this->disableJoinMessage) {
            $event->setJoinMessage("");
        }
    }
}

