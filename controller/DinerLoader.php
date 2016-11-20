<?php
  
  require_once ("model/Diner.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class DinerLoader
  {
    public function __construct ()
    {
    }
    
    
    public function getDiners ()
    {
      $diners       = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                first_name,
                last_name,
                email,
                phone,
                mobile,
                member_since
         FROM   Diner
        "
      );
      
      $diners = $this->createDinersFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $diners;
    }
    
    public function getDinerByID ($dinerID)
    {
      $diner        = null;
      $diners       = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                first_name,
                last_name,
                email,
                phone,
                mobile,
                member_since
         FROM   Diner
         WHERE  id = ?
        "
      );
      
      $sqlStatement->bind_param ("s", $dinerID);
      
      $diners = $this->createDinersFromSqlStatement ($sqlStatement);
      
      if (empty ($diners))
      {
        $diner = null;
      }
      else
      {
        $diner = $diners[0];
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $diner;
    }
    
    public function getDinerByEmail ($dinerEmail)
    {
      $diner        = null;
      $diners       = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                first_name,
                last_name,
                email,
                phone,
                mobile,
                member_since
         FROM   Diner
         WHERE  email LIKE ?
        "
      );
      
      $sqlStatement->bind_param ("s", $dinerEmail);
      
      $diners = $this->createDinersFromSqlStatement ($sqlStatement);
      
      if (empty ($diners))
      {
        $diner = null;
      }
      else
      {
        $diner = $diners[0];
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $diner;
    }
    
    
    private function createDinersFromSqlStatement ($sqlStatement)
    {
      $diners = array ();
      
      if ($sqlStatement->execute ())
      {
        $sqlStatement->bind_result
        (
          $id,
          $firstName,
          $lastName,
          $email,
          $phoneNumber,
          $mobileNumber,
          $memberSince
        );
        
        while ($row = $sqlStatement->fetch ())
        {
          $diner = new Diner ();
          $diner->id           = $id;
          $diner->firstName    = $firstName;
          $diner->lastName     = $lastName;
          $diner->email        = $email;
          $diner->phoneNumber  = $phoneNumber;
          $diner->mobileNumber = $mobileNumber;
          $diner->memberSince  = $memberSince;
          
          $diners[] = $diner;
        }
      }

      return $diners;
    }
  }
  
?>
