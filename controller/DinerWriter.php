<?php
  
  require_once ('model/Diner.php');
  require_once ("utils/DatabaseConnectionProvider.php");
  
  
  /* Writes a new Diner into the database.
   * 
   * Date:   20.11.2015
   * Author: Kaveh Yousefi
   */
  class DinerWriter
  {
    private $diner;


    public function __construct ()
    {
      $this->diner = null;
    }


    public function getDiner ()
    {
      return $this->diner;
    }

    public function setDiner (Diner $diner)
    {
      $this->diner = $diner;
    }
    
    
    public function persist ()
    {
      $createdID    = null;
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "INSERT INTO Diner (first_name,
                            last_name,
                            email,
                            phone,
                            mobile)
         VALUES (?, ?, ?, ?, ?)"
      );
      
      $sqlStatement->bind_param("sssss",
                                $this->diner->firstName,
                                $this->diner->lastName,
                                $this->diner->email,
                                $this->diner->phoneNumber,
                                $this->diner->mobileNumber);
      $sqlStatement->execute   ();
      $createdID = $sqlStatement->insert_id;
      
      $this->diner->id = $createdID;
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $createdID;
    }
  }
  
?>
