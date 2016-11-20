<?php
  
  /* Loads RestaurantOperationTime instances for a certain restaurant.
   * 
   * Date:   10.12.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("model/RestaurantModel.php");  
  require_once ("model/RestaurantOperationTime.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class RestaurantOperationTimeLoader
  {
    public function __construct ()
    {
    }
    
    
    public function getRestaurantOperationTimesForRestaurant (RestaurantModel $restaurant)
    {
      $operationTimes = array ();
      $dbConnection   = null;
      $sqlStatement   = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                opening_time,
                closing_time,
                weekday_name
         FROM   RestaurantOperationTime
         WHERE  restaurant_id = ?;
        "
      );
      
      $sqlStatement->bind_param ("s", $restaurant->id);
      $sqlStatement->execute    ();
      
      if ($sqlStatement)
      {
        $sqlStatement->bind_result
        (
          $id,
          $openingTime,
          $closingTime,
          $weekdayName
        );
        
        while ($row = $sqlStatement->fetch ())
        {
          $operationTime              = new RestaurantOperationTime ();
          $operationTime->id          = $id;
          $operationTime->restaurant  = $restaurant;
          $operationTime->openingTime = $openingTime;
          $operationTime->closingTime = $closingTime;
          $operationTime->weekdayName = $weekdayName;

          $operationTimes[] = $operationTime;
        }
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $operationTimes;
    }
    
    public function hasOperationTimesForRestaurant (RestaurantModel $restaurant)
    {
      return (count ($this->getRestaurantOperationTimesForRestaurant ($restaurant)) > 0);
    }
  }

?>
