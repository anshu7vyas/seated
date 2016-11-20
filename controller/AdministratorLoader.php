<?php
  
  /* Loads administrators from the database.
   * 
   * Date:   02.12.2015
   * Author: Kaveh Yousefi
   */
  
  
  require_once ("model/Administrator.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class AdministratorLoader
  {
    private static $SQL_SELECT_STATEMENT_CODE =
      "SELECT id,
              first_name,
              last_name,
              email,
              phone,
              mobile,
              position,
              password
       FROM   admin";
    
    
    public function __construct ()
    {
    }
    
    
    public function getAdministrators ()
    {
      $administrators = array ();
      $dbConnection   = null;
      $sqlStatement   = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        self::$SQL_SELECT_STATEMENT_CODE . ";"
      );
      
      $administrators = $this->createAdministratorsFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $administrators;
    }
    
    public function getAdministratorByID ($administratorID)
    {
      $administrator  = null;
      $administrators = array ();
      $dbConnection   = null;
      $sqlStatement   = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        self::$SQL_SELECT_STATEMENT_CODE . "
        WHERE  id = ?;"
      );
      
      $sqlStatement->bind_param ("s", $administratorID);
      
      $administrators = $this->createAdministratorsFromSqlStatement ($sqlStatement);
      
      if (empty ($administrators))
      {
        $administrator = null;
      }
      else
      {
        $administrator = $administrators[0];
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $administrator;
    }
    
    public function getAdministratorByEmail ($email)
    {
      $administrator  = null;
      $administrators = array ();
      $dbConnection   = null;
      $sqlStatement   = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        self::$SQL_SELECT_STATEMENT_CODE . "
        WHERE  email LIKE ?;"
      );
      
      $sqlStatement->bind_param ("s", $email);
      
      $administrators = $this->createAdministratorsFromSqlStatement ($sqlStatement);
      
      if (empty ($administrators))
      {
        $administrator = null;
      }
      else
      {
        $administrator = $administrators[0];
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $administrator;
    }
    
    
    private function createAdministratorsFromSqlStatement ($sqlStatement)
    {
      $administrators = array ();
      
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
          $position,
          $password
        );
        
        while ($row = $sqlStatement->fetch ())
        {
          $administrator = new Administrator ();
          $administrator->id                = $id;
          $administrator->firstName         = $firstName;
          $administrator->lastName          = $lastName;
          $administrator->email             = $email;
          $administrator->phoneNumber       = $phoneNumber;
          $administrator->mobileNumber      = $mobileNumber;
          $administrator->position          = $position;
          $administrator->encryptedPassword = $password;
          
          $administrators[] = $administrator;
        }
      }
      
      return $administrators;
    }
  }
  
?>
