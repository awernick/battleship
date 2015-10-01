<?php

class Ship {
}

class AircraftCarrier extends Ship {
  const SIZE = 5;
  const NAME = "Aircraft carrier";
}

class Battleship extends Ship {
  const SIZE = 4;
  const NAME = "Battleship";
}

class Frigate extends Ship {
  const SIZE = 3;
  const NAME = "Frigate";
}

class Submarine extends Ship {
  const SIZE = 3;
  const NAME = "Submarine";
}

class Minesweeper extends Ship {
  const SIZE = 2;
  const NAME = "Minesweeper";
}
