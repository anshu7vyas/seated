<?php
  
  require_once ("model/ReservationState.php");
  require_once ("timemanagement/Day.php");
  
  
  class Reservation
  {
    const RESERVED_VIA_UNKNOWN = null;
    const RESERVED_VIA_DINER   = 1;
    const RESERVED_VIA_HOST    = 2;
    const RESERVED_VIA_WIDGET  = 3;
    
    public $id;
    public $diner;
    public $table;
    public $partySize;
    public $partyName;
    public $date;
    public $time;
    public $requests;
    public $notes;
    public $host;
    public $state;
    public $restaurant_id;
    public $diner_id;
    public $table_id;
    public $state_id;  
    public $reservedVia;
    public $holdExpires;
    public $createdOn;
    public $lastModify;


    public function __construct ()
    {
      $this->id            = 0;
      $this->diner         = null;    
      $this->partySize     = 0;
      $this->partyName     = null;
      $this->date          = null;
      $this->time          = null;
      $this->table         = null;
      $this->requests      = null;
      $this->notes         = null;
      $this->host          = null;
      $this->state         = null;
      $this->restaurant_id = 0;
      $this->diner_id      = 0;
      $this->table_id      = 0;
      $this->state_id      = 0;
      $this->reservedVia   = self::RESERVED_VIA_UNKNOWN;
      $this->holdExpires   = 0;
      $this->createdOn     = 0;
      $this->lastModify    = 0;
    }

    
    public function getStateID ()
    {
      if ($this->state != null)
      {
        return $this->state->id;
      }
      else
      {
        return $this->state_id;
      }
    }
    
    public function getDay ()
    {
      $reservationDay = Day::createFromYearMonthDayHourMinuteSecondString ($this->time);

      return $reservationDay;
    }

    public function getDateTime ()
    {
      return DateTime::createFromFormat ("Y-m-d H:i:s", $this->time);
    }
    
    public function hasThisState ($stateIDToCompare)
    {
      return ($this->state_id == $stateIDToCompare);
    }
    
    public function isActive ()
    {
      $isActive = false;
      $stateID  = 0;
      
      $stateID  = $this->state_id;
      $isActive = ($this->hasThisState (ReservationState::RESERVED) ||
                   $this->hasThisState (ReservationState::SEATED));
      
      return $isActive;
    }
  }

?>
