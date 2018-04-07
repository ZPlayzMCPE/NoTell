<?php

namespace VMPE\notell;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\PluginBase;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class Main extends PluginBase implements Listener {

    private $enabled;

    public function onEnable() {
        $this->enabled = [];
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $issuer, Command $cmd, string $label, array $args) : bool{
        if ((strtolower($cmd->getName()) == "notell") && !(isset($args[0])) && ($issuer instanceof Player) && ($issuer->hasPermission("notell.toggle") || $issuer->hasPermission("notell.toggle.self"))) {
            if (isset($this->enabled[strtolower($issuer->getName())])) {
                unset($this->enabled[strtolower($issuer->getName())]);
            } else {
                $this->enabled[strtolower($issuer->getName())] = strtolower($issuer->getName());
            }

            if (isset($this->enabled[strtolower($issuer->getName())])) {
                $issuer->sendMessage("§6People will no longer be able to /tell you! :D");
            } else {
                $issuer->sendMessage("§cPeople are now able to use /tell to mesage you again. :D");
            }
            return true;
        } else {
            return false;
        }
    }

    public function onPlayerCommand(PlayerCommandPreprocessEvent $event) {
        if ($event->isCancelled()) return;
        $message = $event->getMessage();
        if (strtolower(substr($message, 0, 3)) == "/m" || strtolower(substr($message, 0, 5)) == "/tell" || strtolower(substr($message, 0, 7)) == "/msg" || strtolower(substr($message, 0, 4)) == "/t") {
            $args = explode(" ", $message);
            if (!isset($args[1])) {
                return;    
            }
            $sender = $event->getPlayer();

            foreach ($this->enabled as $notelluser) {

                if ((strpos(strtolower($notelluser), strtolower($args[1])) !== false) && (strtolower($notelluser) !== strtolower($sender->getName()))) {
                    $sender->sendMessage(TextFormat::RED . "§cThis player is not online.");
                    $event->setCancelled(true);
                    return;
                }

                if (isset($args[2]) && strpos(strtolower($notelluser), strtolower($args[2])) !== false && (strtolower($notelluser) !== strtolower($sender->getName()))) {
                    $sender->sendMessage(TextFormat::RED . "§cThis player is not online.");
                    $event->setCancelled(true);
                    return;
                }
            }
        }
    }
}
