<?php

  /* Creates a JSON string representation of a SearchResultRestaurant.
   * 
   * Date:   30.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  require_once ("controller/SearchResultRestaurant.php");
  
  
  class SearchResultRestaurantJSONfier
  {
    private $returnBlocksOnly;
    
    
    public function __construct ()
    {
      $this->returnBlocksOnly = false;
    }
    
    
    public function getReturnsBlocksOnly ()
    {
      return $this->returnBlocksOnly;
    }
    
    public function setReturnsBlocksOnly ($returnsBlocksOnly)
    {
      $this->returnBlocksOnly = $returnsBlocksOnly;
    }
    
    
    public function createJSONString (SearchResultRestaurant $searchResultRestaurant)
    {
      $jsonfiedResult    = null;
      $jsonReadyPHPArray = null;
      
      $jsonReadyPHPArray = $this->createPHPObjectToJSONfy ($searchResultRestaurant);
      $jsonfiedResult    = json_encode ($jsonReadyPHPArray);
      
      return $jsonfiedResult;
    }
    
    public function createPHPObjectToJSONfy (SearchResultRestaurant $searchResultRestaurant)
    {
      $jsonReadyPHPArray = null;
      
      if ($this->returnBlocksOnly)
      {
        $jsonReadyPHPArray = $this->createBlocksOnlyPHPObjectToJSONfy($searchResultRestaurant);
      }
      else
      {
        $jsonReadyPHPArray = $this->createFullPHPObjectToJSONfy($searchResultRestaurant);
      }
      
      return $jsonReadyPHPArray;
    }
    
    private function createFullPHPObjectToJSONfy (SearchResultRestaurant $searchResultRestaurant)
    {
      $jsonReadyPHPArray        = null;
      $resultTimeBlockCompounds = null;
      
      $resultTimeBlockCompounds = $searchResultRestaurant->getResultTimeBlockCompounds ();
      $jsonReadyPHPArray                  = array ();
      $jsonReadyPHPArray['restaurant_id'] = $searchResultRestaurant->getRestaurant ()->id;
      $jsonReadyPHPArray['blocks']        = array ();
      
      foreach ($resultTimeBlockCompounds as $timeBlockCompound)
      {
        $timeBlocksInCompound = $timeBlockCompound->getTimeBlocks ();
        $firstTimeBlock       = $timeBlocksInCompound[0];

        $jsonReadyPHPArray['blocks'][] = array
        (
          'time' => $firstTimeBlock->getTimespan ()
                                   ->getStartTimeAsHourMinuteSecondString (),
          'date' => $firstTimeBlock->getDay      ()
                                   ->getAsYearMonthDayString ()
        );
      }
      
      return $jsonReadyPHPArray;
    }
    
    private function createBlocksOnlyPHPObjectToJSONfy (SearchResultRestaurant $searchResultRestaurant)
    {
      $jsonReadyPHPArray        = null;
      $resultTimeBlockCompounds = null;
      
      $resultTimeBlockCompounds = $searchResultRestaurant->getResultTimeBlockCompounds ();
      
      foreach ($resultTimeBlockCompounds as $timeBlockCompound)
      {
        $timeBlocksInCompound = $timeBlockCompound->getTimeBlocks ();
        $firstTimeBlock       = $timeBlocksInCompound[0];

        $jsonReadyPHPArray[] = array
        (
          'time' => $firstTimeBlock->getTimespan ()
                                   ->getStartTimeAsHourMinuteSecondString (),
          'date' => $firstTimeBlock->getDay      ()
                                   ->getAsYearMonthDayString ()
        );
      }
      
      return $jsonReadyPHPArray;
    }
  }

?>
