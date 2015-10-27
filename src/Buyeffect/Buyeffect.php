<?php

namespace Buyeffect;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\entity\Effect;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\tile\Tile;
use pocketmine\tile\Sign;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;

class Buyeffect extends PluginBase implements Listener{
	
	 public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
		        	if(is_dir($this->getServer()->getPluginPath()."EconomyAPI")){
				$this->getLogger()->info(TextFormat::GREEN."EconomyAPI loaded!");
				$this->economy = true;
			}else{
				$this->getLogger()->info(TextFormat::RED."EconomyAPI not loaded.");
				$this->economy = false;
			}

		if(!file_exists($this->getDataFolder())){
			@mkdir($this->getDataFolder(), 0744, true);
		}
		if(!file_exists($this->getDataFolder() . "config.yml")){
			$this->settings = new config($this->getDataFolder() . "config.yml", config::YAML, array(
			"effect cost(per second)"=> null,
			"1" => 5,
			"2" => 5,
			"3" => 5,
			"4" => 5,
			"5" => 5,
			"8" => 5,
			"9" => 5,
			"10" => 5,
			"11" => 5,
			"12" => 5,
			"13" => 5,
			"14" => 5,
			"18" => 5,
			"19" => 5,
			"20" => 5,
			"effect levels" => null,
			"1s" => 5,
			"2s" => 5,
			"3s" => 5,
			"4s" => 5,
			"5s" => 5,
			"8s" => 5,
			"9s" => 5,
			"10s" => 5,
			"11s" => 5,
			"12s" => 5,
			"13s" => 5,
			"14s" => 5,
			"18s" => 5,
			"19s" => 5,
			"20s" => 5,
			
			));
		}else{
			$this->settings = new config($this->getDataFolder() . "config.yml", config::YAML, array());
		}
		$this->config = new config($this->getDataFolder() . "config.yml", config::YAML, array());
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
			}

    public function onDisable(){
    }
				
	

		
	 public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
    	$fcmd = strtolower($cmd->getName());
    	switch($fcmd){
    		case "buyeffect":
    			if(isset($args[0])){
    				$args[0] = strtolower($args[0]);
    				if($args[0]=="buy"){
						if($sender->hasPermission("Buyeffect.commands.buy")){
								if(!isset($args[1])){
									$sender->sendMessage("Usege: /buyeffect buy 'effectID' duration");
									break;
								}
								if(!ctype_digit($args[1])){
									$sender->sendMessage("Usege: /buyeffect buy 'effectID' duration");
									break;
								}
								if(!isset($args[2])){
									$sender->sendMessage("Usege: /buyeffect buy effectID 'duration'");
									break;
								}
								if(!ctype_digit($args[2])){
									$sender->sendMessage("Usege: /buyeffect buy effectID 'duration'");
									break;
								}
								$name = $sender->getName();
								$cfg = $this->settings->getAll();
								if(!$this->settings->exists($args[1])){
									$sender->sendMessage("EffectID is wrong.");
									break;
								}
								$elevel = $cfg[$args[1]."s"] - 1;
								$effect = Effect::getEffect($args[1]); //Effect ID
								$effect->setVisible(true); //Particles
								$effect->setAmplifier($elevel);
								$effect->setDuration($args[2]*20); //Ticks
								$priceps = $cfg[$args[1]];
								$price = ($priceps*$args[2]);
								if($price < $this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->myMoney($sender)){
								$this->getServer()->getPluginManager()->getPlugin("EconomyAPI")->reduceMoney($name, $price);
								$sender->addEffect($effect) ;
								$sender->sendMessage("You bought ".$args[1]."."."The cost was $".$price.".");
								unset($priceps);
								unset($price);
								unset($cfg);
								break;
								}else{
									$sender->sendMessage("You are short of money.");
																	unset($priceps);
								unset($price);
								unset($cfg);
    						
    						break;}
    					}else{
    						$sender->sendMessage("You don't have permissions to use this command");
    						break;

    					}
						
    				}elseif($args[0]=="conf"){
    					if($sender->hasPermission("Buyeffect.commands.conf")){
								$cfg = $this->settings->getAll();
								if(!isset($args[1])){
									$sender->sendMessage("Usege: /buyeffect conf 'price or power' [effectID] [price]or[power]");
									break;
								}
								if(ctype_digit($args[1])){
									$sender->sendMessage("Usege: /buyeffect conf 'price or power' [effectID] [price]or[power]");
									break;
								}
								if(!isset($args[2])){
									$sender->sendMessage("Usege: /buyeffect conf price or power '[effectID]' [price]or[power]");
									break;
								}
								if(!$this->settings->exists($args[2])){
									$sender->sendMessage("EffectID is wrong.");
									break;
								}
								if(!ctype_digit($args[2])){
									$sender->sendMessage("Usege: /buyeffect conf price or power '[effectID]' [price]or[power]");
									break;
								}
								if(!isset($args[3])){
									$sender->sendMessage("Usege: /buyeffect price or power [effectID] '[price]or[power]'");
									break;
								}
								if(!ctype_digit($args[3])){
									$sender->sendMessage("Usege: /buyeffect price or power [effectID] '[price]or[power]'");
									break;
								}
								if($args[1] == 'price'){
									$this->config->set($args[2], $args[3]);
									$this->config->save();
									$sender->sendMessage("You changed EffectID ".$args[2]." 's price $".$args[3]);	
									break;
								}elseif($args[1] == 'power'){
									$this->config->set($args[2]."s", $args[3]);
									$this->config->save();
									$sender->sendMessage("You changed power".$args[2]." 's power".$args[3]);	
									break;
								}else{
									$sender->sendMessage("Usege: /buyeffect conf 'price or power' [effectID] [price]or[power]");
								
    				        break;
								}
						}else{
    						$sender->sendMessage("You don't have permissions to use this command");
    						break;
    					}
							
					}elseif($args[0]=="reload"){
    					if($sender->hasPermission("Buyeffect.commands.reload")){
				
$this->settings->reload() ;
					$sender->sendMessage("Config was reloaded.");
        				        break;
    					}else{
    						$sender->sendMessage("You don't have permissions to use this command");
    						break;
    					}



					
						}elseif($args[0]=="check"){
    					if($sender->hasPermission("Buyeffect.commands.check")){
								if(!isset($args[1])){
									$sender->sendMessage("Usege: /buyeffect check '[effectID]'");
									break;
								}
								if(!ctype_digit($args[1])){
									$sender->sendMessage("Usege: /buyeffect check '[effectID]'");
									break;
								}
								$cfg = $this->settings->getAll();
								$ecost = $cfg[$args[1]];
								$elevel = $cfg[$args[1]."s"];
								$sender->sendMessage("EffectID ".$args[1].": Price $".$ecost." Power ".$elevel);
								unset($cfg);
								unset($ecost);
								unset($elevel);
								
        				        break;
    					}else{
    						$sender->sendMessage("You don't have permissions to use this command");
    						break;
						}
										



					}
					else{
							$sender->sendMessage("There are not this option. mistyped?");
					}
				
				}else{
							$sender->sendMessage("There are not this option. mistyped?");
				}
		}
		}
	
						
	
	

		
}