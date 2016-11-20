<?php

  /* Allows logging in (starts a session).
   * 
   * Date:   25.11.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("authorization/CookieBasedLoginOperator.php");
  
  
  class LoginManager
  {
    private static $loginOperator = null;
    
    
    // Checks login credentials.
    //   On success: return LoginStatus.
    //   On failure: throw  Exception.
    public static function login (LoginCredentials $credentials)
    {
      self::initializeLoginOperatorIfNecessary ();
      return self::$loginOperator->login ($credentials);
    }
    
    public static function logout ()
    {
      self::initializeLoginOperatorIfNecessary ();
      return self::$loginOperator->logout ();
    }
    
    
    public static function getStatus ()
    {
      self::initializeLoginOperatorIfNecessary ();
      return self::$loginOperator->getStatus ();
    }
    
    public static function isLoggedIn ()
    {
      self::initializeLoginOperatorIfNecessary ();
      return self::$loginOperator->isLoggedIn ();
    }
    
    
    public static function isLoggedInAsUserOfThisType ($userType)
    {
      if (self::isLoggedIn ())
      {
        $loginStatus = self::getStatus ();
        return $loginStatus->isOfThisUserType ($userType);
      }
      else
      {
        return false;
      }
    }
    
    
    
    ////////////////////////////////////////////////////////////////////
    // -- Implementation of auxiliary methods.                     -- //
    ////////////////////////////////////////////////////////////////////
    
    private static function initializeLoginOperatorIfNecessary ()
    {
      if (self::$loginOperator == null)
      {
        self::$loginOperator = new CookieBasedLoginOperator ();
      }
    }
  }
  
?>
