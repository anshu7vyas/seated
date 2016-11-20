<?php
  
  /* Stores a Host in the database.
   * 
   * Date:   25.11.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("model/Host.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class HostWriter
  {
    private $host;
    
    
    public function __construct ()
    {
      $this->host = null;
    }
    
    
    public function getHost ()
    {
      return $this->host;
    }
    
    public function setHost (Host $host)
    {
      $this->host = $host;
    }
    
    
    public function persist ()
    {
      $createdID    = null;
      $dbConnection = null;
      $sqlStatement = null;
      $restaurantID = null;
      
      $restaurantID = $this->host->getRestaurantID ();
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "INSERT INTO Host (first_name,
                           last_name,
                           email,
                           restaurant_id,
                           position,
                           password)
         VALUES (?, ?, ?, ?, ?, ?)"
      );
      
      $sqlStatement->bind_param("ssssss",
                                $this->host->firstName,
                                $this->host->lastName,
                                $this->host->email,
                                $restaurantID,
                                $this->host->position,
                                $this->host->encryptedPassword);
      $sqlStatement->execute   ();
      $createdID = $sqlStatement->insert_id;
      
      $this->host->id = $createdID;
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $createdID;
    }
    
    
    public function update ()
    {
      $isSuccessful     = false;
      $hasError         = false;
      $exceptionMessage = null;
      $dbConnection     = null;
      $sqlStatement     = null;
      $restaurantID     = null;
      
      $restaurantID = $this->host->getRestaurantID ();
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "UPDATE Host
         SET    first_name       = ?,
                last_name        = ?,
                email            = ?,
                restaurant_id    = ?,
                position         = ?,
                password         = ?
        WHERE   id = ?;"
      );
      
      $sqlStatement->bind_param("sssdssd",
                                $this->host->firstName,
                                $this->host->lastName,
                                $this->host->email,
                                $restaurantID,
                                $this->host->position,
                                $this->host->encryptedPassword,
                                $this->host->id);
      $sqlStatement->execute ();
      
      if ($sqlStatement)
      {
        $hasError         = ($sqlStatement->errno != 0);
        $exceptionMessage = $sqlStatement->error;
        //$isSuccessful     = ($sqlStatement->affected_rows > 0);  // Returns FALSE, if everything OK, but nothing to update.
        $isSuccessful     = true;
        $sqlStatement->close ();
      }
      else
      {
        $hasError         = true;
        $exceptionMessage = "Host could not be updated.";
      }
      
      $dbConnection->close ();
      
      if ($hasError)
      {
        throw new Exception ($exceptionMessage);
      }
      
      return $isSuccessful;
    }
  }
  
?>
