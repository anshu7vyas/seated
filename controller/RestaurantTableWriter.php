<?php
  
  /* Stores a RestaurantTable in the database.
   * 
   * Date:   24.11.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("model/RestaurantTable.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class RestaurantTableWriter
  {
    private $table;
    
    
    public function __construct ()
    {
      $this->table = null;
    }
    
    
    public function getRestaurantTable ()
    {
      return $this->table;
    }
    
    public function setRestaurantTable (RestaurantTable $table)
    {
      $this->table = $table;
    }
    
    
    public function persist ()
    {
      $createdID    = null;
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "INSERT INTO RestaurantTable (restaurant_id,
                                      seats_no,
                                      description)
         VALUES (?, ?, ?)"
      );
      
      $sqlStatement->bind_param("sss",
                                $this->table->restaurant->id,
                                $this->table->numberOfSeats,
                                $this->table->description);
      $sqlStatement->execute   ();
      $createdID = $sqlStatement->insert_id;
      
      $this->table->id = $createdID;
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $createdID;
    }
    
    public function delete ()
    {
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "DELETE FROM RestaurantTable
         WHERE       id = ?;"
      );
      
      $sqlStatement->bind_param ("s", $this->table->id);
      $sqlStatement->execute    ();
    }
  }
  
?>
