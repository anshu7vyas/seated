<?php
  
  /* Models an administrator.
   * 
   * Date:   21.11.2015
   * Author: Kaveh Yousefi
   */
  
  class Administrator
  {
    public $id;
    public $firstName;
    public $lastName;
    public $email;
    public $phoneNumber;
    public $mobileNumber;
    public $position;
    public $password;
    public $encryptedPassword;
    
    
    public function __construct ()
    {
      $this->id                = null;
      $this->firstName         = null;
      $this->lastName          = null;
      $this->email             = null;
      $this->phoneNumber       = null;
      $this->mobileNumber      = null;
      $this->position          = null;
      $this->encryptedPassword = null;
    }
    
    
    public function getFullName ()
    {
      return sprintf ("%s %s", $this->firstName, $this->lastName);
    }
  }

?>
