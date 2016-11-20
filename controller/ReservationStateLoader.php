<?php

  require_once ("model/ReservationState.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class ReservationStateLoader
  {
    public function __construct ()
    {
    }
    
    
    public function getStates ()
    {
      $states       = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                name,
                description
         FROM   ReservationState
        "
      );

      $states = $this->createStatesFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $states;
    }
    
    public function getStateByID ($stateID)
    {
      $state        = null;
      $states       = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                name,
                description
         FROM   ReservationState
         WHERE  id = ?
        "
      );

      $sqlStatement->bind_param ("s", $stateID);

      $states = $this->createStatesFromSqlStatement ($sqlStatement);
      
      if (empty ($states))
      {
        $state = null;
      }
      else
      {
        $state = $states[0];
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $state;
    }


    private function createStatesFromSqlStatement ($sqlStatement)
    {
      $states = array ();

      if ($sqlStatement->execute ())
      {
        $sqlStatement->bind_result
        (
          $id,
          $name,
          $description
        );

        while ($row = $sqlStatement->fetch ())
        {
          $state = new ReservationState ();
          $state->id          = $id;
          $state->name        = $name;
          $state->description = $description;
          
          $states[] = $state;
        }
      }

      return $states;
    }
  }

?>
