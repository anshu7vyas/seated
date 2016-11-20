<?php
  
  /* Stores an Administrator in the database.
   * 
   * Date:   02.12.2015
   * Author: Kaveh Yousefi
   */
  
  
  require_once ("model/Administrator.php");
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  class AdministratorWriter
  {
    private $administrator;
    
    
    public function __construct ()
    {
      $this->administrator = null;
    }
    
    
    public function getAdministrator ()
    {
      return $this->administrator;
    }
    
    public function setAdministrator (Administrator $administrator)
    {
      $this->administrator = $administrator;
    }
    
    
    public function persist ()
    {
      $createdID    = null;
      $dbConnection = null;
      $sqlStatement = null;
      //$restaurantID = null;
      
      //$restaurantID = $this->administrator->getRestaurantID ();
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "INSERT INTO admin (first_name,
                            last_name,
                            email,
                            phone,
                            mobile,
                            position,
                            password)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
      );
      
      $sqlStatement->bind_param("sssssss",
                                $this->administrator->firstName,
                                $this->administrator->lastName,
                                $this->administrator->email,
                                $this->administrator->phoneNumber,
                                $this->administrator->mobileNumber,
                                $this->administrator->position,
                                $this->administrator->encryptedPassword);
      $sqlStatement->execute   ();
      $createdID = $sqlStatement->insert_id;
      
      $this->administrator->id = $createdID;
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $createdID;
    }
  }
  
?>
