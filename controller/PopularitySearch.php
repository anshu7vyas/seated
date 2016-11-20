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
  
  
  require_once ("controller/ReservationLoader.php");
  require_once ("controller/RestaurantLoader.php");
  require_once ("model/Reservation.php");
  require_once ("model/RestaurantModel.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class PopularitySearch
  {
    const DEFAULT_MAXIMUM_NUMBER_OF_RESULTS = 5;
    
    private $cityName;
    private $maximumNumberOfResults;
    private $restaurantLoader;
    
    
    public function __construct ()
    {
      $this->cityName               = null;
      $this->maximumNumberOfResults = self::DEFAULT_MAXIMUM_NUMBER_OF_RESULTS;
      $this->restaurantLoader       = new RestaurantLoader ();
    }
    
    
    public function getCityName ()
    {
      return $this->cityName;
    }
    
    public function setCityName ($cityName)
    {
      $this->cityName = $cityName;
    }
    
    
    public function process ()
    {
      $matchingRestaurants = array ();
      $dbConnection        = null;
      $sqlStatement        = null;
      
      if ($this->cityName === null)
      {
        return array ();
      }
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT   Restaurant.id,
                  COUNT(*) AS number_of_reservations
         FROM     Restaurant
         JOIN     Location
         ON       Restaurant.location_id = Location.id
         JOIN     Reservation
         ON       Restaurant.id = Reservation.restaurant_id
         WHERE    Location.city LIKE ?
         GROUP BY Restaurant.id
         ORDER BY number_of_reservations DESC
         LIMIT    ?;
        "
      );
      
      $sqlStatement->bind_param  ("si", $this->cityName,
                                        $this->maximumNumberOfResults);
      $sqlStatement->execute     ();
      $sqlStatement->bind_result ($restaurantID, $numberOfReservations);
      
      while ($row = $sqlStatement->fetch ())
      {
        $restaurant            = $this->restaurantLoader->getRestaurantByID ($restaurantID);
        $matchingRestaurants[] = $restaurant;
      }
      
      return $matchingRestaurants;
    }
  }

?>
