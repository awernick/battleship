<?php

abstract class Strategy {
  public function getExtras() {
    // NOOP
  }

  public function __toString(){
    return (String) $this->name;  
  }
}
