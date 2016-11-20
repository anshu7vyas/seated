<?php


class RestaurantTable
{
  public $id;
  public $restaurant;
  public $numberOfSeats;
  public $description;
  
  
  public function __construct ()
  {
    $this->id            = 0;
    $this->restaurant    = 0;
    $this->numberOfSeats = 0;
    $this->description   = null;
  }
  
  
  public function __toString ()
  {
    $asString = null;

    $asString = sprintf
    (
      "RestaurantTable(id=%s)",
      $this->id
    );

    return $asString;
  }
}
