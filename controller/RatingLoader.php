<?php

require_once ("model/Rating.php");
require_once ("utils/DatabaseConnectionProvider.php");

class RatingLoader {
    public function __construct ()
    {
    }
    
    public function getRatingsForRestaurant ($restaurantID)
    {
      $ratings       = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                username,
                restaurant_id,
                rating,
                description
         FROM   Rating WHERE restaurant_id = ?
        "
      );
      $sqlStatement->bind_param ("s", $restaurantID);
      $ratings = $this->createRatingsFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $ratings;
    }
    
    private function createRatingsFromSqlStatement ($sqlStatement)
    {
      $ratings = array ();
      
      if ($sqlStatement->execute ())
      {
        $sqlStatement->bind_result
        (
          $id,
          $username,
          $restaurantID,
          $rating,
          $description
        );
        
        while ($row = $sqlStatement->fetch ())
        {
          $rating = new Rating();
          $rating->id = $id;
          $rating->username = $username;
          $rating->restaurantID = $restaurantID;
          $rating->rating = $rating;
          $rating->description = $description;
          $ratings[] = $rating;
        }
      }

      return $ratings;
    }
}
