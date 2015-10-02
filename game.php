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
  
  static $strategies = [self::Random, self::Sweep, self::Smart];

  // Class variables
  static $options = [
    "size" => Board::SIZE, 
    "strategies" => [self::Random, self::Sweep, self::Smart],
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
      $ships = self::assembleShips($ships);
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
    
    // TODO: return appropriate strategy 
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



  public static function assembleShips($shipPlacement) {
    $ship_names = [
      AircraftCarrier::NAME => "AircraftCarrier", 
      Battleship::NAME => "Battleship",
      Frigate::NAME => "Frigate", 
      Submarine::NAME => "Submarine", 
      Minesweeper::NAME => "Minesweeper"
    ];

    $remaining_names = array_keys($ship_names);
    
    $ship_configs = explode(';', $shipPlacement);
    
    // Store completed ships
    $ships = [];

    // Validate that 5 ships were sent 
    if(count($ship_configs) < 5){
      throw new IncompleteShipPlacementException;
    }

    foreach($ship_configs as $ship_config) {
      // Separate ship attributes into an array
      $ship_attrs = explode(',',$ship_config);
      
      // Ensure that only 4 attributes where passed
      if(count($ship_attrs) > 4 || count($ship_attrs) < 4) {
        throw new MalformedShipPlacementException;
      }

      $name = $ship_attrs[0];
      $x = $ship_attrs[1]; 
      $y = $ship_attrs[2];
      $dir = $ship_attrs[3];
      
      // Verify ship names
      if(empty($ship_names[$name])) {
        throw new UnknownShipException;
      }

      // Check that ships are not repeated
      $index = array_search($name, $remaining_names);
      if($index === false) {
        throw new MalformedShipPlacementException;
      } else {
        unset($remaining_names[$index]);
      }

      // Make sure that a direction was sent and convert 
      // the string representation to a boolean
      if($dir != 'true' && $dir != 'false') {
        throw new InvalidShipDirectionException;
      } else {
        $dir = $dir === 'true' ? true : false;
      }
      

      // Verify that coordinates provided are correct
      foreach([$x, $y] as $coord) {
        // Range check
        if($coord < 1 || $coord > 10) {
          throw new InvalidShipPositionException;
        }
      }

      // Create corresponding ship class
      $ship = new $ship_names[$name];
      
      // Make sure battleship is not bigger than the board
      $start = $dir === true ? $x : $y; 
      if($start + $ship::SIZE > Board::SIZE) {
        throw new InvalidShipPositionException;
      }
      
      // Generate coordinates for 
      $coordinates = [];
      for($i = 0; $i < $ship::SIZE; $i++) {
        if($dir === true) {
          $coord = [$x + $i, $y];
        } else {
          $coord = [$x, $y + $i];
        }
        array_push($coordinates, $coord);
      }
      
      // Update ship
      $ship->setCoordinates($coordinates);

      // Store if everything is alright up to now
      array_unshift($ships, $ship);
    }

    // Check for conflicts
    for($i = 0; $i < count($ships); $i++) {
      $current = $ships[$i];
      $current_coordinates = $current->getCoordinates();
      for($j = $i+1; $j < (count($ships)); $j++){
        $another = $ships[$j];
        $another_coordinates = $another->getCoordinates();
        for($k = 0; $k < count($another_coordinates); $k++) {
          $another_coord = $another_coordinates[$k];
          for($l = 0; $l < count($current_coordinates); $l++){
            $current_coord = $current_coordinates[$l];
            if($another_coord[0]  == $current_coord[0] && $another_coord[1]  == $current_coord[1] ) {
              throw new ConflictingShipPlacementException;
            }
          }
        }
      }
    }

    return $ships;
  }

  public static function generateShipPlacement(){
    $ship_sizes = [
      "Battleship" => Battleship::SIZE, 
      "Frigate" => Frigate::SIZE, 
      "Submarine" => Submarine::SIZE, 
      "Aircraft carrier" => AircraftCarrier::SIZE, 
      "Minesweeper" => Minesweeper::SIZE
    ];
    
    $valid = false;
    $ships;
    do {
      $ship_configs = [];
      foreach($ship_sizes as $name => $size) {
        $dir = rand(0,1) ? 'true' : 'false';
        if($dir === 'true') {
          $x = rand(1, (Board::SIZE - $size));
          $y = rand(1, 10);
        }
        else {
          $y = rand(1, (Board::SIZE - $size));
          $x = rand(1, 10);
        }
        array_push($ship_configs, "$name,$x,$y,$dir");
      }
       
      
      try {
        $ships = self::assembleShips(implode($ship_configs, ";"));
        $valid = true;
      } catch (GameException $e) {
        $valid = false;
      }
    } while(!$valid);

    return $ships; 
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
