<?php

  /* Encapsulates the data necessary for a registration.
   * 
   * Date:   25.11.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("authorization/RegistrationCredentials.php");
  
  
  class RegistrationManager
  {
    private function __construct ()
    {
    }
    
    
    public static function register (RegistrationCredentials $credentials)
    {
      if ($credentials === null)
      {
        throw new Exception ("No credentials given.");
      }
      
      $isRegistrationSuccessful = false;
      
      $isRegistrationSuccessful = $credentials->checkForValidity ();
      $credentials->persist ();
      
      return $isRegistrationSuccessful;
    }
  }

?>
