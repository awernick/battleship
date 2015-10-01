<?php

require_once 'game_exceptions.php';
require_once 'ship.php';
require_once 'board.php';
require_once 'human_player.php';
require_once 'computer_player.php';
require_once 'strategy.php';
require_once 'random_strategy.php';
require_once 'sweep_strategy.php';
require_once 'smart_strategy.php';

class Game {

   // Strategies
  const Random = "random";
  const Sweep = "sweep";
  const Smart = "smart";
 
  // Class variables
  static $options = [
    "size" => 10, 
    "strategies" => [self::Smart, self::Random, self::Sweep],
    "ships" => [
      ["name" => AircraftCarrier::NAME, "size" => AircraftCarrier::SIZE],
      ["name" => Battleship::NAME, "size" => Battleship::SIZE],
      ["name" => Frigate::NAME, "size" => Frigate::SIZE],
      ["name" => Submarine::NAME, "size" => Submarine::SIZE],
      ["name" => Minesweeper::NAME, "size" => Minesweeper::SIZE]
    ]
  ];

  // Properties
  private $board;
  private $pid;


  public function Game($strategy, $ships = null){
    $strategy = self::validateStrategy($strategy);
    
    // Generate ship placement if ships are not present,
    // validate them if they are.
    if(empty($ships)){
      $ships = self::generateShipPlacement();
    } else {
      self::validateShipPlacement($ships);
    }
    
    // Setup Human Player 
    $human = new HumanPlayer();
    $human->setShips($ships);

    // Setup Computer Player
    $computer = new ComputerPlayer($strategy);
    $computer_ships = self::generateShipPlacement();
    $computer->setShips($computer_ships);
    
    // Setup Board
    $this->board = new Board;
    $this->board->setHumanPlayer($human);
    $this->board->setComputerPlayer($computer);
    
    // Generate PID
    $this->pid = uniqid();
  }

  // Play
  public function play($pid){
    // Load game based on PID
  }
  
  // Helper method to access player ID
  public function getPlayerID(){
    return $this->pid;
  }
  
  // Class/Static functions
  public static function info() {
    return json_encode(self::$options); 
  }

  public static function validateStrategy($strategy) {
    if(empty($strategy)) {
      throw new StrategyMissingException;
    }

    $strategies = [
      self::Random, 
      self::Sweep, 
      self::Smart 
    ];
    
    if(!in_array($strategy, $strategies)) {    
      throw new UnknownStrategyException;
    }
    
    return new RandomStrategy;
  }

  public static function validateShipPlacement($shipPlacement) {
      
  }

  public static function generateShipPlacement(){
  }
}
