<?php

  /* Stores a RestaurantImage into the database.
   * 
   * Date:   24.11.2015
   * Author: Kaveh Yousefi
   */
  
//  require_once ("../model/RestaurantImage.php");
//  require_once ("../utils/DatabaseConnectionProvider.php");
  
  require_once ("model/RestaurantImage.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class RestaurantImageWriter
  {
    private $image;
    
    
    public function __construct ()
    {
      $this->image = null;
    }
    
    
    public function getRestaurantImage ()
    {
      return $this->image;
    }
    
    public function setRestaurantImage (RestaurantImage $image)
    {
      $this->image = $image;
    }
    
    
    public function persist ()
    {
      if ($this->image == null)
      {
        throw new Exception ("RestaurantImage is null.");
      }
      
      $createdID    = null;
      $dbConnection = null;
      $sqlStatement = null;
      $restaurantID = 0;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "INSERT INTO RestaurantImage (restaurant_id,
                                      org_name,
                                      name,
                                      path_to_file,
                                      description,
                                      is_cover)
         VALUES (?, ?, ?, ?, ?, ?)"
      );
      $restaurantID = $this->image->getRestaurantID ();
      
      $sqlStatement->bind_param("ssssss",
                                $restaurantID,
                                $this->image->originalName,
                                $this->image->name,
                                $this->image->pathToFile,
                                $this->image->description,
                                $this->image->isCover);
      $sqlStatement->execute   ();
      $createdID = $sqlStatement->insert_id;
      
      $this->image->id = $createdID;
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $createdID;
    }
  }
  
?>
