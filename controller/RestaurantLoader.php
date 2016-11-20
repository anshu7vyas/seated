<?php
  
  /* Date:   05.11.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("utils/DatabaseConnectionProvider.php");
  require_once ("model/RestaurantModel.php");  
  require_once ("RestaurantCategorizationLoader.php");
  require_once ("LocationLoader.php");  
  require_once ("controller/AdministratorLoader.php");
  require_once ("controller/RestaurantOperationTimeLoader.php");
  require_once ("model/RestaurantOperationTime.php");
  
  
  class RestaurantLoader
  {
    // The redundant SQL "SELECT" statement code common to all requests.
    private static $SQL_SELECT_STATEMENT_CODE =
      "SELECT id,
              name,
              admin_id,
              short_description,
              long_description,
              location_id,
              opening,
              closing,
              phone,
              email,
              inet_address,
              meal_duration_time,
              street,
              street_no,
              longitude,
              latitude,
              created_on,
              last_modify,
              price_range
       FROM   Restaurant";
    
    private $locationLoader;
    private $categorizationLoader;
    private $administratorLoader;
    private $operationTimeLoader;
    
    
    public function __construct ()
    {
      $this->locationLoader       = new LocationLoader ();
      $this->categorizationLoader = new RestaurantCategorizationLoader ();
      $this->administratorLoader  = new AdministratorLoader ();
      $this->operationTimeLoader  = new RestaurantOperationTimeLoader ();
    }
    
    
    public function getRestaurants ()
    {
      $restaurants  = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        self::$SQL_SELECT_STATEMENT_CODE . ";"
      );
      
      $restaurants = $this->createRestaurantsFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $restaurants;
    }
    
    public function getRestaurantByID ($restaurantID)
    {
      $restaurant   = null;
      $restaurants  = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        self::$SQL_SELECT_STATEMENT_CODE . "
        WHERE  id = ?;
        "
      );
      
      $sqlStatement->bind_param ("s", $restaurantID);
      
      $restaurants = $this->createRestaurantsFromSqlStatement ($sqlStatement);
      
      if (empty ($restaurants))
      {
        $restaurant = null;
      }
      else
      {
        $restaurant = $restaurants[0];
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $restaurant;
    }
    
    public function getRestaurantByName ($restaurantName)
    {
      $restaurant   = null;
      $restaurants  = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        self::$SQL_SELECT_STATEMENT_CODE . "
        WHERE  `name` LIKE ?;
        "
      );
      
      $sqlStatement->bind_param ("s", $restaurantName);
      
      $restaurants = $this->createRestaurantsFromSqlStatement ($sqlStatement);
      
      if (empty ($restaurants))
      {
        $restaurant = null;        
      }
      else
      {
        $restaurant = $restaurants[0];        
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $restaurant;
    }
    
    public function getRestaurantsByAdminID ($adminID)
    {
      $restaurants  = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        self::$SQL_SELECT_STATEMENT_CODE . "
        WHERE admin_id = ?;
        "
      );
      
      $sqlStatement->bind_param ("s", $adminID);
      
      $restaurants = $this->createRestaurantsFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $restaurants;
    }
    
    
    private function createRestaurantsFromSqlStatement ($sqlStatement)
    {
      $restaurants = array ();
      
      if ($sqlStatement->execute ())
      {
        $sqlStatement->bind_result
        (
          $id,
          $name,
          $adminID,
          $shortDescription,
          $longDescription,
          $locationID,
          $opening,
          $closing,
          $phone,
          $email,
          $inet_address,
          $mealDurationTime,
          $street,
          $street_no,
          $longitude,
          $latitude,
          $createdOn,
          $lastModify,
          $priceRange
        );
        
        while ($row = $sqlStatement->fetch ())
        {
          $restaurant = new RestaurantModel ();
          $restaurant->id               = $id;
          $restaurant->name             = $name;
          $restaurant->shortDescription = $shortDescription;
          $restaurant->longDescription  = $longDescription;
          $restaurant->opening          = $opening;
          $restaurant->closing          = $closing;
          $restaurant->phoneNumber      = $phone;
          $restaurant->email            = $email;
          $restaurant->inet_address     = $inet_address;
          $restaurant->location         = $this->locationLoader->getLocationByID ($locationID);
          $restaurant->street           = $street;
          $restaurant->houseNumber      = $street_no;
          $restaurant->longitude        = $longitude;
          $restaurant->latitude         = $latitude;
          $restaurant->mealDurationTime = $mealDurationTime;
          $restaurant->createdOn        = $createdOn;
          $restaurant->lastModify       = $lastModify;
          $restaurant->categories       = $this->categorizationLoader->getCategoriesByRestaurantID ($id);
          $restaurant->priceRange       = $priceRange;
          $restaurant->administrator    = $this->getAdministrator ($adminID);
          
          $this->loadRestaurantOperationTimes ($restaurant);
          
          $restaurants[] = $restaurant;
        }
      }
      
      return $restaurants;
    }
    
    private function getAdministrator ($administratorID)
    {
      return $this->administratorLoader->getAdministratorByID ($administratorID);
    }
    
    private function loadRestaurantOperationTimes (RestaurantModel $restaurant)
    {
      $operationTimes = $this->operationTimeLoader->getRestaurantOperationTimesForRestaurant ($restaurant);
      
      foreach ($operationTimes as $operationTime)
      {
        $restaurant->setOperationTime ($operationTime->weekdayName, $operationTime);
      }
    }
  }
  
?>
