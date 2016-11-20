<?php
  
  /* Represents the search parameters for a restaurant.
   * 
   * Date:   19.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  class SearchParameters
  {
    public $partySize;
    public $date;
    public $time;
    public $targetRestaurantID;
    public $targetLocationID;
    public $userPosition;
    public $cuisine;
    public $categories;
    public $cityName;
    public $restName;
    
    
    public function __construct ()
    {
      $this->partySize          = null;
      $this->date               = null;
      $this->time               = null;
      $this->targetRestaurantID = null;
      $this->targetLocationID   = null;
      $this->userPosition       = null;
      $this->cuisine            = null;
      $this->categories         = null;
      $this->cityName           = null;
      $this->restName           = null;
    }
    
    
    public function hasTargetRestaurant ()
    {
      return ($this->targetRestaurantID != null);
    }
    
    public function hasTargetLocation ()
    {
      return ($this->targetLocationID != null);
    }
    
    public function hasUserPosition ()
    {
      return ($this->userPosition != null);
    }
    
    public function hasCuisine ()
    {
      return ($this->cuisine != null);
    }
    
    public function hasRestaurantName ()
    {
      return ($this->restName != null);
    }
    
    public function hasCategories ()
    {
      return (($this->categories != null) &&
              (! empty ($this->categories)));
    }
    
    public function getDay ()
    {
      $reservationDay = Day::createFromYearMonthDayString ($this->date);
      
      return $reservationDay;
    }
    
    public function getDateTime ()
    {
      return DateTime::createFromFormat ("H:i:s", $this->time);
    }
    
    public function getTimeAsDateTime ()
    {
      return DateTime::createFromFormat ("H:i:s", $this->time);
    }
    
    public function getTimeAsHourMinuteSecondString ()
    {
      return $this->time;
    }
    
    
    public function __toString ()
    {
      return sprintf ("SearchParameters(partySize=%s, date=%s, time=%s)",
                      $this->partySize,
                      $this->date,
                      $this->time);
    }
  }

?>
