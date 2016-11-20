<?php
  
  /* Implements a restaurant table as part of a found restaurant.
   * 
   * A SearchResultTable contains all blocks of a certain table at
   * a given day, with respect to the desired diner search time. Thus,
   * free block before, at and after the search time can be addressed.
   * 
   * Date:   22.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  require_once ("timemanagement/TimeBlockCompound.php");
  
  
  class SearchResultTable
  {
    private $table;            // The table.
    private $timeBlocks;       // All time blocks for this table.
    private $searchTimespan;   // Start time + meal duration time.
    private $hasMatch;         // At least one free time block?
    private $hasPerfectMatch;  // Enough free blocks for desired time?
    
    
    public function __construct ($table, $timeBlocks, $searchTimespan)
    {
      $this->table           = $table;
      $this->timeBlocks      = $timeBlocks;
      $this->searchTimespan  = $searchTimespan;
      
      if (empty ($timeBlocks))
      {
        $this->hasMatch        = false;
        $this->hasPerfectMatch = false;
      }
      else
      {
        $this->hasMatch        = $this->checkForMatch       ();
        $this->hasPerfectMatch = $this->checkIfPerfectMatch ();
      }
    }
    
    
    public function getTable ()
    {
      return $this->table;
    }
    
    public function hasMatch ()
    {
      return $this->hasMatch;
    }
    
    public function hasPerfectMatch ()
    {
      return $this->hasPerfectMatch;
    }
    
    public function getTimeBlocks ()
    {
      return $this->timeBlocks;
    }
    
    
    
    public function getTimeBlockCompoundsBefore ()
    {
      $timeBlockCompounds        = array ();
      $minimumNumberOfFreeBlocks = $this->getNumberOfBlocksForReservation ();
      $timeBlocksBefore          = $this->getTimeBlocksBefore ();
      $timeBlocksBefore          = array_reverse ($timeBlocksBefore);
      $timeBlockCompound         = new TimeBlockCompound ($minimumNumberOfFreeBlocks);
      
      foreach ($timeBlocksBefore as $timeBlock)
      {
        if ($timeBlockCompound->isFull ())
        {
          array_unshift ($timeBlockCompounds, $timeBlockCompound);
          $timeBlockCompound = new TimeBlockCompound ($minimumNumberOfFreeBlocks);
        }
        
        $canAdd = $timeBlockCompound->tryToAddAtBeginning ($timeBlock);

        if (! $canAdd)
        {
          $timeBlockCompound = new TimeBlockCompound ($minimumNumberOfFreeBlocks);
        }
      }
      
      if ($timeBlockCompound->isFull ())
      {
        array_unshift ($timeBlockCompounds, $timeBlockCompound);
      }
      
      return $timeBlockCompounds;
    }
    
    public function getTimeBlockCompoundsAtOrAfter ()
    {
      $timeBlockCompounds        = array ();
      $minimumNumberOfFreeBlocks = $this->getNumberOfBlocksForReservation ();
      $timeBlocksAtOrAfter       = $this->getTimeBlocksAtOrAfter ();
      $timeBlockCompound         = new TimeBlockCompound ($minimumNumberOfFreeBlocks);
      
      foreach ($timeBlocksAtOrAfter as $timeBlock)
      {
        if ($timeBlockCompound->isFull ())
        {
          $timeBlockCompounds[] = $timeBlockCompound;
          $timeBlockCompound    = new TimeBlockCompound ($minimumNumberOfFreeBlocks);
        }
        
        $canAdd = $timeBlockCompound->tryToAddAtEnd ($timeBlock);

        if (! $canAdd)
        {
          $timeBlockCompound = new TimeBlockCompound ($minimumNumberOfFreeBlocks);
        }
      }
      
      if ($timeBlockCompound->isFull ())
      {
        $timeBlockCompounds[] = $timeBlockCompound;
      }
      
      return $timeBlockCompounds;
    }
    
    public function getTimeBlockCompoundAt ()
    {
      $timeBlockCompoundsAtOrAfter = $this->getTimeBlockCompoundsAtOrAfter ();
      
      if (count ($timeBlockCompoundsAtOrAfter) > 0)
      {
        return $timeBlockCompoundsAtOrAfter[0];
      }
      else
      {
        return null;
      }
    }
    
    public function getTimeBlockCompoundsAfter ()
    {
      $timeBlockCompounds        = array ();
      $minimumNumberOfFreeBlocks = $this->getNumberOfBlocksForReservation ();
      $timeBlocksAfter           = $this->getTimeBlocksAfter ();
      $timeBlockCompound         = new TimeBlockCompound ($minimumNumberOfFreeBlocks);
      
      foreach ($timeBlocksAfter as $timeBlock)
      {
        if ($timeBlockCompound->isFull ())
        {
          $timeBlockCompounds[] = $timeBlockCompound;
          $timeBlockCompound    = new TimeBlockCompound ($minimumNumberOfFreeBlocks);
        }
        else
        {
          $canAdd = $timeBlockCompound->tryToAddAtEnd ($timeBlock);
          
          if (! $canAdd)
          {
            $timeBlockCompound = new TimeBlockCompound ($minimumNumberOfFreeBlocks);
          }
        }
      }
      
      if ($timeBlockCompound->isFull ())
      {
        $timeBlockCompounds[] = $timeBlockCompound;
      }
      
      return $timeBlockCompounds;
    }
    
    private function getTimeBlockAtStartOfSearchTimespan ()
    {
      foreach ($this->timeBlocks as $timeBlock)
      {
        if ($timeBlock->intersectsDateTime ($this->searchTimespan->getStartTime ()))
        {
          return $timeBlock;
        }
      }
    }
    
    private function getNumberOfBlocksForReservation ()
    {
      $mealDurationTime = $this->searchTimespan->getDurationInMinutes ();
      $firstTimeBlock   = $this->timeBlocks[0];
      $timeBlockSize    = $firstTimeBlock->getTimespan ()
                                         ->getDurationInMinutes ();
      
      return ($mealDurationTime / $timeBlockSize);
    }
    
    
    
    
    
    public function getTimeBlocksBefore ()
    {
      $timeBlocksBefore = array ();
      
      foreach ($this->timeBlocks as $timeBlock)
      {
        if ($timeBlock->getTimespan ()->isBeforeOtherTimespan ($this->searchTimespan))
        {
          $timeBlocksBefore[] = $timeBlock;
        }
        else
        {
          break;
        }
      }
      
      return $timeBlocksBefore;
    }
    
    public function getFreeTimeBlocksBefore ()
    {
      $freeTimeBlocksBefore = null;
      $timeBlocksBefore     = null;
      
      $freeTimeBlocksBefore = array ();
      $timeBlocksBefore     = $this->getTimeBlocksBefore ();
      
      foreach ($timeBlocksBefore as $timeBlock)
      {
        if (! $timeBlock->isOccupied ())
        {
          $freeTimeBlocksBefore[] = $timeBlock;
        }
      }
      
      return $freeTimeBlocksBefore;
    }
    
    public function getTimeBlocksAtOrAfter ()
    {
      $timeBlocksAtOrAfter = array ();
      
      foreach ($this->timeBlocks as $timeBlock)
      {
        if (! $timeBlock->getTimespan ()->isBeforeOtherTimespan ($this->searchTimespan))
        {
          $timeBlocksAtOrAfter[] = $timeBlock;
        }
      }
      
      return $timeBlocksAtOrAfter;
    }
    
    public function getTimeBlocksAfter ()
    {
      $timeBlocksAfter = array ();
      
      foreach ($this->timeBlocks as $timeBlock)
      {
        if ($timeBlock->getTimespan ()->isAfterOtherTimespan ($this->searchTimespan))
        {
          $timeBlocksAfter[] = $timeBlock;
        }
      }
      
      return $timeBlocksAfter;
    }
    
    public function getFreeTimeBlocksAfter ()
    {
      $freeTimeBlocksAfter = null;
      $timeBlocksAfter     = null;
      
      $freeTimeBlocksAfter = array ();
      $timeBlocksAfter     = $this->getTimeBlocksAfter ();
      
      foreach ($timeBlocksAfter as $timeBlock)
      {
        if (! $timeBlock->isOccupied ())
        {
          $freeTimeBlocksAfter[] = $timeBlock;
        }
      }
      
      return $freeTimeBlocksAfter;
    }
    
    public function getTimeBlocksAt ()
    {
      $timeBlocksAt = array ();
      
      foreach ($this->timeBlocks as $timeBlock)
      {
        if ($timeBlock->getTimespan ()->intersects ($this->searchTimespan))
        {
          $timeBlocksAt[] = $timeBlock;
        }
      }
      
      return $timeBlocksAt;
    }
    
    public function getFreeTimeBlocksAt ()
    {
      $freeTimeBlocksAt = array ();
      $timeBlocksAt     = $this->getTimeBlocksAt ();
      
      foreach ($timeBlocksAt as $timeBlockAt)
      {
        if (! $timeBlockAt->isOccupied ())
        {
          $freeTimeBlocksAt[] = $timeBlockAt;
        }
      }
      
      return $freeTimeBlocksAt;
    }
    
    /* Returns associative array:
     *   ["isEmpty"]    => Got intersection at all?
     *   ["startIndex"] => Index in $this->timeBlocks where intersection starts.
     *   ["endIndex"]   => Index in $this->timeBlocks where intersection ends.
     */
    public function getStartAndEndIndexOfTimeBlocksAt ()
    {
      $indicesOfTimeBlockAt = null;
      $isEmpty              = true;   // No start and end indices found?
      $startIndex           = -1;     // Where intersection starts.
      $endIndex             = -1;     // Where intersection ends.
      $currentBlockIndex    =  0;     // Current time block's index.
      
      foreach ($this->timeBlocks as $timeBlock)
      {
        $currentTimespan = $timeBlock->getTimespan ();
        
        // Found an intersection? => Store time block index as start.
        if ($currentTimespan->intersects ($this->searchTimespan))
        {
          $startIndex = $currentBlockIndex;
          $isEmpty    = false; 
        }
        // Block AFTER intersection? => Store time block index as end.
        else if ($currentTimespan->isAfterOtherTimespan ($this->searchTimespan))
        {
          $endIndex = ($currentBlockIndex - 1);
        }
        
        $currentBlockIndex++;
      }
      
      /* Found a start index, but no end index?
       * => Search timespan spans last time block.
       */
      if (($startIndex !== -1) && ($endIndex === -1))
      {
        $endIndex = (count ($this->timeBlocks) - 1);
      }
      
      $indicesOfTimeBlockAt = array
      (
        "isEmpty"    => $isEmpty,
        "startIndex" => $startIndex,
        "endIndex"   => $endIndex
      );
      
      return $indicesOfTimeBlockAt;
    }
    
    public function getTotalDistanceToTimeBlockAt ()
    {
      $distanceBefore = $this->getDistanceOfNearestTimeBlockBefore ();
      $distanceAfter  = $this->getDistanceOfNearestTimeBlockAfter  ();
      
      return ($distanceBefore + $distanceAfter);
    }
    
    public function getDistanceOfNearestTimeBlockBefore ()
    {
      $distanceBefore       = 0;
      $timeBlocksBefore     = array_reverse ($this->getTimeBlocksBefore ());
      $numberOfBlocksBefore = count ($timeBlocksBefore);
      
      if ($numberOfBlocksBefore <= 0)
      {
        $distanceBefore = 0;
      }
      else
      {
        // Count the blocks, until the first unoccupied block appears.
        foreach ($timeBlocksBefore as $timeBlock)
        {
          if ($timeBlock->isOccupied ())
          {
            $distanceBefore++;
          }
          else
          {
            break;
          }
        }
      }
      
      return $distanceBefore;
    }
    
    public function getDistanceOfNearestTimeBlockAfter ()
    {
      $distanceAfter       = 0;
      $timeBlocksAfter     = $this->getTimeBlocksAfter();
      $numberOfBlocksAfter = count ($timeBlocksAfter);
      
      if ($numberOfBlocksAfter <= 0)
      {
        $distanceAfter = 0;
      }
      // Find FIRST unoccupied time block.
      else
      {
        foreach ($timeBlocksAfter as $timeBlock)
        {
          if ($timeBlock->isOccupied ())
          {
            $distanceAfter++;
          }
          else
          {
            break;
          }
        }
      }
      
      return $distanceAfter;
    }
    
    
    
    ////////////////////////////////////////////////////////////////////
    // -- Implementation of auxiliary methods.                     -- //
    ////////////////////////////////////////////////////////////////////
    
    private function checkForMatch ()
    {
      if ((count ($this->getTimeBlockCompoundsBefore    ()) > 0) ||
          (count ($this->getTimeBlockCompoundsAtOrAfter ()) > 0))
      {
        return true;
      }
      else
      {
        return false;
      }
    }
    
    private function checkIfPerfectMatch ()
    {
      $timeBlocksAt = $this->getTimeBlocksAt ();
      
      if (count ($timeBlocksAt) <= 0)
      {
        return false;
      }
      
      foreach ($timeBlocksAt as $timeBlockAt)
      {
        if ($timeBlockAt->isOccupied ())
        {
          return false;
        }
      }
      
      return true;
    }
  }
  
?>
