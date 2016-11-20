<?php
  
  /* Represents a timespan between two times.
   * 
   * Date:   22.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  class Timespan
  {
    private $startTime;
    private $duration;
    private $endTime;
    
    
    public function __construct ()
    {
      $this->startTime = Timespan::createDateTime ("00:00:00");
      $this->duration  = 0.0;
      $this->endTime   = Timespan::createDateTime ("00:00:00");
    }
    
    
    public function getStartTime ()
    {
      return $this->startTime;
    }
    
    public function getStartTimeAsHourMinuteSecondString ()
    {
      return $this->startTime->format ("H:i:s");
    }
    
    public function getDurationInMinutes ()
    {
      return $this->duration;
    }
    
    public function getEndTime ()
    {
      return $this->endTime;
    }
    
    public function getEndTimeAsHourMinuteSecondString ()
    {
      return $this->endTime->format ("H:i:s");
    }
    
    public function intersects (Timespan $timespanToCompare)
    {
      return (($this->startTime < $timespanToCompare->endTime) &&
              ($this->endTime   > $timespanToCompare->startTime));
    }
    
    // this before other?
    public function isBeforeOtherTimespan (Timespan $timespanToCompare)
    {
      return ($this->endTime <= $timespanToCompare->startTime);
    }
    
    // other before this?
    public function isAfterOtherTimespan (Timespan $timespanToCompare)
    {
      return ($this->startTime >= $timespanToCompare->endTime);
    }
    
    public function intersectsDateTime (DateTime $dateTime)
    {
      return (($this->startTime > $dateTime) &&
              ($this->endTime   < $dateTime));
    }
    
    public function isBeforeOtherDateTime (DateTime $dateTime)
    {
      return ($this->endTime <= $dateTime);
    }
    
    public function isAfterOtherDateTime (DateTime $dateTime)
    {
      return ($this->startTime >= $dateTime);
    }
    
    
    public function __toString ()
    {
      return sprintf
      (
        "Timespan(start=%s, duration=%d, end=%s)",
        $this->startTime->format ("H:i:s"),
        $this->duration,
        $this->endTime->format   ("H:i:s")
      );
    }
    
    
    
    ////////////////////////////////////////////////////////////////////
    // -- Implementation of static creation methods.               -- //
    ////////////////////////////////////////////////////////////////////
    
    // $startTime: "H:i:s" time string.
    public static function createFromStartTimeAndDuration ($startTime, $durationInMinutes)
    {
      $timespan            = new Timespan ();
      $timespan->startTime = Timespan::createDateTime   ($startTime);
      $timespan->endTime   = Timespan::createDateTime   ($startTime);
      $timespan->endTime   = $timespan->endTime->add    (DateInterval::createFromDateString ($durationInMinutes . " minutes"));
      $timespan->duration  = (  $timespan->endTime->getTimestamp   ()
                              - $timespan->startTime->getTimestamp ()) / 60;
      
      return $timespan;
    }
    
    // $startTime & endTime: "H:i:s" time strings.
    public static function createFromStartTimeAndEndTime ($startTime, $endTime)
    {
      $timespan            = new Timespan ();
      $timespan->startTime = Timespan::createDateTime ($startTime);
      $timespan->endTime   = Timespan::createDateTime ($endTime);
      $timespan->duration  = $timespan->startTime
                                      ->diff   ($timespan->endTime)
                                      ->format ('%i');
      $timespan->duration  = (  $timespan->endTime->getTimestamp   ()
                              - $timespan->startTime->getTimestamp ()) / 60;
      
      return $timespan;
    }
    
    
    
    ////////////////////////////////////////////////////////////////////
    // -- Implementation of auxiliary methods.                     -- //
    ////////////////////////////////////////////////////////////////////
    
    private static function createDateTime ($timeString)
    {
      return DateTime::createFromFormat ("H:i:s", $timeString);
    }
  }
  
?>
