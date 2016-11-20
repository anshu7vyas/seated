<?php

  /* Implements a search based on a restaurant's location
   * and popularity.
   * 
   * The "popularity" is defined as the number of reservations for
   * a restaurant currently stored in the database.
   * 
   * Date:   27.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  require_once ("controller/CitySearch.php");
  
  
  class CitySearchJSONfier
  {
    public function __construct ()
    {
    }
    
    
    public function createJSONString (CitySearch $citySearch)
    {
      $jsonString    = null;
      $searchResults = $citySearch->getSearchResults ();
      
      $jsonString = json_encode ($searchResults);
      
      return $jsonString;
    }
  }

?>