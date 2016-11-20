<?php

  /* Allows logging in through a session.
   * 
   * Date:   02.12.2015
   * Author: Kaveh Yousefi
   */
  
  
  /* Load ALL classes to be stored in a session here!
   * => Otherwise: incomplete classes occur in other pages.
   */
  require_once ("authorization/LoginCredentials.php");
  require_once ('authorization/LoginOperator.php');
  require_once ("authorization/LoginStatus.php");
  require_once ("controller/HostLoader.php");
  require_once ("model/Host.php");
  
  session_start ();
  
  
  class      SessionBasedLoginOperator
  implements LoginOperator
  {
    const STATUS_SESSION_KEY = "status";
    
    
    public function __construct ()
    {
    }
    
    
    // Checks login credentials.
    //   On success: return LoginStatus.
    //   On failure: throw  Exception.
    public function login (LoginCredentials $credentials)
    {
      if ($credentials == null)
      {
        throw new Exception ("Login credentials are null.");
      }
      
      $loginStatus = null;
      
      //if ($credentials->userName === "BingWang")
      $host = $this->tryToLoadHostByEmail ($credentials->userName);
      if ($host !== null)
      {
        $loginStatus = new LoginStatus ();
//        $loginStatus->setUserName ($credentials->getUserName ());
//        $loginStatus->setUserType ($credentials->getUserType ());
        $loginStatus->setUserName ($host->getFullName ());
        $loginStatus->setUserID   ($host->id);
        $loginStatus->setUserType ($credentials->getUserType ());
        
        $_SESSION[self::STATUS_SESSION_KEY] = $loginStatus;
      }
      else
      {
//        throw new Exception ("LoginManager->login(): You must be 'BingWang' to be logged in.");
        throw new Exception ("LoginManager->login(): You must provide a valid user name to log in.");
      }
      
      return $loginStatus;
    }
    
    public function logout ()
    {
      unset ($_SESSION[self::STATUS_SESSION_KEY]);
    }
    
    
    public function getStatus ()
    {
      if ($this->isLoggedIn ())
      {
        return $_SESSION[self::STATUS_SESSION_KEY];
      }
      else
      {
        return null;
      }
    }
    
    public function isLoggedIn ()
    {
      return (isset ($_SESSION[self::STATUS_SESSION_KEY]));
    }
    
    
    public function __toString ()
    {
      return "Im am the SessionBasedLoginOperator.";
    }
    
    
    
    ////////////////////////////////////////////////////////////////////
    // -- Implementation of auxiliary methods.                     -- //
    ////////////////////////////////////////////////////////////////////
    
    private function tryToLoadHostByEmail ($email)
    {
      $hostLoader = new HostLoader              ();
      $host       = $hostLoader->getHostByEmail ($email);
      
      return $host;
    }
  }
  
?>
