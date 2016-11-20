<?php
  
  /* Loads a RestaurantImage from the database.
   * 
   * Date:   24.11.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("controller/RestaurantLoader.php");
  require_once ("model/RestaurantImage.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class RestaurantImageLoader
  {
    private $restaurantLoader;
    
    
    public function __construct ()
    {
      $this->restaurantLoader = new RestaurantLoader ();
    }
    
    
    public function getRestaurantImage ()
    {
      return $this->image;
    }
    
    public function setRestaurantImage (RestaurantImage $image)
    {
      $this->image = $image;
    }
    
    
    public function getImages ()
    {
      $images       = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                restaurant_id,
                org_name,
                name,
                path_to_file,
                description,
                is_cover
         FROM   RestaurantImage
        "
      );

      $images = $this->createImagesFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $images;
    }
    
    public function getImagesByRestaurantID ($restaurantID)
    {
      $images       = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                restaurant_id,
                org_name,
                name,
                path_to_file,
                description,
                is_cover
         FROM   RestaurantImage
         WHERE  restaurant_id = ?
        "
      );

      $sqlStatement->bind_param ("s", $restaurantID);

      $images = $this->createImagesFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $images;
    }
    
    
    private function createImagesFromSqlStatement ($sqlStatement)
    {
      $images = array ();
      
      if ($sqlStatement->execute ())
      {
        $sqlStatement->bind_result
        (
          $id,
          $restaurantID,
          $originalName,
          $name,
          $pathToFile,
          $description,
          $isCover
        );
        
        while ($row = $sqlStatement->fetch ())
        {
          $image               = new RestaurantImage ();
          $image->id           = $id;
          $image->restaurant   = $this->restaurantLoader->getRestaurantByID ($restaurantID);
          $image->originalName = $originalName;
          $image->name         = $name;
          $image->pathToFile   = $pathToFile;
          $image->description  = $description;
          $image->isCover      = $isCover;
          
          $images[] = $image;
        }
      }

      return $images;
    }
  }
  
?>
