<?php
  
  /* Writes a new Location into the database.
   * 
   * Date:   23.11.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ('model/Location.php');
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class LocationWriter
  {
    private $location;
    
    
    public function __construct ()
    {
      $this->location = null;
    }
    
    
    public function getLocation ()
    {
      return $this->location;
    }
    
    public function setLocation (Location $location)
    {
      $this->location = $location;
    }
    
    
    public function persist ()
    {
      $createdID    = null;
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "INSERT INTO Location (city,
                               state,
                               zip,
                               neighborhood)
         VALUES (?, ?, ?, ?)"
      );
      
      $sqlStatement->bind_param("ssss",
                                $this->location->city,
                                $this->location->state,
                                $this->location->zip,
                                $this->location->neighborhood);
      $sqlStatement->execute   ();
      $createdID = $sqlStatement->insert_id;
      
      $this->location->id = $createdID;
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $createdID;
    }
  }
  
?>
