<?php

  /* Implements the keyword search functionality.
   * 
   * Date:   26.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  require_once ("controller/CategoryLoader.php");
  require_once ("controller/EncodedNameMatcher.php");
  require_once ("controller/QueryProcessor.php");
  require_once ("controller/RestaurantLoader.php");
  require_once ("model/Category.php");
  require_once ("model/RestaurantModel.php");
  
  
  class KeywordSearch
  {
    const DEFAULT_MAXIMUM_NUMBER_OF_RESULTS = 5;
    
    private $searchString;
    private $prepardSearchString;
    private $maximumNumberOfResults;
    private $restaurantLoader;
    private $categoryLoader;
    
    
    public function __construct ()
    {
      $this->searchString           = null;
      $this->maximumNumberOfResults = self::DEFAULT_MAXIMUM_NUMBER_OF_RESULTS;
      $this->restaurantLoader       = new RestaurantLoader ();
      $this->categoryLoader         = new CategoryLoader   ();
    }
    
    
    public function getSearchString ()
    {
      return $this->searchString;
    }
    
    public function setSearchString ($searchString)
    {
      $this->searchString = $searchString;
      
      if ($this->searchString !== null)
      {
        $this->prepardSearchString = strtolower ($searchString);
      }
      else
      {
        $this->prepardSearchString = null;
      }
    }
    
    
    // Returns a multidimensional array of associative arrays:
    //   [index] => array (
    //                       array (type_1 => "...", value_1 => "..."),
    //                       ...
    //                       array (type_n => "...", value_n => "...")
    //                    )
    public function process ()
    {
      $searchResults   = array ();
      $restaurants     = null;
      $categories      = null;
      $numberOfMatches = 0;
      $cityRequest     = null;      // city=...
      $queryRequest    = null;      // query=...
      $queryProcessor  = null;
      $nameMatcher     = null;
      $desiredCityName = null;
      
      $queryProcessor = new QueryProcessor     ();
      $nameMatcher    = new EncodedNameMatcher ();
      
      $queryProcessor->setQueryString ($this->searchString);
      $queryProcessor->process        ();
      
      if ($queryProcessor->hasQueryParameterOfName ("city"))
      {
        $cityRequest = $queryProcessor->getQueryParameterValue ("city");
      }
      else
      {
        throw new Exception ("KeywordSearch::process(): No 'city' parameter found.");
      }
      
      $desiredCityName = $nameMatcher->getDecodedCityName ($cityRequest);
      $queryRequest    = $queryProcessor->getQueryParameterValueOrSubstitute ("query", null);
      $queryRequest    = $nameMatcher->getEncodedString ($queryRequest);
      
      $restaurants = $this->restaurantLoader->getRestaurants ();
      $categories  = $this->categoryLoader->getCategories    ();
      
      foreach ($restaurants as $restaurant)
      {
        if ($this->hasEnoughMatches ($numberOfMatches))
        {
          break;
        }
        
        if ($restaurant->location == null)
        {
          continue;
        }
        else if ($restaurant->location->city != $desiredCityName)
        {
          continue;
        }
        
        //$desiredQuery = $nameMatcher->getDecodedRestaurantName ($queryRequest);
        
        if ($this->startWithSearchString ($queryRequest, $nameMatcher->getEncodedString ($restaurant->name)))
        {
          $searchResults[] = array
          (
            "type"  => "restaurant",
            "value" => $restaurant->name
          );
          
          $numberOfMatches++;
        }
      }
      
      foreach ($categories as $category)
      {
        if ($this->hasEnoughMatches ($numberOfMatches))
        {
          break;
        }
        
        $categoryText = $nameMatcher->getEncodedString ($category->labelText);
        
        if ($this->startWithSearchString ($queryRequest, $categoryText))
        {
          $searchResults[] = array
          (
            "type"  => "category",
            "value" => $category->labelText
          );
          
          $numberOfMatches++;
        }
      }
      
      return $searchResults;
    }
    
    private function startWithSearchString ($queryString, $stringToCheck)
    {
      if ($stringToCheck !== null)
      {
        $positionOfMatch = strpos ($stringToCheck, $queryString);
        
        if ($positionOfMatch === false)
        {
          return false;
        }
        else if ($positionOfMatch >= 0)
        {
          return true;
        }
        else
        {
          return false;
        }
      }
      else
      {
        return false;
      }
    }
    
    private function hasEnoughMatches ($numberOfMatches)
    {
      return ($numberOfMatches >= $this->maximumNumberOfResults);
    }
  }

?>
