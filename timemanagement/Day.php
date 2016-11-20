<?php

  /* Represents a day as more convenient representation of a date in the
   * "Y-m-d" format (year, month, day).
   * 
   * Date:   22.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  class Day
  {
    private $dateTime;
    
    
    public function __construct ()
    {
      $this->dateTime = null;
    }
    
    
    public function getAsDateTime ()
    {
      return $this->dateTime;
    }
    
    public function isOnSameDate (Day $dayToCompare)
    {
      return ($this->dateTime->format ("Y-m-d") == $dayToCompare->dateTime->format ("Y-m-d"));
    }
    
    // Returns day in the "YYYY-MM-DD" format.
    public function getAsYearMonthDayString ()
    {
      return $this->dateTime->format ("Y-m-d");
    }
    
    public function getWeekdayName ()
    {
      return strtolower ($this->dateTime->format ("l"));
    }
    
    
    public function __toString ()
    {
      return sprintf ("Day(%s)", $this->dateTime->format ("Y-m-d"));
    }
    
    
    
    ////////////////////////////////////////////////////////////////////
    // -- Implementation of static creation methods.               -- //
    ////////////////////////////////////////////////////////////////////
    
    public static function createFromTimestamp ($timestamp)
    {
      $day = new Day ();
      $day->dateTime = DateTime::createFromFormat ("U", $timestamp);
      
      return $day;
    }
    
    public static function createFromYearMonthDayString ($ymdString)
    {
      $day = new Day ();
      $day->dateTime = DateTime::createFromFormat ("Y-m-d", $ymdString);
      
      return $day;
    }
    
    public static function createFromYearMonthDayHourMinuteSecondString ($ymdHisString)
    {
      $day = new Day ();
      $day->dateTime = DateTime::createFromFormat ("Y-m-d H:i:s", $ymdHisString);
      
      return $day;
    }
    
    public static function createFromDateTime (DateTime $dateTime)
    {
      $day = new Day ();
      $day->dateTime = $dateTime;
      
      return $day;
    }
    
    public static function createFromYearMonthDay
    (
      $yearAsNumber,
      $monthAsNumber,
      $dayAsNumber
    )
    {
      $day        = null;
      $dateString = null;
      
      $dateString = sprintf
      (
        "%d-%d-%d",
        $yearAsNumber,
        $monthAsNumber,
        $dayAsNumber
      );
      $day = Day::createFromYearMonthDayString ($dateString);
      
      return $day;
    }
  }

?>
