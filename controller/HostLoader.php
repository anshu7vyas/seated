<?php
  
  /* Loads all hosts from the database.
   * 
   * Date:   01.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  require_once ("model/Host.php");
  require_once ("model/RestaurantModel.php");
  require_once ("RestaurantLoader.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class HostLoader
  {
    private static $SQL_SELECT_STATEMENT_CODE =
      "SELECT id,
                first_name,
                last_name,
                email,
                restaurant_id,
                position,
                password
         FROM   Host";
    
    private $restaurantLoader;
    
    
    public function __construct ()
    {
      $this->restaurantLoader = new RestaurantLoader ();
    }
    
    
    public function getHosts ()
    {
      $hosts        = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        self::$SQL_SELECT_STATEMENT_CODE . ";"
      );
      
      $hosts = $this->createHostsFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $hosts;
    }
    
    public function getHostByID ($hostID)
    {
      $hosts        = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        self::$SQL_SELECT_STATEMENT_CODE . "
        WHERE  id = ?;"
      );
      
      $sqlStatement->bind_param ("d", $hostID);
      
      $hosts = $this->createHostsFromSqlStatement ($sqlStatement);
      
      if (empty ($hosts))
      {
        $host = null;
      }
      else
      {
        $host = $hosts[0];
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $host;
    }
    
    public function getHostByEmail ($email)
    {
      $hosts        = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        self::$SQL_SELECT_STATEMENT_CODE . "
        WHERE  email LIKE ?;"
      );
      
      $sqlStatement->bind_param ("s", $email);
      
      $hosts = $this->createHostsFromSqlStatement ($sqlStatement);
      
      if (empty ($hosts))
      {
        $host = null;
      }
      else
      {
        $host = $hosts[0];
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $host;
    }
    
    
    private function createHostsFromSqlStatement ($sqlStatement)
    {
      $hosts = array ();
      
      if ($sqlStatement->execute ())
      {
        $sqlStatement->bind_result
        (
          $id,
          $firstName,
          $lastName,
          $email,
          $restaurantID,
          $position,
          $password
        );
        
        while ($row = $sqlStatement->fetch ())
        {
          $host = new Host ();
          $host->id                = $id;
          $host->firstName         = $firstName;
          $host->lastName          = $lastName;
          $host->email             = $email;
          $host->restaurant        = $this->restaurantLoader->getRestaurantByID ($restaurantID);
          $host->position          = $position;
          $host->encryptedPassword = $password;
          
          $hosts[] = $host;
        }
      }
      
      return $hosts;
    }
  }
  
?>
