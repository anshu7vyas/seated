<?php
  
  /* Represents a collection of a certain number of TimeBlocks with no
   * gaps between them.
   * 
   * The TimeBlockCompound is capable of collecting TimeBlocks on his
   * own, rejecting further insertions, after its capacity
   * ($desiredNumberOfBlocks) has been reached.
   * 
   * Date:   27.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  class TimeBlockCompound
  {
    private $desiredNumberOfBlocks;   // Capacity.
    private $timeBlocks;
    
    
    public function __construct ($desiredNumberOfBlocks)
    {
      $this->desiredNumberOfBlocks = $desiredNumberOfBlocks;
      $this->timeBlocks            = array ();
    }
    
    
    public function getTimeBlocks ()
    {
      return $this->timeBlocks;
    }
    
    public function isFull ()
    {
      return ($this->getNumberOfTimeBlocks () ===
              $this->desiredNumberOfBlocks);
    }
    
    public function getNumberOfTimeBlocks ()
    {
      return count ($this->timeBlocks);
    }
    
    public function getCapacity ()
    {
      return $this->desiredNumberOfBlocks;
    }
    
    // If $timeBlock is null     => throws Exception.
    // If $timeBlock is occupied => returns false.
    // If array      is full     => returns false.
    // Otherwise                 => add element at array head.
    public function tryToAddAtBeginning (TimeBlock $timeBlock)
    {
      if ($timeBlock === null)
      {
        throw Exception ("TimeBlockCompound::tryToAdd(): TimeBlock is null.");
      }
      
      if ($timeBlock->isOccupied ())
      {
        return false;
      }
      else if ($this->isFull ())
      {
        return false;
      }
      else
      {
        //$this->timeBlocks[] = $timeBlock;
        array_unshift ($this->timeBlocks, $timeBlock);
        
        return true;
      }
    }
    
    public function tryToAddAtEnd (TimeBlock $timeBlock)
    {
      if ($timeBlock === null)
      {
        throw Exception ("TimeBlockCompound::tryToAdd(): TimeBlock is null.");
      }
      
      if ($timeBlock->isOccupied ())
      {
        return false;
      }
      else if ($this->isFull ())
      {
        return false;
      }
      else
      {
        $this->timeBlocks[] = $timeBlock;
        
        return true;
      }
    }
    
    
    public function __toString ()
    {
      $asString       = null;
      $firstBlock     = null;
      $lastBlock      = null;
      $numberOfBlocks = 0;
      
      $asString       = "TimeBlockCompound(";
      $numberOfBlocks = $this->getNumberOfTimeBlocks ();
      
      if ($numberOfBlocks > 1)
      {
        $firstBlock = $this->timeBlocks[0];
        $lastBlock  = $this->timeBlocks[$numberOfBlocks - 1];
        
        $asString .= sprintf
        (
          "Table ID=%d, from %s to %s, occupancy=%d/%d, isFull=%d)",
          $firstBlock->getTable    ()->id,
          $firstBlock->getTimespan ()->getStartTimeAsHourMinuteSecondString (),
          $lastBlock->getTimespan  ()->getEndTimeAsHourMinuteSecondString   (),
          $numberOfBlocks,
          $this->desiredNumberOfBlocks,
          $this->isFull ()
        );
      }
      else
      {
        $asString .= sprintf
        (
          "Table ID=%d, occupancy=%d/%d, isFull=%d)",
          $firstBlock->getTable    ()->id,
          $numberOfBlocks,
          $this->desiredNumberOfBlocks,
          $this->isFull ()
        );
      }
      
      return $asString;
    }
  }

?>
