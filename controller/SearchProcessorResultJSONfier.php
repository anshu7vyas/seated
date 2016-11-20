<?php

  /* Creates a JSON string representation of a SearchProcessor.
   * 
   * Date:   30.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  require_once ("controller/SearchProcessor.php");
  require_once ("controller/SearchResultRestaurant.php");
  require_once ("controller/SearchResultRestaurantJSONfier.php");
  
  
  class SearchProcessorResultJSONfier
  {
    private $searchResultRestaurantJSONfier;
    private $reduceSingleResultToNonArray;
    
    
    public function __construct ()
    {
      $this->searchResultRestaurantJSONfier = new SearchResultRestaurantJSONfier ();
      $this->reduceSingleResultToNonArray   = false;
    }
    
    
    public function getSearchResultJSONfier ()
    {
      return $this->searchResultRestaurantJSONfier;
    }
    
    public function setReduceSingleResultToNonArray ($reduceToNonArray)
    {
      $this->reduceSingleResultToNonArray = $reduceToNonArray;
    }
    
    
    public function createJSONString (SearchProcessor $searchProcessor)
    {
      $jsonfiedResult          = null;
      $jsonReadyResults        = array ();
      $searchResultRestaurants = $searchProcessor->getFoundSearchResultRestaurants ();
      
      if (count ($searchResultRestaurants) <= 0)
      {
        $jsonfiedResult = array ();
      }
      else if ($this->shouldNotCreateArray ($searchResultRestaurants))
      {
        $jsonReadyResults = $this->searchResultRestaurantJSONfier
                                 ->createPHPObjectToJSONfy ($searchResultRestaurants[0]);
      }
      else
      {
        foreach ($searchResultRestaurants as $searchResultRestaurant)
        {
          $jsonReadyResults[] = $this->searchResultRestaurantJSONfier
                                     ->createPHPObjectToJSONfy ($searchResultRestaurant);
        }
      }
      
      $jsonfiedResult = json_encode ($jsonReadyResults);
      
      return $jsonfiedResult;
    }
    
    
    private function shouldNotCreateArray ($searchResultRestaurants)
    {
      return ((count ($searchResultRestaurants) == 1) &&
              ($this->reduceSingleResultToNonArray));
    }
  }

?>
