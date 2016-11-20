<?php  
  class Rating
  {
    public $id;
    public $username;
    public $restaurantID;
    public $rating;
    public $description;   
    
    public function __construct ()
    {
      $this->id           = 0;
      $this->username     = null;
      $this->restaurantID = 0;
      $this->rating       = 0;
      $this->description  = null;
    }
  }

?>
