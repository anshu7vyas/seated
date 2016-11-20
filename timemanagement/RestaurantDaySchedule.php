<?php
  
  /* Creates an array (column) of TimeBlocks for each restaurant
   * table.
   * 
   * Defines all TimeBlocks for a restaurant on a given day.
   * 
   * Date:   22.11.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ('timemanagement/TimeBlockFactory.php');
  require_once ('timemanagement/TimespanFactory.php');
  
  
  class RestaurantDaySchedule
  {
    public $restaurant;
    public $day;
    public $tableTimeOverviews;   // Maps [tableID] -> array (TimeBlocks)
    public $timeBlockDuration;    // Length of a time block in minutes.
    public $timespans;
    
    
    public function __construct
    (
      $restaurant,
      $tables,
      $day,
      $timeBlockDurationInMinutes
    )
    {
      $this->restaurant         = $restaurant;
      $this->day                = $day;
      $this->tableTimeOverviews = array ();
      $this->timeBlockDuration  = $timeBlockDurationInMinutes;
      $this->timespans          = null;
      
      $weekday                = $day->getWeekdayName ();
      // Opening and closing time for this day of the week.
      $operationTimeOnThisDay = $restaurant->getOperationTime ($weekday);
      
      /* Restaurant is not open on this day of the week?
       * => Nothing to show.
       */
      if (! $operationTimeOnThisDay->isOpen ())
      {
        $this->timespans          = array ();
        $this->tableTimeOverviews = array ();
        
        foreach ($tables as $table)
        {
          $this->tableTimeOverviews[$table->id] = array ();
        }
      }
      else
      {
        $timespanFactory = new TimespanFactory ();
        $this->timespans = $timespanFactory->createTimespans
        (
  //        $restaurant->opening,
  //        $restaurant->closing,
          $operationTimeOnThisDay->openingTime,
          $operationTimeOnThisDay->closingTime,
          $timeBlockDurationInMinutes
        );

        $timeBlockFactory = new TimeBlockFactory ();

        foreach ($tables as $table)
        {
          $this->tableTimeOverviews[$table->id] = $timeBlockFactory->createTimeBlocksFromTimespans
          (
            $restaurant,
            $table,
            $day,
            $this->timespans
          );
        }
      }
    }
    
    
    public function processReservations ($reservations)
    {
      foreach ($reservations as $reservation)
      {
        foreach ($this->tableTimeOverviews as $tableBlocks)
        {
          foreach ($tableBlocks as $timeBlock)
          {
            $timeBlock->checkIfOccupied ($reservation);
          }
        }
      }
    }
    
    
    public function getRestaurant ()
    {
      return $this->restaurant;
    }
    
    public function getDay ()
    {
      return $this->day;
    }
    
    // All time blocks, that is:
    //   [table_id] => array (timeBlock_1, ..., timeBlock_n).
    public function getTimeBlocks ()
    {
      return $this->tableTimeOverviews;
    }
    
    public function getFreeTimeBlocks ()
    {
      $freeTimeBlocks = array ();
      
      foreach ($this->tableTimeOverviews as $tableBlocks)
      {
        foreach ($tableBlocks as $timeBlock)
        {
          if (! $timeBlock->isOccupied ())
          {
            $table   = $timeBlock->getTable ();
            $tableID = $table->id;
            
            if (! isset ($freeTimeBlocks[$tableID]))
            {
              $freeTimeBlocks[$tableID] = array ();
            }
            
            $freeTimeBlocks[$tableID][] = $timeBlock;
          }
        }
      }
      
      return $freeTimeBlocks;
    }
    
    // Get all TimeBlocks for a certain table ID.
    public function getTimeBlocksByTableID ($tableID)
    {
      return $this->tableTimeOverviews[$tableID];
    }
    
    // Get all free TimeBlocks for a certain table ID.
    public function getFreeTimeBlocksByTableID ($tableID)
    {
      $freeTimeBlocks = array ();
      
      foreach ($this->getTimeBlocksByTableID ($tableID) as $tableBlocks)
      {
        foreach ($tableBlocks as $timeBlock)
        {
          if (! $timeBlock->isOccupied ())
          {
            if (! isset ($freeTimeBlocks[$tableID]))
            {
              $freeTimeBlocks[$tableID] = array ();
            }
            
            $freeTimeBlocks[$tableID][] = $timeBlock;
          }
        }
      }
      
      return $freeTimeBlocks;
    }
    
    // Maps: [tableID] => TimeBlock at index.
    public function getTimeBlocksByIndex ($timeBlockIndex)
    {
      $timeBlocksAtIndex = array ();
      
      foreach ($this->tableTimeOverviews as $tableID => $timeBlocksOfTable)
      {
        $timeBlocksAtIndex[$tableID] = $timeBlocksOfTable[$timeBlockIndex];
      }
      
      return $timeBlocksAtIndex;
    }
    
    // Maps: [timeBlockIndex] => array (timeBlockOfTable1, ..., timeBlockOfTableN)
    public function getTimeBlocksByBlockOrder ()
    {
      $timeBlocksByBlockOrder = array ();
      $numberOfTimeBlocks     = count ($this->timespans);
      
      for ($timeBlockIndex = 0;
           $timeBlockIndex < $numberOfTimeBlocks;
           $timeBlockIndex++)
      {
        $timeBlocksByBlockOrder[] = $this->getTimeBlocksByIndex ($timeBlockIndex);
      }
      
      return $timeBlocksByBlockOrder;
    }
    
    // Finds TimeBlocks which intersect a given timespan.
    // Maps: [tableID] => array (TimeBlock_1, ... TimeBlock_n).
    public function getTimeBlocksByTimespanIntersection (Timespan $timespan)
    {
      $timeBlocksByIntersection = array ();
      
      foreach ($this->tableTimeOverviews as $tableID => $timeBlocksOfTable)
      {
        $timeBlocksByIntersection[$tableID] = array ();
        
        foreach ($timeBlocksOfTable as $timeBlock)
        {
          if ($timeBlock->getTimespan ()->intersects ($timespan))
          {
            $timeBlocksByIntersection[$tableID][] = $timeBlock;
          }
        }
      }
      
      return $timeBlocksByIntersection;
    }
    
    public function getTimespans ()
    {
      return $this->timespans;
    }
    
    
    public function __toString ()
    {
      $asString = "";
      
      foreach ($this->tableTimeOverviews as $tableBlocks)
      {
        foreach ($tableBlocks as $timeBlock)
        {
          $asString = $asString . "<br />" . $timeBlock;
        }
      }
      
      return $asString;
    }
  }
?>
