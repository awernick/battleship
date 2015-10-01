<?php

abstract class Player {
  protected $ships = [];
  protected $shots = [];

  abstract public function makeShot();

  public function setShips($ships) {
    $this->ships = $ships;
  }

  public function getShips() {
    return $this->ships;
  }

  public function getShots() {
    return $this->shots;
  }
}
