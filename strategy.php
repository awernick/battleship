<?php

abstract class Strategy {
  public function getExtras() {
    // NOOP
  }

  public static function getName(){
    return self::$name;
  }
}
