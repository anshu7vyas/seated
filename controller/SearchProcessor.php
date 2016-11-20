<?php

  /* Implements the restaurant search functionality.
   * 
   * Date:   22.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  require_once ('controller/LocationLoader.php');
  require_once ('controller/ReservationLoader.php');
  require_once ('controller/RestaurantTableLoader.php');
  require_once ('controller/SearchResultRestaurant.php');
  require_once ('controller/SearchResultTable.php');
  require_once ('model/SearchParameters.php');
  require_once ('timemanagement/Day.php');
  require_once ('timemanagement/RestaurantDaySchedule.php');
  
  
  class SearchProcessor
  {
    private $searchParameters;
    private $foundRestaurants;
    
    
    public function __construct ($searchParameters)
    {
      $this->searchParameters = $searchParameters;
      $this->foundRestaurants = array ();
    }
    
    
    public function search ()
    {
      $restaurantLoader  = null;
      $restaurants       = array();
      $reservationLoader = null;
      $reservations      = null;
      $searchDateTime    = null;
      $searchDay         = null;
      // Maps: [restaurant_id] -> RestaurantDaySchedule.
      $schedules         = null;
      
      $this->foundRestaurants = array ();
      $searchDateTime    = $this->searchParameters->getDateTime ();
      $searchDay         = $this->searchParameters->getDay      ();
      $restaurantLoader  = new RestaurantLoader                 ();
      $reservationLoader = new ReservationLoader                ();      
      if($this->searchParameters->hasRestaurantName()){
          //Only for one restaurant
          $restaurant = $restaurantLoader->getRestaurantByName($this->searchParameters->restName);  
          if($restaurant == null)
          {
             
          } else {
            $reservations    = $reservationLoader->getReservationsByRestaurantIDAndDate($restaurant->id,$searchDay->getAsDateTime ());
            $restaurants[] = $restaurant;
          }
          
      } else
      {
          //For all restaurants
          $restaurants = $restaurantLoader->getRestaurants    ();
          $reservations      = $reservationLoader->getReservationsByDate ($searchDay->getAsDateTime ());
      }      
            
      $schedules         = array ();
      
      // Filter RESTAURANTS.
      foreach ($restaurants as $restaurant)
      {
        $foundRestaurant = new SearchResultRestaurant
        (
          $restaurant,
          $this->searchParameters
        );
        
        // Restaurant has no matches for this request? => Remove it.
        if ($foundRestaurant->hasMatch ())
        {
          $this->foundRestaurants[] = $foundRestaurant;
        }
      }
    }
    
    public function getFoundSearchResultRestaurants ()
    {
      return $this->foundRestaurants;
    }
  }

?>
