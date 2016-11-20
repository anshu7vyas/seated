<?php
  
  /* Creates "TimeBlock" instances for a restaurant table on a given
   * day.
   * 
   * If a "TimeBlock" is a cell in a schedule, a "TimeBlockFactory"
   * creates a column of such cells.
   * 
   * Date:   22.11.2015
   * Author: Kaveh Yousefi
   */
   
  require_once ('timemanagement/TimeBlock.php');
  require_once ('timemanagement/TimespanFactory.php');
  
  
  class TimeBlockFactory
  {
    public function __construct ()
    {
    }
    
    
    public function createTimeBlocksForRestaurantTable
    (
      $restaurant,
      $table,
      $day,
      $timeBlockDurationInMinutes
    )
    {
      $timeBlocks      = null;
      $timeBlockIndex  = 0;
      $timespanFactory = null;
      $timespans       = null;
      
      if ($timeBlockDurationInMinutes <= 0)
      {
        throw Exception ("Duration must be greater than zero.");
      }
      
      $timeBlocks      = array ();
      $timeBlockIndex  = 0;
      $timespanFactory = new TimespanFactory ();
      $timespans       = $timespanFactory->createTimespans
      (
        $restaurant->opening,
        $restaurant->closing,
        $timeBlockDurationInMinutes
      );
      
      foreach ($timespans as $timespan)
      {
        $timeBlocks[] = new TimeBlock
        (
          $timeBlockIndex,
          $restaurant,
          $table,
          $day,
          $timespan
        );
        $timeBlockIndex++;
      }
      
      return $timeBlocks;
    }
    
    // Advantageous if you already have the timespans.
    // => Does not need to calculate a Timespan array again.
    public function createTimeBlocksFromTimespans
    (
      $restaurant,
      $table,
      $day,
      $timespans   // Array of Timespan objects.
    )
    {
      $timeBlocks     = array ();
      $timeBlockIndex = 0;
      
      foreach ($timespans as $timespan)
      {
        $timeBlocks[] = new TimeBlock
        (
          $timeBlockIndex,
          $restaurant,
          $table,
          $day,
          $timespan
        );
        $timeBlockIndex++;
      }
      
      return $timeBlocks;
    }
  }
?>
