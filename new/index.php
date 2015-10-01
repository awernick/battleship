<?php
require_once '../game.php';

$strategy = $_GET['strategy'];
$ships = $_GET['ships'];

try {
  $game = new Game($strategy, $ships);
  echo json_encode(["response" => true, "pid" => $game->getPlayerID()]);
} 
catch(GameException $e) {
  echo json_encode(["response" => false, "reason" => $e->getMessage()]);
}

