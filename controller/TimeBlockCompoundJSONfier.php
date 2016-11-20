<?php

  /* Creates a JSON string representation of a TimeBlockCompound.
   * 
   * Date:   05.12.2015
   * Author: Kaveh Yousefi
   */
  
  
  class TimeBlockCompoundJSONfier
  {
    public function __construct ()
    {
    }
    
    
    public function createJSONString (TimeBlockCompound $timeBlockCompound)
    {
      $jsonfiedResult    = null;
      $jsonReadyPHPArray = null;
      
      $jsonReadyPHPArray = $this->createPHPObjectToJSONfy ($timeBlockCompound);
      $jsonfiedResult    = json_encode ($jsonReadyPHPArray);
      
      return $jsonfiedResult;
    }
    
    public function createPHPObjectToJSONfy (TimeBlockCompound $timeBlockCompound)
    {
      $jsonReadyPHPArray    = null;
      $timeBlocksInCompound = $timeBlockCompound->getTimeBlocks ();
      $firstTimeBlock       = $timeBlocksInCompound[0];
      
      $jsonReadyPHPArray = array
      (
        'time' => $firstTimeBlock->getTimespan ()
                                 ->getStartTimeAsHourMinuteSecondString (),
        'date' => $firstTimeBlock->getDay      ()
                                 ->getAsYearMonthDayString ()
      );
      
      return $jsonReadyPHPArray;
    }
  }

?>
