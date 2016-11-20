<?php
  
  /* Prints a "timemanagement/RestaurantDaySchedule" instance
   * for testing purposes.
   * 
   * NOTE: This class can be deleted, after the time management module
   *       has been either approved correct or discarded.
   *       This class has no production value.
   * 
   * Date:   26.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  class ScheduleHTMLPrinter
  {
    private $restaurantDaySchedule;
    
    
    public function __construct ($restaurantDaySchedule)
    {
      $this->restaurantDaySchedule = $restaurantDaySchedule;
    }
    
    
    public function getHTMLString ()
    {
      $htmlString             = null;
      $timeBlocksInTableOrder = $this->restaurantDaySchedule->getTimeBlocks ();
      $timeBlocksInBlockOrder = $this->restaurantDaySchedule->getTimeBlocksByBlockOrder ();
      
      $htmlString = sprintf
      (
        '<h1>%s [ID = %d | Day = %s | Meal duration in minutes = %s]</h1>
         <table>
           <tr>
           ',
         $this->restaurantDaySchedule->restaurant->name,
         $this->restaurantDaySchedule->restaurant->id,
         $this->restaurantDaySchedule->getDay (),
         $this->restaurantDaySchedule->restaurant->getMealDurationTimeInMinutes ()
      );
      
      $isFirstColumn = true;
      
      // Create table header.
      foreach ($timeBlocksInTableOrder as $tableID => $timeBlocks)
      {
        if ($isFirstColumn)
        {
          $htmlString = $htmlString .
            '<th>Timespan</th>
            ';
          $isFirstColumn = false;
        }
        
        $htmlString = $htmlString . sprintf
        (
          '<th>Table %d</th>
          ',
          $tableID
        );
      }
      
      $htmlString = $htmlString . '
        </tr>
      ';
      
      foreach ($timeBlocksInBlockOrder as $timeBlocks)
      {
        $isFirstColumn = true;
        
        foreach ($timeBlocks as $timeBlock)
        {
          if ($isFirstColumn)
          {
            $htmlString = $htmlString . sprintf
            (
              '<tr>
                 <td>%s - %s</td>
              ',
              $timeBlock->getTimespan ()->getStartTimeAsHourMinuteSecondString (),
              $timeBlock->getTimespan ()->getEndTimeAsHourMinuteSecondString   ()
            );
            $isFirstColumn = false;
          }
          
          
          $htmlString = $htmlString . sprintf
          (
            '<td style="background-color : %s;">%s</td>
            ',
            ($timeBlock->isOccupied ()) ? "red"      : "lime",
//            ($timeBlock->isOccupied ()) ? "occupied" : "free"
            ($timeBlock->isOccupied ()) ? $timeBlock->getReservation ()->state->name : "free"
          );
        }
        
        $htmlString = $htmlString . '
          </tr>
        ';
      }
      
      $htmlString = $htmlString . '</table>
      ';
      
      return $htmlString;
    }
  }
  
?>
