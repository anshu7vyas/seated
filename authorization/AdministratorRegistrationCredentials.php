<?php

  /* Encapsulates the data necessary for a registration as
   * an administrator.
   * 
   * Date:   02.12.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("authorization/RegistrationCredentials.php");
  //require_once ("controller/AdministratorWriter.php");
  require_once ("controller/RestaurantLoader.php");
  
  
  class   AdministratorRegistrationCredentials
  extends RegistrationCredentials
  {
    public function __construct ()
    {
      parent::__construct ();
      $this->userType = UserType::USER_TYPE_ADMIN;
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
//      if ($this->restaurantID == null)
//      {
//        throw new Exception ("No restaurant ID.");
//      }
      
      return true;
    }
    
    public function persist ()
    {
      $administrator = null;
      //$adminWriter   = null;
      
      /*
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
       * 
       */
      
      return $administrator;
    }
  }
  
?>
