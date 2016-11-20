<?php
  
  /* Encapsulates the data necessary for a registration as a host.
   * 
   * Date:   25.11.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("authorization/RegistrationCredentials.php");
  require_once ("controller/HostWriter.php");
  require_once ("controller/RestaurantLoader.php");
  
  
  class   HostRegistrationCredentials
  extends RegistrationCredentials
  {
    public $restaurantID;
    
    
    public function __construct ()
    {
      parent::__construct ();
      $this->restaurantID = null;
      $this->userType     = UserType::USER_TYPE_HOST;
    }
    
    
    public function checkForValidity ()
    {
      if ($this->firstName == null)
      {
        throw new Exception ("First name is null.");
      }
      if ($this->lastName == null)
      {
        throw new Exception ("Last name is null.");
      }
      if ($this->email == null)
      {
        throw new Exception ("E-Mail is null.");
      }
      if ($this->restaurantID == null)
      {
        throw new Exception ("No restaurant ID.");
      }
      
      return true;
    }
    
    public function persist ()
    {
      $hostWriter       = new HostWriter ();
      $host             = new Host       ();
      $restaurantLoader = new RestaurantLoader ();
      
      $host->firstName  = $this->firstName;
      $host->lastName   = $this->lastName;
      $host->email      = $this->email;
      $host->restaurant = $restaurantLoader->getRestaurantByID ($this->restaurantID);
      $host->position   = $this->position;
      
      $hostWriter->setHost ($host);
      $hostWriter->persist ();
      
      return $host;
    }
  }

?>
