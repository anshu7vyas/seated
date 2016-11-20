<?php

  /* Implements a search based on a restaurant's location
   * and popularity.
   * 
   * The "popularity" is defined as the number of reservations for
   * a restaurant currently stored in the database.
   * 
   * Date:   27.11.2015
   * Author: Kaveh Yousefi
   */
   
  require_once ("controller/LocationLoader.php");
  require_once ("controller/RestaurantLoader.php");
  require_once ("model/Location.php");
  require_once ("model/RestaurantModel.php");
  
  
  class CitySearch
  {
    private $locationLoader;
    private $restaurantLoader;
    private $searchResults;
    
    
    public function __construct ()
    {
      $this->searchResults    = array                ();
      $this->locationLoader   = new LocationLoader   ();
      $this->restaurantLoader = new RestaurantLoader ();
    }
    
    
    public function process ()
    {
      $this->searchResults = array ();
      $locations           = $this->locationLoader->getLocations     ();
      $restaurants         = $this->restaurantLoader->getRestaurants ();
      
      foreach ($locations as $location)
      {
        //$groupingKey = $location->id;
        $groupingKey = $location->city;
        
        $this->searchResults[$groupingKey]["city"]  = $location->city;
        $this->searchResults[$groupingKey]["state"] = $location->state;
        $this->searchResults[$groupingKey]["num_restaurants"] = 0;
      }
      
      foreach ($restaurants as $restaurant)
      {
        //$groupingKey = $restaurant->location->id;
        $groupingKey = $restaurant->location->city;
        $this->searchResults[$groupingKey]["num_restaurants"] += 1;
      }
      
      $this->searchResults = array_values ($this->searchResults);
    }
    
    public function getSearchResults ()
    {
      return $this->searchResults;
    }
  }

?>
