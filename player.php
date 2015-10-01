<?php

abstract class Player {
  private $ships;
  
  abstract public function makeShot();

  public function setShips($ships) {
    $this->ships = $ships;
  }

  public function getShips($ships) {
    return $this->ships;
  }
}
