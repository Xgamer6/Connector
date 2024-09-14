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

    protected function onEnable(): void {
        $this->saveDefaultConfig();
        $config = $this->getConfig();
        $this->ip = $config->get("ip", "0.0.0.0");
        $this->port = $config->get("port", 0);
        $this->disableJoinMessage = $config->get("disable_join_message", false);
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
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        $version = $this->getDescription()->getVersion();
        $message = "§7[§eConnector§7] §cThis server uses Connector v{$version}\n§eConnector supports API 5.18.0 or 5.18.1\n§rCommands\n§a!reconnect §r- §7Reconnect to the current Server\n§a!changelog §r- §7View the Changelog (§cSOON§7)§r";
        $player->sendMessage($message);

        if ($this->disableJoinMessage) {
            $event->setJoinMessage("");
        }
    }
}
