<?php

  /* Loads RestaurantCategorizations from the database.
   * 
   * Date:   20.11.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("model/Category.php");
  require_once ("model/RestaurantModel.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class RestaurantCategorizationWriter
  {
    private $restaurant;
    
    
    public function __construct ()
    {
      $this->restaurant = null;
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
      $createdIDs   = array ();
      $dbConnection = null;
      $sqlStatement = null;
      $categoryIDs  = null;
      
      $categoryIDs  = $this->restaurant->getCategoryIDs            ();
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      
      if (($this->restaurant->id > 0) && (count ($categoryIDs) > 0))
      {
        foreach ($categoryIDs as $categoryID)
        {
          $sqlStatement = $dbConnection->prepare
          (
            "INSERT INTO RestaurantCategorization (restaurant_id,
                                                   category_id)
             VALUES (?, ?)"
          );

          $sqlStatement->bind_param ("ss",
                                     $this->restaurant->id,
                                     $categoryID);
          $sqlStatement->execute    ();
          
          $createdIDs[] = $sqlStatement->insert_id;
        }
      }

      $sqlStatement->close ();
      $dbConnection->close ();

      return $createdIDs;
    }
    
    public function deleteCategorizations ()
    {
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "DELETE FROM RestaurantCategorization
         WHERE       restaurant_id = ?;"
      );
      
      $sqlStatement->bind_param ("s", $this->restaurant->id);
      $sqlStatement->execute    ();
    }
  }
?>