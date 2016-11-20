<?php

  require_once ("model/RestaurantTable.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class RestaurantTableLoader
  {
    private $restaurantLoader;
    
    
    public function __construct ()
    {
      $this->restaurantLoader = new RestaurantLoader ();
    }
    
    
    public function getTables ()
    {
      $tables       = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                restaurant_id,
                seats_no,
                description
         FROM   RestaurantTable
        "
      );
      
      $tables = $this->createTablesFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $tables;
    }
    
    public function getTableByID ($tableID)
    {
      $table        = null;
      $tables       = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                restaurant_id,
                seats_no,
                description
         FROM   RestaurantTable
         WHERE  id = ?
        "
      );

      $sqlStatement->bind_param ("s", $tableID);

      $tables = $this->createTablesFromSqlStatement ($sqlStatement);
      
      if (empty ($tables))
      {
        $table = null;
      }
      else
      {
        $table = $tables[0];
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $table;
    }
    
    public function getTablesByRestaurantID ($restaurantID)
    {
      $tables       = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                restaurant_id,
                seats_no,
                description
         FROM   RestaurantTable
         WHERE  restaurant_id = ?
        "
      );

      $sqlStatement->bind_param ("s", $restaurantID);

      $tables = $this->createTablesFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $tables;
    }
    
    
    private function createTablesFromSqlStatement ($sqlStatement)
    {
      $tables = array ();
      
      if ($sqlStatement->execute ())
      {
        $sqlStatement->bind_result
        (
          $id,
          $restaurantID,
          $numberOfSeats,
          $description
        );
        
        while ($row = $sqlStatement->fetch ())
        {
          $table                = new RestaurantTable ();
          $table->id            = $id;
          $table->restaurant    = $this->restaurantLoader->getRestaurantByID ($restaurantID);
          $table->numberOfSeats = $numberOfSeats;
          $table->description   = $description;
          
          $tables[] = $table;
        }
      }

      return $tables;
    }
  }

?>
