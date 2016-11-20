<?php
  
  /* Models a location.
   * 
   * Date:   Kaveh Yousefi
   * Author: 16.11.2015
   */
  
  class Location
  {
    public $id;
    public $city;
    public $state;
    public $zip;
    public $neighborhood;   
    
    public function __construct ()
    {
      $this->id           = 0;
      $this->city         = null;
      $this->state        = null;
      $this->zip          = null;
      $this->neighborhood = null;
    }
    
    
    public function __toString ()
    {
      return sprintf
      (
        "Location(id=%d, city=%s, state=%s, zip=%s, neighborhood=%s)",
        $this->id, $this->city, $this->state, $this->zip,
        $this->neighborhood
      );
    }
  }

?>
