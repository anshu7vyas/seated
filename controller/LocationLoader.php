<?php
  
  /* Loads locations from the database.
   * 
   * Date:   Kaveh Yousefi
   * Author: 16.11.2015
   */
  
  require_once ("model/Location.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class LocationLoader
  {
    public function __construct ()
    {
    }
    
    
    public function getLocations ()
    {
      $locations    = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                city,
                state,
                zip,
                neighborhood
         FROM   Location;"
      );
      
      $locations = $this->createLocationsFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $locations;
    }
    
    public function getLocationInfos ()
    {
      $locations    = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                city,
                state,
                zip,
                neighborhood
         FROM   Location;"
      );
      
      $locations = $this->createLocationsFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $locations;
    }
    
    public function getLocationByID ($locationID)
    {
      $location     = null;
      $locations    = null;
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                city,
                state,
                zip,
                neighborhood
         FROM   Location
         WHERE  id = ?;"
      );
      
      $sqlStatement->bind_param ("s", $locationID);
      
      $locations = $this->createLocationsFromSqlStatement ($sqlStatement);
      
      if (empty ($locations))
      {
        $location = null;
      }
      else
      {
        $location = $locations[0];
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $location;
    }
    
    public function getLocationByCityStateAndZip ($city, $state, $zip)
    {
      $location     = null;
      $locations    = null;
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                city,
                state,
                zip,
                neighborhood
         FROM   Location
         WHERE  city  LIKE ? AND
                state LIKE ? AND
                zip   LIKE ?;"
      );
      
      $sqlStatement->bind_param ("sss", $city, $state, $zip);
      
      $locations = $this->createLocationsFromSqlStatement ($sqlStatement);
      
      if (empty ($locations))
      {
        $location = null;
      }
      else
      {
        $location = $locations[0];
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $location;
    }
    
    
    private function createLocationsFromSqlStatement ($sqlStatement)
    {
      $locations = array ();
      
      if ($sqlStatement->execute ())
      {
        $sqlStatement->bind_result
        (
          $id,
          $city,
          $state,
          $zip,      
          $neighborhood
        );
        
        while ($row = $sqlStatement->fetch ())
        {
          $location               = new Location ();
          $location->id           = $id;
          $location->city         = $city;
          $location->state        = $state;
          $location->zip          = $zip;
          $location->neighborhood = $neighborhood;          
          
          $locations[] = $location;
        }
      }
      
      return $locations;
    }
  }
  
?>
