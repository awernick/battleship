<?php

abstract class Ship {
  protected $coordinates;
  
  public function wasHit($x, $y){
    foreach($this->coordinates as $coordinate) {
      if($coordinate === [$x, $y]) {
        return true;
      }
    }
    return false;
  }

  public function getCoordinates(){
    return $this->coordinates;
  }

  public function setCoordinates($coordinates){
    $this->coordinates = $coordinates;
  }
}

class AircraftCarrier extends Ship {
  const SIZE = 5;
  const NAME = "Aircraft carrier";
  public function getName(){
    return self::NAME;
  }
}

class Battleship extends Ship {
  const SIZE = 4;
  const NAME = "Battleship";
  public function getName(){
    return self::NAME;
  }
}

class Frigate extends Ship {
  const SIZE = 3;
  const NAME = "Frigate";
  public function getName(){
    return self::NAME;
  }
}

class Submarine extends Ship {
  const SIZE = 3;
  const NAME = "Submarine";
  public function getName(){
    return self::NAME;
  }
}

class Minesweeper extends Ship {
  const SIZE = 2;
  const NAME = "Minesweeper";
  public function getName(){
    return self::NAME;
  }
}
