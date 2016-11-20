<?php
  
  /* Loads RestaurantCategorizations from the database.
   * 
   * Date:   20.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  require_once ("CategoryLoader.php");
  require_once ("model/Category.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class RestaurantCategorizationLoader
  {
    private $categoryLoader;
    
    
    public function __construct ()
    {
      $this->categoryLoader = new CategoryLoader ();
    }
    
    
    public function getCategoriesByRestaurantID ($restaurantID)
    {
      $categories   = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                restaurant_id,
                category_id
         FROM   RestaurantCategorization
         WHERE  restaurant_id = ?;"
      );
      
      $sqlStatement->bind_param ("s", $restaurantID);
      
      if ($sqlStatement->execute ())
      {
        $sqlStatement->bind_result
        (
          $id,
          $restaurantID,
          $categoryID
        );
        
        while ($row = $sqlStatement->fetch ())
        {
          $category = null;
          
          $category     = $this->categoryLoader->getCategoryByID ($categoryID);
          $categories[] = $category;
        }
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $categories;
    }
  }
  
?>
