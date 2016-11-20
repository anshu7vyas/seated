<?php
  
  /* Creates "Timespan" instances between start and ending time.
   * 
   * This allows splitting a larger timespan into an array of smaller
   * timespans.
   * 
   * Date:   22.11.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ('timemanagement/Timespan.php');
  
  
  class TimespanFactory
  {
    public function __construct ()
    {
    }
    
    
    public function createTimespansBySplittingAnotherTimespan
    (
      Timespan $timespanToSplit,
      $durationInMinutes    // As integer.
    )
    {
      $timespans       = array ();
      $startTimeString = $timespanToSplit->getStartTimeAsHourMinuteSecondString ();
      $endTimeString   = $timespanToSplit->getEndTimeAsHourMinuteSecondString   ();
      
      $timespans = $this->createTimespans
      (
        $startTimeString,
        $endTimeString,
        $durationInMinutes
      );
      
      return $timespans;
    }
    
    public function createTimespans
    (
      $startTimeString,     // In "H:i:s" format.
      $endTimeString,       // In "H:i:s" format.
      $durationInMinutes    // As integer.
    )
    {
      $timespans              = array ();
      $nextStartTimeAsString  = $startTimeString;
      $totalTimespan          = Timespan::createFromStartTimeAndEndTime
      (
        $startTimeString,
        $endTimeString
      );
      $totalDurationInMinutes = $totalTimespan->getDurationInMinutes ();
      
      do
      {
        if ($totalDurationInMinutes < $durationInMinutes)
        {
          $durationInMinutes = $totalDurationInMinutes;
        }
        
        $timespan = Timespan::createFromStartTimeAndDuration
        (
          $nextStartTimeAsString,
          $durationInMinutes
        );
        $nextStartTimeAsString  = $timespan->getEndTime ()->format ("H:i:s");
        
        $totalDurationInMinutes = $totalDurationInMinutes - $durationInMinutes;
        
        $timespans[] = $timespan;
      }
      while ($totalDurationInMinutes > 0);
      
      return $timespans;
    }
  }
  
?>
