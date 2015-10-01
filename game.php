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
  
 // static $strategies = [SmartStrategy::getName(), RandomStrategy::getName(), SweepStrategy::getName()];

  // Class variables
  static $options = [
    "size" => Board::SIZE, 
    "strategies" => [SmartStrategy::$name, RandomStrategy::$name, SweepStrategy::$name],
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
    $this->save();
  }

  // Play
  public function play($pid, $shot){
    // Load game based on PID
    
    // TODO: Possibly get rid of validate methods, 
    // and delegate responsability to getters and setters.
    self::validatePlayerID($pid);

    $this->pid = $pid;
    
    // Restore game state
    load();

    // Probably not going to be used, but throw 
    // exception if the game has ended already
    if($this->active === false) {
      throw new GameEndedException;
    }
    
    // Set human shot 
    $this->getHumanPlayer()->setShot($shot);

    // Play turn
    $response = $this->board->playTurn();

    // Persist turn
    save();
  }
  
  
  // Class/Static functions
  public static function info() {
    return json_encode(self::$options); 
  }

  public static function validateStrategy($strategy) {
    if(empty($strategy)) {
      throw new StrategyMissingException;
    }

    if(!in_array($strategy, self::$strategies)) {    
      throw new UnknownStrategyException;
    }
    
    return new RandomStrategy;
  }

  public static function validatePlayerID($pid) {
    if(empty($pid)) {
      throw new PidMissingException;
    }

    if(!file_exists("$pid.json")) {
      throw new UnknownPidException;
    } 
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
    $computer_shots = $computer->getShots();
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
      "active" => $this->active
    ];
    
    // Persist changes
    $file = fopen("{$this->pid}.json", 'w');
    fwrite($file, json_encode($json_data));
    fclose($file);
  }

  public function load() {
    // Load data from persistance file
    $json_data = file_get_contents("{$this->pid}.json");
    $data = json_decode($json_data, true);
    $human_data = $data["human"];
    $computer_data = $data["computer"];
    
    // Load human player
    $human_ships = $human_data["ships"];
    $human_shots = $human_data["shots"];
    $human = new HumanPlayer;

    // Load computer player
    $computer_ships = $computer_data["ships"];
    $computer_shots = $computer_data["shots"];
    $computer_strategy = $computer_data["strategy"];
    $computer_extra = $computer_data["extra"];
    $computer = new ComputerPlayer;

    // Reset the board
    $board = new Board;
    $board->setHumanPlayer($human);
    $board->setComputerPlayer($computer);
    $this->board = $board;

    // Set game state
    $this->active = $data["active"];
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
