<?php
  
  /* A TimeBlock associates a restaurant and a specific table with
   * a day, timespan and occupation state (that is, a reservation):
   * As a mapping: [restaurant + table] -> (day, timespan, reservation).
   * 
   * It can be imagined as a cell in a schedule.
   * 
   * Date:   22.11.2015
   * Author: Kaveh Yousefi
   */
  
  class TimeBlock
  {
    private $blockIndex;
    private $restaurant;
    private $table;
    private $day;
    private $timespan;
    private $isOccupied;
    private $reservation;
    
    
    public function __construct
    (
      $blockIndex,
      $restaurant,
      $table,
      $day,
      $timespan
    )
    {
      $this->blockIndex  = $blockIndex;
      $this->restaurant  = $restaurant;
      $this->table       = $table;
      $this->day         = $day;
      $this->timespan    = $timespan;
      $this->isOccupied  = false;
      $this->reservation = null;
    }
    
    
    public function getIndex ()
    {
      return $this->blockIndex;
    }
    
    public function getRestaurant ()
    {
      return $this->restaurant;
    }
    
    public function getTable ()
    {
      return $this->table;
    }
    
    public function getDay ()
    {
      return $this->day;
    }
    
    public function getTimespan ()
    {
      return $this->timespan;
    }
    
    public function isOccupied ()
    {
      return $this->isOccupied;
    }
    
    public function getReservation ()
    {
      return $this->reservation;
    }
    
    // Check if the reservation occupies this time block.
    // Also, change the time block's state if necessary.
    public function checkIfOccupied ($reservation)
    {
      if (! $this->isOccupied)
      {
        $isSameTable = $this->isSameTable          ($reservation);
        $isOnSameDay = $this->isOnSameDay          ($reservation);
        $intersects  = $this->doTimespansIntersect ($reservation);
        $isActive    = $reservation->isActive      ();
        
        $this->isOccupied  = ($isSameTable &&
                              $isOnSameDay &&
                              $intersects  &&
                              $isActive);
        $this->reservation = $reservation;
      }
    }
    
    
    public function __toString ()
    {
      return sprintf
      (
        "TimeBlock(restaurant=%s, table=%s, date=%s, timespan=%s, isOccupied=%d)",
        $this->restaurant,
        $this->table->id,
        $this->day,
        $this->timespan,
        $this->isOccupied
      );
    }
    
    
    
    ////////////////////////////////////////////////////////////////////
    // -- Implementation of auxiliary methods.                     -- //
    ////////////////////////////////////////////////////////////////////
    
    private function isSameTable ($reservation)
    {
      return ($this->table->id == $reservation->table->id);
    }
    
    private function isOnSameDay ($reservation)
    {
      //$reservationDay = Day::createFromYearMonthDayHourMinuteSecondString ($reservation->time);
      $reservationDay = $reservation->getDay ();
      
      return ($this->day->isOnSameDate ($reservationDay));
    }
    
    private function doTimespansIntersect ($reservation)
    {
      $reservationTimespan = Timespan::createFromStartTimeAndDuration
      (
        $reservation->getDateTime ()->format ("H:i:s"),
        $reservation->restaurant->getMealDurationTimeInMinutes ()
      );
      
      return $this->timespan->intersects ($reservationTimespan);
    }
  }
?>
