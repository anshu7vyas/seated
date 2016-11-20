<?php


class Diner
{
  public $id;
  public $firstName;
  public $lastName;
  public $phoneNumber;
  public $mobileNumber;
  public $email;
  public $memberSince;
  public $encryptedPassword;
  
  
  public function __construct ()
  {
    $this->id                = 0;
    $this->firstName         = null;
    $this->lastName          = null;
    $this->phoneNumber       = null;
    $this->mobileNumber      = null;
    $this->email             = null;
    $this->memberSince       = 0;
    $this->encryptedPassword = null;
  }
}

?>
