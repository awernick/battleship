<?php

require_once 'player.php';
require_once 'strategy.php';

class ComputerPlayer extends Player {
  private $strategy;

  public function ComputerPlayer(Strategy $strategy) {
    $this->strategy = $strategy;
  }

  public function getStrategy() {
    return $this->strategy;
  }

  public function makeShot() {
    return ["x" => 0, "y" => 0];
  }
}
