<?php

class RatingJSONfier {
    public function __construct ()
    {
    }
    
    public function createJSONString (Rating $rating)
    {
      $jsonString         = null;
      $jsonReadyPHPObject = null;
      
      $jsonReadyPHPObject = array
      (
        "id"         => $rating->username,
        "first_name" => $rating->restaurantID,
        "last_name"  => $rating->rating,
        "email"      => $rating->description,
      );
      
      $jsonString = json_encode ($jsonReadyPHPObject);
      return $jsonString;
    }
    
    public function createPHPObjectToJSONfy (Rating $rating)
    {
      $jsonReadyPHPArray = null;
      
      $jsonReadyPHPArray = array
      (
        "username" => $rating->username,
        "restaurant_id" => $rating->restaurantID,
        "rating" => $rating->rating,
        "description" => $rating->description,
      );
      
      return $jsonReadyPHPArray;
    }
}
