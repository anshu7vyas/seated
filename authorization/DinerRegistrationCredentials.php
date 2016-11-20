<?php
  
  /* Encapsulates the data necessary for a registration as a host.
   * 
   * Date:   25.11.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("authorization/RegistrationCredentials.php");
  require_once ("controller/DinerWriter.php");
  require_once ("controller/RestaurantLoader.php");
  require_once ("model/Diner.php");
  
  
  class   DinerRegistrationCredentials
  extends RegistrationCredentials
  {
    public function __construct ()
    {
      parent::__construct ();
      $this->userType     = UserType::USER_TYPE_DINER;
    }
    
    
    public function checkForValidity ()
    {
      /*
      if ($this->firstName == null)
      {
        throw new Exception ("First name is null.");
      }
      if ($this->lastName == null)
      {
        throw new Exception ("Last name is null.");
      }
       * 
       */
      if ($this->email == null)
      {
        throw new Exception ("E-Mail is null.");
      }
      
      return true;
    }
    
    public function persist ()
    {
      $dinerWriter      = new DinerWriter ();
      $diner            = new Diner       ();
      
      $diner->firstName  = $this->firstName;
      $diner->lastName   = $this->lastName;
      $diner->email      = $this->email;
      
      $dinerWriter->setDiner ($diner);
      $dinerWriter->persist  ();
      
      return $diner;
    }
  }

?>
