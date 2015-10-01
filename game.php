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
  private $active;

  public function create($strategy, $ships = null){
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
    $this->active = true;
    save();
  }

  // Play
  public static function play($pid){
    // Load game based on PID
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

  public static function validatePID($pid) { 
  }



  public static function validateShipPlacement($shipPlacement) {
      
  }

  public static function generateShipPlacement(){
  }

  public function save(){
    // TODO: 
    //  - Throw exception if $pid is not set
    //  - Throw exception if players not set
    //  - Throw exception if board not set


    // Gather Human Data
    $human = $this->getHumanPlayer();
    $human_ships = $human->getShips();
    $human_shots = $human->getShots();
    
    // Gather Computer Data
    $computer = $this->getComputerPlayer();
    $computer_ships = $computer->getShips();
    $computer_strategy = $computer->getStrategy();
    $computer_extra = $computer_strategy->getExtras();
    
    $json_data = [
      "human" => [
        "ships" => $human_ships,
        "shots" => $human_shots
      ],
      "computer" => [
        "ships" => $computer_ships,
        "shots" => $computer_shots,
        "strategy" => (String) $computer_strategy,
        "extra" => $computer_extra
      ],
      "active" = $this->active
    ];
    
    // Persist changes
    $file = fopen("{$this->pid}.json", 'w');
    fwrite($file, json_encode($json_data));
    fclose($file);
  }
  
  // Helper method to access player ID
  public function getPlayerID(){
    return $this->pid;
  }
  
  // Helper method to get the human player
  public function getHumanPlayer(){
    return $this->board->getHumanPlayer();
  }
  
  // Helper method to get the computer player
  public function getComputerPlayer(){
    return $this->board->getComputerPlayer();
  }
}
