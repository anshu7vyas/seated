<?php

  /* Models the opening and closing time of a restaurant on a
   * certain weekday.
   *
   * Date:   09.13.2015
   * Author: Kaveh Yousefi
   */
  
  class RestaurantOperationTime
  {
    const CLOSED_DAY_TIME_VALUE = "00:00:00";
    
    
    public $id;
    public $restaurant;
    public $openingTime;
    public $closingTime;
    public $weekdayName;
    
    
    public function __construct ()
    {
      $this->id          = null;
      $this->restaurant  = null;
      $this->openingTime = self::CLOSED_DAY_TIME_VALUE;
      $this->closingTime = self::CLOSED_DAY_TIME_VALUE;
      $this->weekdayName = null;
    }
    
    
    public function isOpen ()
    {
      return ($this->openingTime != $this->closingTime);
    }
    
    public function getRestaurantID ()
    {
      if ($this->restaurant != null)
      {
        return $this->restaurant->id;
      }
      else
      {
        return null;
      }
    }
    
    
    public function __toString ()
    {
      return sprintf
      (
        "RestaurantOperationTime(id=%s, restaurant_id=%s, " .
        "weekdayName=%s, openingTime=%s, closingTime=%s, isOpen=%d)",
        $this->id,
        $this->getRestaurantID (),
        $this->weekdayName,
        $this->openingTime,
        $this->closingTime,
        $this->isOpen ()
      );
    }
  }

?>
