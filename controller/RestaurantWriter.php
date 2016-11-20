<?php
  
  /* Writes a new RestaurantModel into the database.
   * 
   * Date:   20.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  require_once ("controller/LocationWriter.php");
  require_once ("controller/RestaurantOperationTimeLoader.php");
  require_once ("model/Location.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class RestaurantWriter
  {
    private $restaurant;
    private $operationTimeLoader;
    
    
    public function __construct ()
    {
      $this->restaurant          = null;
      $this->operationTimeLoader = new RestaurantOperationTimeLoader ();
    }
    
    
    public function getRestaurant ()
    {
      return $this->restaurant;
    }
    
    public function setRestaurant (RestaurantModel $restaurant)
    {
      $this->restaurant = $restaurant;
    }
    
    
    public function persist ()
    {
      $createdId      = null;
      $dbConnection   = null;
      $sqlStatement   = null;      
      $locationID     = null;
      $location       = null;
//      $locationWriter = null;
      
      if ($this->restaurant->location != null)
      {
        $location   = $this->restaurant->location;
        $locationID = $this->restaurant->location->id;
      }
      else
      {
        $location   = null;
        $locationID = null;
      }
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "INSERT INTO Restaurant (name,
                                 admin_id,
                                 short_description,
                                 long_description,
                                 location_id,
                                 opening,
                                 closing,
                                 email,
                                 inet_address,
                                 meal_duration_time,
                                 last_modify,
                                 street,
                                 street_no,
                                 longitude,
                                 latitude,
                                 price_range)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
      );
      
      $sqlStatement->bind_param("ssssssssssssssss",
                                $this->restaurant->name,
                                $this->restaurant->administrator->id,
                                $this->restaurant->shortDescription,
                                $this->restaurant->longDescription,
                                $this->restaurant->location->id,
                                $this->restaurant->opening,
                                $this->restaurant->closing,
                                $this->restaurant->email,
                                $this->restaurant->inet_address,
                                $this->restaurant->mealDurationTime,
                                $this->restaurant->lastModify,
                                $this->restaurant->street,
                                $this->restaurant->houseNumber,
                                $this->restaurant->longitude,
                                $this->restaurant->latitude,
                                $this->restaurant->priceRange);
      
      $sqlStatement->execute ();
      $createdId = $sqlStatement->insert_id;
      
      $this->restaurant->id = $createdId;
      
      $this->persistOperationTimes ();
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $createdId;
    }
    
    public function update ()
    {
      $createdId      = null;
      $dbConnection   = null;
      $sqlStatement   = null;      
      $locationID     = null;
      $location       = null;
      
      if ($this->restaurant->location != null)
      {
        $location   = $this->restaurant->location;
        $locationID = $this->restaurant->location->id;
      }
      else
      {
        $location   = null;
        $locationID = null;
      }
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "UPDATE Restaurant
         SET    name               = ?,
                admin_id           = ?,
                short_description  = ?,
                long_description   = ?,
                location_id        = ?,
                meal_duration_time = ?,
                opening            = ?,
                closing            = ?,
                phone              = ?,
                email              = ?,
                inet_address       = ?,
                street             = ?,
                street_no          = ?,
                longitude          = ?,
                latitude           = ?,
                last_modify        = ?,
                price_range        = ?
         WHERE  id                 = ?;
        "
      );
      
      $sqlStatement->bind_param("ssssssssssssssssss",
                                $this->restaurant->name,
                                $this->restaurant->administrator->id,
                                $this->restaurant->shortDescription,
                                $this->restaurant->longDescription,
                                $this->restaurant->location->id,
                                $this->restaurant->mealDurationTime,
                                $this->restaurant->opening,
                                $this->restaurant->closing,
                                $this->restaurant->phoneNumber,
                                $this->restaurant->email,
                                $this->restaurant->inet_address,
                                $this->restaurant->street,
                                $this->restaurant->houseNumber,
                                $this->restaurant->longitude,
                                $this->restaurant->latitude,
                                $this->restaurant->lastModify,
                                $this->restaurant->priceRange,
                                $this->restaurant->id);
      
      $sqlStatement->execute ();
      
      if ($sqlStatement)
      {
        $hasError         = ($sqlStatement->errno != 0);
        $exceptionMessage = $sqlStatement->error;
        $sqlStatement->close ();
      }
      else
      {
        $hasError         = true;
        $exceptionMessage = "Restaurant could not be updated.";
      }
      
      if ($hasError)
      {
        throw new Exception ($exceptionMessage);
      }
      
      $this->updateOperationTimes ();
      
      $dbConnection->close ();
      
      return $createdId;
    }
    
    public function deleteAllTables ()
    {
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "DELETE FROM RestaurantTable
         WHERE       restaurant_id = ?;"
      );
      
      $sqlStatement->bind_param ("s", $this->restaurant->id);
      $sqlStatement->execute    ();
    }
    
    public function deleteAllImages ()
    {
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "DELETE FROM RestaurantImage
         WHERE       restaurant_id = ?;"
      );
      
      $sqlStatement->bind_param ("s", $this->restaurant->id);
      $sqlStatement->execute    ();
    }
    
    public function deleteAllNonCoverImages ()
    {
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "DELETE FROM RestaurantImage
         WHERE       is_cover      = 0 AND
                     restaurant_id = ?;"
      );
      
      $sqlStatement->bind_param ("s", $this->restaurant->id);
      $sqlStatement->execute    ();
    }
    
    public function deleteAllCoverImages ()
    {
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "DELETE FROM RestaurantImage
         WHERE       is_cover      = 1 AND
                     restaurant_id = ?;"
      );
      
      $sqlStatement->bind_param ("s", $this->restaurant->id);
      $sqlStatement->execute    ();
    }
    
    
    private function persistOperationTimes ()
    {
      if ($this->restaurant == null)
      {
        throw new Exception ("Cannot update operation times; restaurant is NULL.");
      }
      
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      
      foreach ($this->restaurant->getOperationTimes () as $weekday => $operationTime)
      {
        $sqlStatement = $dbConnection->prepare
        (
          "INSERT INTO RestaurantOperationTime
           (
             restaurant_id,
             opening_time,
             closing_time,
             weekday_name
           )
           VALUES (?, ?, ?, ?);"
        );
        
        $sqlStatement->bind_param("ssss",
                                  $this->restaurant->id,
                                  $operationTime->openingTime,
                                  $operationTime->closingTime,
                                  $weekday);

        $sqlStatement->execute ();
      }
    }
    
    private function updateOperationTimes ()
    {
      if ($this->restaurant == null)
      {
        throw new Exception ("Cannot update operation times; restaurant is NULL.");
      }
      
      $dbConnection   = null;
      $sqlStatement   = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      
      if (! $this->operationTimeLoader->hasOperationTimesForRestaurant ($this->restaurant))
      {
        $this->persistOperationTimes ();
      }
      else
      {
        foreach ($this->restaurant->getOperationTimes () as $weekday => $operationTime)
        {
          $sqlStatement = $dbConnection->prepare
          (
            "UPDATE RestaurantOperationTime
             SET    opening_time  = ?,
                    closing_time  = ?
             WHERE  restaurant_id = ?
                    AND
                    weekday_name  LIKE ?;
            "
          );
          
          $sqlStatement->bind_param ("ssss",
                                     $operationTime->openingTime,
                                     $operationTime->closingTime,
                                     $this->restaurant->id,
                                     $weekday);

          $sqlStatement->execute ();

          if ($sqlStatement)
          {
            $hasError         = ($sqlStatement->errno != 0);
            $exceptionMessage = $sqlStatement->error;
            $sqlStatement->close ();
          }
          else
          {
            $hasError         = true;
            $exceptionMessage = "Operation time could not be updated.";
          }

          if ($hasError)
          {
            throw new Exception ($exceptionMessage);
          }
        }

        $dbConnection->close ();
      }
      
      return true;
    }
  }
  
?>

