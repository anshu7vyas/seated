<?php

require_once ("model/Rating.php");
require_once ("utils/DatabaseConnectionProvider.php");
  
class RatingWriter {
    public $rating;
    
    
    public function __construct ()
    {
      $this->rating = null;
    }
    
    
    public function getRating()
    {
      return $this->rating;
    }
    
    public function setRating(Administrator $administrator)
    {
      $this->rating = $rating;
    }
    
    
    public function persist ()
    {
      $createdID    = null;
      $dbConnection = null;
      $sqlStatement = null;
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "INSERT INTO rating (username,
                            restaurant_id,
                            rating,
                            description)
         VALUES (?, ?, ?, ?)"
      );
      
      $sqlStatement->bind_param("ssss",
                                $this->rating->username,
                                $this->rating->restaurantID,
                                $this->rating->rating,
                                $this->rating->description);
      $sqlStatement->execute();
      $createdID = $sqlStatement->insert_id;
      
      $this->rating->id = $createdID;
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $createdID;
    }
    
    public function ratingAllowed($restaurantID, $username){
      $dinerLoader = new DinerLoader();
      $diner = $dinerLoader->getDinerByEmail($username);
      $createdID    = null;
      $dbConnection = null;
      $sqlStatement = null;
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
         "SELECT * FROM Reservation WHERE restaurant_id = ? AND diner_id = ? AND state_id = 4"
      );
      
      $sqlStatement->bind_param("ss", $restaurantID, $diner->id);
      $sqlResult = $sqlStatement->execute();
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $sqlResult;
    }
}
