<?php

require_once 'player.php';

class HumanPlayer extends Player {
  private $x;
  private $y;

  public function makeShot() {
    return ["x" => $x, "y" => $y];
  }

  public function setX($x) {
    $this->x = $x;
  }

  public function setY($y) {
    $this->y = $y;
  }  
}
