<?php
  
  /* Loads all categories from the database.
   * 
   * Date:   01.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  require_once ("model/Category.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class CategoryLoader
  {
    public function __construct ()
    {
    }
    
    
    public function getCategories ()
    {
      $categories   = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                label_text,
                path_to_img,
                description
         FROM   Category;"
      );
      
      if ($sqlStatement->execute ())
      {
        $sqlStatement->bind_result
        (
          $id,
          $labelText,
          $pathToImage,
          $description
        );
        
        while ($row = $sqlStatement->fetch ())
        {
          $category = new Category  ();
          $category->setID          ($id);
          $category->setLabelText   (utf8_encode ($labelText));
          $category->setPathToImage ($pathToImage);
          $category->setDescription ($description);
          
          $categories[] = $category;
        }
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $categories;
    }
    
    public function getCategoryByID ($id)
    {
      $categories   = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                label_text,
                path_to_img,
                description
         FROM   Category
         WHERE  id = ?
        "
      );
      
      $sqlStatement->bind_param ("s", $id);
      
      if ($sqlStatement->execute ())
      {
        $sqlStatement->bind_result
        (
          $id,
          $labelText,
          $pathToImage,
          $description
        );
        
        while ($row = $sqlStatement->fetch ())
        {
          $category = new Category  ();
          $category->setID          ($id);
          $category->setLabelText   (utf8_encode ($labelText));
          $category->setPathToImage ($pathToImage);
          $category->setDescription ($description);
          
          $categories[] = $category;
        }
      }
      
      if (empty ($categories))
      {
        $category = null;
      }
      else
      {
        $category = $categories[0];
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $category;
    }
  }
  
?>
