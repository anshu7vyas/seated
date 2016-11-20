<?php
  
  ## TODO:
  ##  (01) Clarify if PASSWORD necessary.
  ##  (02) Clarify if EMAIL    suffices as "username".
  
  /* Encapsulates the data necessary for a registration.
   * 
   * Date:   25.11.2015
   * Author: Kaveh Yousefi
   */
  
  abstract class RegistrationCredentials
  {
    public $firstName;
    public $lastName;
    public $email;
    public $phone;
    public $mobile;
    public $position;
    public $userType;
    
    
    public function __construct ()
    {
      $this->firstName = null;
      $this->lastName  = null;
      $this->email     = null;
      $this->phone     = null;
      $this->mobile    = null;
      $this->position  = null;
      $this->userType  = null;
    }
    
    
    // Check if the data is valid.
    //   On success: return TRUE.
    //   On error:   throw  Exception.
    abstract public function checkForValidity ();
    
    // Try to store the user in the database.
    //   On success: return persisted object (Host/Admin).
    abstract public function persist ();
  }

?>
