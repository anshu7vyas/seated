<?php

  /* Encapsulates the data necessary for a login.
   * 
   * Date:   25.11.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("authorization/UserType.php");
  
  class LoginCredentials
  {
    public $userName;
    public $password;
    public $userType;
    
    
    public function __construct ()
    {
      $this->userName = null;
      $this->password = null;
      $this->userType = UserType::USER_TYPE_SIMPLE;
    }
    
    
    public function getUserName ()
    {
      return $this->userName;
    }
    
    public function setUserName ($userName)
    {
      $this->userName = $userName;
    }
    
    public function getPassword ()
    {
      return $this->password;
    }
    
    public function setPassword ($password)
    {
      $this->password = $password;
    }
    
    public function getUserType ()
    {
      return $this->userType;
    }
    
    public function setUserType ($userType)
    {
      $this->userType = $userType;
    }
  }
  
?>
