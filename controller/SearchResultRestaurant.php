<?php

  /* Implements the restaurant search functionality.
   *
   * Date:   22.11.2015
   * Author: Kaveh Yousefi
   */

  require_once ("controller/ReservationLoader.php");
  require_once ("controller/SearchResultTable.php");
  require_once ("timemanagement/RestaurantDaySchedule.php");
  require_once ("timemanagement/ScheduleHTMLPrinter.php");
  require_once ("timemanagement/Timespan.php");
  require_once ("timemanagement/TimeBlockCompound.php");


  class SearchResultRestaurant
  {
    const DEFAULT_DURATION_IN_MINUTES = 30;
    const DEFAULT_MAXIMUM_NUMBER_OF_TIMEBLOCK_BEFORE = 2;
    const DEFAULT_MAXIMUM_NUMBER_OF_TIMEBLOCK_AFTER  = 2;

    private $restaurant;
    private $searchParameters;
    private $resultTables;    // Array of all SearchResultTable objects.
    private $restaurantMatches;
    private $hasMatch;
    private $hasPerfectMatch;
    private $searchTimespan;
    private $maximumNumberOfTimeBlocksBefore;
    private $maximumNumberOfTimeBlocksAfter;


    public function __construct ($restaurant, $searchParameters)
    {
      $this->restaurant        = $restaurant;
      $this->searchParameters  = $searchParameters;
      $this->resultTables      = array ();
      $this->restaurantMatches = $this->isRestaurantCandidate ($this->restaurant);
      $this->maximumNumberOfTimeBlocksBefore = self::DEFAULT_MAXIMUM_NUMBER_OF_TIMEBLOCK_BEFORE;
      $this->maximumNumberOfTimeBlocksAfter  = self::DEFAULT_MAXIMUM_NUMBER_OF_TIMEBLOCK_AFTER;
      $this->hasPerfectMatch   = false;
      $this->hasMatch          = false;

      if (! $this->restaurantMatches)
      {
        return;
      }

      $tables = $this->getTablesForRestaurant ($restaurant);
      // Restaurant has no tables? => Remove it from tables array.
      if (empty ($tables))
      {
        $this->hasMatch = false;
        return;
      }
      else
      {
        $searchDay         = null;
        $reservationLoader = null;
        $reservations      = null;

        $searchDay         = $this->searchParameters->getDay ();
        $reservationLoader = new ReservationLoader           ();
        $reservations      = $reservationLoader->getReservationsByRestaurantIDAndDate
        (
          $restaurant->id,
          $searchDay->getAsDateTime ()
        );

        // Remove tables with too few seats.
        foreach ($tables as $tableIndex => $table)
        {
          if (! $this->isTableCandidate ($table))
          {
            unset ($tables[$tableIndex]);
          }
        }

        $this->searchTimespan = Timespan::createFromStartTimeAndDuration
        (
          $this->searchParameters->getTimeAsHourMinuteSecondString (),
          $this->restaurant->getMealDurationTimeInMinutes          ()
        );

        $schedule = new RestaurantDaySchedule
        (
          $restaurant,
          $tables,
          $searchDay,
          self::DEFAULT_DURATION_IN_MINUTES
        );
        $schedule->processReservations ($reservations);


//        $htmlSchedulePrinter = new ScheduleHTMLPrinter ($schedule);
//        print ($htmlSchedulePrinter->getHTMLString ());

        foreach ($tables as $table)
        {
          $searchResultTable = new SearchResultTable
          (
            $table,
            $schedule->getTimeBlocksByTableID ($table->id),
            $this->searchTimespan
          );

          $this->resultTables[] = $searchResultTable;

          // At least one table with perfect match? => Perfect match.
          if ($searchResultTable->hasPerfectMatch ())
          {
            $this->hasPerfectMatch = true;
          }

          // At least one table has a simple match? => Simple match.
          if ($searchResultTable->hasMatch ())
          {
            $this->hasMatch = true;
          }


//          // TEST: Print restaurants before, at & after the desired time.
//          if ($searchResultTable->hasMatch ())
//          {
//            $compounds = $searchResultTable->getTimeBlockCompoundsBefore ();
//
//            foreach ($compounds as $comp)
//            {
//              print ("<code>BEFORE:::</code> " . $comp . "<br />");
//            }
//            $compounds = $searchResultTable->getTimeBlockCompoundsAtOrAfter ();
//
//            foreach ($compounds as $comp)
//            {
//              print ("<code>AT/AFTER:</code> " . $comp . "<br />");
//            }
//          }
//          /// END OF TEST PRINT ////////////////////////////////////////
        }


//        //// --- START OF TEST PRINT ------------------------------- ////
//        $bestResultTable = $this->getBestResultTable ();
//
//        if ($this->hasPerfectMatch)
//        {
//          print ("PERFECT: ");
//          printf ('<div style="font-weight : bold; color : red;">%s</div>',
//                  $this->getBestResultTable ()->getTimeBlockCompoundAt ());
//        }
//
//        if ($this->hasMatch)
//        {
//          $freeBlocksBefore = $bestResultTable->getTimeBlockCompoundsBefore ();
//          $freeBlocksBefore = array_reverse ($freeBlocksBefore);
//          $freeBlocksAfter  = $bestResultTable->getTimeBlockCompoundsAtOrAfter ();
//          $freeBlocksBefore = array_slice ($freeBlocksBefore, 0, 2, true);
//          $freeBlocksAfter  = array_slice ($freeBlocksAfter,  0, 2, true);
//          $freeBlocksBefore = array_reverse ($freeBlocksBefore);
//
//          if (count ($freeBlocksBefore) > 0)
//          {
//            print ("BEFORE: ");
//            foreach ($freeBlocksBefore as $freeBlockBefore)
//            {
//              printf ('<div style="font-weight : bold; color : blue;">%s</div>',
//                      $freeBlockBefore);
//            }
//          }
//
//          if (count ($freeBlocksAfter) > 0)
//          {
//            print ("AFTER: ");
//            foreach ($freeBlocksAfter as $freeBlockAfter)
//            {
//              printf ('<div style="font-weight : bold; color : magenta;">%s</div>',
//                      $freeBlockAfter);
//            }
//          }
//        }
//        else
//        {
//          printf ("<div>NO MATCH!</div>");
//        }


        /*
        $resultTimeBlockCompounds = $this->getResultTimeBlockCompounds ();
        $jsonfiedResult           = null;
        $jsonReadyPHPArray        = array ();
        $jsonReadyPHPArray['restaurant_id'] = $this->restaurant->id;
        $jsonReadyPHPArray['blocks']        = array ();
        foreach ($resultTimeBlockCompounds as $timeBlockCompound)
        {
          $timeBlocksInCompound = $timeBlockCompound->getTimeBlocks ();
          $firstTimeBlock       = $timeBlocksInCompound[0];

          $jsonReadyPHPArray['blocks'][] = array
          (
            'time' => $firstTimeBlock->getTimespan ()->getStartTimeAsHourMinuteSecondString (),
            'date' => $firstTimeBlock->getDay      ()->getAsYearMonthDayString              ()
          );
        }
        $jsonfiedResult = json_encode ($jsonReadyPHPArray);
        printf ('<div style="height : 70px; padding : 10px; background-color : yellow; font-weight : bold;">JSONfied result:
                   <p>%s</p>
                 </div>',
                 $jsonfiedResult);
        //// --- END OF TEST PRINT -------------------------------- ////
         *
         */
      }
    }


    public function getRestaurant ()
    {
      return $this->restaurant;
    }

    public function restaurantMatches ()
    {
      return $this->restaurantMatches;
    }

    public function hasMatch ()
    {
      return $this->hasMatch;
    }

    public function hasPerfectMatch ()
    {
      return $this->hasPerfectMatch;
    }

    public function getResultTables ()
    {
      return $this->resultTables;
    }

    public function addResultTable ($resultTable)
    {
      $this->resultTables[] = $resultTable;
    }

    public function getBestResultTable ()
    {
      $bestResultTable      = null;
      $bestResultTables     = null;
      $minimumNumberOfSeats = 0;

      $bestResultTables = $this->getResultTablesWithPerfectMatch ();

      /* No perfect matching result table?
       * => Find those with the least number of time blocks distance to
       *    the desired reservation timespan.
       */
      if (empty ($bestResultTables))
      {
        $bestResultTables = $this->getResultTablesWithMinimumDistance ();
      }

      // No results at all? => No result at all (= NULL).
      if (empty ($bestResultTables))
      {
        return null;
      }

      $bestResultTable      = $bestResultTables[0];
      $minimumNumberOfSeats = $bestResultTable->getTable ()->numberOfSeats;

      foreach ($bestResultTables as $currentResultTable)
      {
        $currentTable         = $currentResultTable->getTable ();
        $currentNumberOfSeats = $currentTable->numberOfSeats;

        if ($currentNumberOfSeats < $minimumNumberOfSeats)
        {
          $minimumNumberOfSeats = $currentNumberOfSeats;
          $bestResultTable      = $currentResultTable;
        }
      }

      return $bestResultTable;
    }


    public function getResultTimeBlockCompounds ()
    {
      $resultTimeBlockCompounds = array ();
      $bestResultTable          = $this->getBestResultTable ();
      
      // Leads to duplicate best times.
//      if ($this->hasPerfectMatch)
//      {
//        $resultTimeBlockCompounds[] = $bestResultTable->getTimeBlockCompoundAt ();
//      }

      if ($this->hasMatch)
      {
        $freeBlocksBefore = $bestResultTable->getTimeBlockCompoundsBefore ();
        // We need the LAST (= LATEST) two time points.
        $freeBlocksBefore = array_reverse ($freeBlocksBefore);
        $freeBlocksBefore = array_slice
        (
          $freeBlocksBefore,
          0,
          $this->maximumNumberOfTimeBlocksBefore,
          false
        );

        $freeBlocksAfter  = $bestResultTable->getTimeBlockCompoundsAtOrAfter ();
        $freeBlocksAfter  = array_slice
        (
          $freeBlocksAfter,
          0,
          $this->maximumNumberOfTimeBlocksAfter,
          false
        );

        if (count ($freeBlocksBefore) > 0)
        {
          // We want to return the time points in ASCENDING order.
          $freeBlocksBefore         = array_reverse ($freeBlocksBefore);
          $resultTimeBlockCompounds = array_merge
          (
            $freeBlocksBefore,
            $resultTimeBlockCompounds
          );
        }

        if (count ($freeBlocksAfter) > 0)
        {
          $resultTimeBlockCompounds = array_merge
          (
            $resultTimeBlockCompounds,
            $freeBlocksAfter
          );
        }
      }

      return $resultTimeBlockCompounds;
    }



    ////////////////////////////////////////////////////////////////////
    // -- Implementation of auxiliary methods.                     -- //
    ////////////////////////////////////////////////////////////////////

    private function getResultTablesWithPerfectMatch ()
    {
      $resultTablesWithPerfectMatch = array ();

      foreach ($this->resultTables as $resultTable)
      {
        if ($resultTable->hasPerfectMatch ())
        {
          $resultTablesWithPerfectMatch[] = $resultTable;
        }
      }

      return $resultTablesWithPerfectMatch;
    }

    private function getResultTablesWithMinimumDistance ()
    {
      $matchingResultTables = array ();
      $minimumTotalDistance = $this->getMinimumTotalDistanceOfResultTables ();

      foreach ($this->resultTables as $resultTable)
      {
        if ($resultTable->getTotalDistanceToTimeBlockAt () === $minimumTotalDistance)
        {
          $matchingResultTables[] = $resultTable;
        }
      }

      return $matchingResultTables;
    }

    private function getMinimumTotalDistanceOfResultTables ()
    {
      if ($this->hasMatch)
      {
        return min ($this->getTotalBlockDistancesOfResultTables ());
      }
      else
      {
        return null;
      }
    }

    private function getTotalBlockDistancesOfResultTables ()
    {
      $distances = array ();

      foreach ($this->resultTables as $resultTable)
      {
        if ($resultTable->hasMatch ())
        {
          $distances[] = $resultTable->getTotalDistanceToTimeBlockAt ();
        }
      }

      return $distances;
    }


    private function isRestaurantCandidate (RestaurantModel $restaurant)
    {
      return ($this->isRightRestaurant  ($restaurant) &&
              $this->hasRightCityName   ($restaurant) &&
              $this->hasRightLocation   ($restaurant) &&
              $this->hasRightCategories ($restaurant));
    }

    private function isRightRestaurant (RestaurantModel $restaurant)
    {
      $desiredRestaurantID = $this->searchParameters->targetRestaurantID;

      if (($desiredRestaurantID === null) || ($desiredRestaurantID === 0))
      {
        return true;
      }
      else if ($restaurant->id == null)
      {
        return false;
      }
      else
      {
        return ($restaurant->id == $desiredRestaurantID);
      }
    }

    private function hasRightCityName (RestaurantModel $restaurant)
    {
      $desiredCityName = $this->searchParameters->cityName;

      if ($desiredCityName === null)
      {
        return true;
      }
      else if ($restaurant->location == null)
      {
        return false;
      }
      else
      {
        return ($restaurant->location->city == $desiredCityName);
      }
    }

    private function hasRightLocation (RestaurantModel $restaurant)
    {
//      $locationID = $this->searchParameters->targetLocationID;
//
//      if (($locationID === null) || ($locationID === 0))
//      {
//        return true;
//      }
//      else if ($restaurant->location == null)
//      {
//        return false;
//      }
//      else
//      {
//        return ($restaurant->location->id == $locationID);
//      }
      if ($this->searchParameters->cityName === null)
      {
        return true;
      }
      else if ($restaurant->location == null)
      {
        return false;
      }
      else
      {
        return ($restaurant->location->city == $this->searchParameters->cityName);
      }
    }

    private function hasRightCategories ($restaurant)
    {
      $desiredCategory = $this->searchParameters->categories;
      // echo($desiredCategory);
      if ($desiredCategory == null)
      {
        return true;
      }
      else if (empty ($desiredCategory))
      {
        return true;
      }
      else
      {
        // Collect the restaurant's category IDs.
        $categoriesOfRestaurant = $restaurant->getCategories();

        // Check if each desired category ID is in the restaurant's.
        foreach ($categoriesOfRestaurant as $categoryToCheck)
        {
          if ($categoryToCheck->labelText == $desiredCategory)
          {
            return true;
          }
        }
        return false;
      }
    }


    private function getTablesForRestaurant ($restaurant)
    {
      $tablesForRestaurant = null;
      $tableLoader         = null;

      $tableLoader         = new RestaurantTableLoader ();
      $tablesForRestaurant = $tableLoader->getTablesByRestaurantID ($restaurant->id);

      return $tablesForRestaurant;
    }

    private function isTableCandidate ($table)
    {
      return ($this->hasEnoughSeats ($table));
    }

    private function hasEnoughSeats ($table)
    {
      return ($table->numberOfSeats >= $this->searchParameters->partySize);
    }
  }

?>

