<?php

  /* Matches encoded and decoded names, for instance, those of cities
   * and restaurants.
   * 
   * Date:   30.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  require_once ("controller/LocationLoader.php");
  require_once ("controller/RestaurantLoader.php");
  require_once ("model/Location.php");
  require_once ("model/RestaurantModel.php");
  
  
  class EncodedNameMatcher
  {
    private $restaurantLoader;
    private $locationLoader;
    
    
    public function __construct ()
    {
      $this->restaurantLoader = new RestaurantLoader ();
      $this->locationLoader   = new LocationLoader   ();
    }
    
    
    public function getDecodedRestaurantName ($restaurantNameToDecode)
    {
      $restaurants = $this->restaurantLoader->getRestaurants ();
      
      foreach ($restaurants as $restaurant)
      {
        $originalRestaurantName = $restaurant->name;
        $encodedName            = $this->encodeString ($originalRestaurantName);        
        //printf ("%s -> %s<br />", $originalRestaurantName, $encodedName);
        
        if ($encodedName == $restaurantNameToDecode)
        {
          return $originalRestaurantName;
        }
      }
      
      return null;
    }
    
    public function getDecodedCityName ($cityNameToDecode)
    {
      $locations = $this->locationLoader->getLocations ();
      
      foreach ($locations as $location)
      {
        $originalCityName = $location->city;
        $encodedName      = $this->encodeString ($originalCityName);
        
        if (strcasecmp ($encodedName, $cityNameToDecode) == 0)
        {
          return $originalCityName;
        }
      }
      
      return null;
    }
    
    public function getEncodedString ($stringToEncode)
    {
      return $this->encodeString ($stringToEncode);
    }
    
    
    
    ////////////////////////////////////////////////////////////////////
    // -- Implementation of auxiliary methods.                     -- //
    ////////////////////////////////////////////////////////////////////
    
    // -> "http://stackoverflow.com/questions/11330480/strip-php-variable-replace-white-spaces-with-dashes"
    private function encodeString ($stringToEncode)
    {
      $encodedName = null;
      
      $encodedName = $stringToEncode;
      $encodedName = strtolower ($encodedName);
      //$encodedName = urlencode  ($encodedName);
      $encodedName = preg_replace ("/[^a-zA-Z0-9\s]/", "", $encodedName);
      $encodedName = preg_replace ("/[\s-]+/", " ", $encodedName);
      $encodedName = preg_replace ("/[\s]/",   "-", $encodedName);
      
      return $encodedName;
    }
  }

?>
