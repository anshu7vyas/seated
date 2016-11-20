<?php

  /* The LoginOperator interface provides an abstraction of the
   * temporary user data storage (cookie versus session).
   * 
   * Date:   02.12.2015
   * Author: Kaveh Yousefi
   */
  
  
  interface LoginOperator
  {
    // Checks login credentials.
    //   On success: return LoginStatus.
    //   On failure: throw  Exception.
    public function login (LoginCredentials $credentials);
    
    public function logout ();
    
    public function getStatus ();
    
    public function isLoggedIn ();
  }

?>
