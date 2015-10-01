<?php

require_once 'human_player.php';
require_once 'computer_player.php';

class Board {
  private $human_player;
  private $computer_player;
  
  public function setHumanPlayer(HumanPlayer $human) {
    $this->human_player = $human;
  }

  public function setComputerPlayer(ComputerPlayer $computer) {
    $this->computer_player = $computer;
  }
}
