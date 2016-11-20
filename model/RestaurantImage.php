<?php
  
  /* Models an image associated with a restaurant.
   * 
   * Date:   24.11.2015
   * Author: Kaveh Yousefi
   */
  
  class RestaurantImage
  {
    public $id;
    public $restaurant;
    public $originalName;
    public $name;
    public $pathToFile;
    public $description;
    public $isCover;
    
    
    public function __construct ()
    {
      $this->id           = 0;
      $this->restaurant   = 0;
      $this->originalName = null;
      $this->name         = null;
      $this->pathToFile   = null;
      $this->description  = null;
      $this->isCover      = false;
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
  }

?>
