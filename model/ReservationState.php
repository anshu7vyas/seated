<?php
  
  class ReservationState
  {
    const RESERVED = 1;
    const HELD     = 2;
    const CANCELED = 3;
    const SEATED   = 4;
    
    public $id;
    public $name;
    public $description;
    
    
    public function __construct ()
    {
      $this->id          = 0;
      $this->name        = null;
      $this->description = null;
    }
  }
  
?>
