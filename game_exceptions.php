<?php

class GameException extends Exception {}


// Strategy Exceptions
class StrategyMissingException extends GameException { protected $message = "Strategy not specified"; }
  
class UnknownStrategyException extends GameException { protected $message = "Unknown strategy"; } 


// Ship Exceptions
  
class MalformedShipPlacementException extends GameException { protected $message = "Ship placement not well-formed, ..."; }

class UnknownShipException extends GameException { protected $message = "Unknown ship name, ..."; }

class InvalidShipPositionException extends GameException { protected $message = "Invalid ship position, ..."; }

class InvalidShipDirectionException extends GameException { protected $message = "Invalid ship direction, ..."; }

class IncompleteShipPlacementException extends GameException { protected $message = "Incomplete ship placements"; }

class ConflictingShipPlacementException extends GameException { protected $message = "Conflicting ship placements"; }


// PID Exceptions
class PidMissingException extends GameException { protected $message = "Conflicting ship placements"; }

class UnknownPidException extends GameException { protected $message = "Conflicting ship placements"; }
