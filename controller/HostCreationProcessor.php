<?php

  /* Encapsulates the host creation process from a JSON input
   * object.
   * 
   * Date:   06.12.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("authorization/LoginManager.php");
  require_once ("authorization/LoginStatus.php");
  require_once ("authorization/PasswordEncryptionManager.php");
  require_once ("controller/HostLoader.php");
  require_once ("controller/HostWriter.php");
  require_once ("controller/RestaurantLoader.php");
  require_once ("model/Host.php");
  
  
  class HostCreationProcessor
  {
    private $request;
    private $hostLoader;
    private $hostWriter;
    private $restaurantLoader;
    
    
    public function __construct ()
    {
      $this->request          = null;
      $this->hostLoader       = new HostLoader       ();
      $this->hostWriter       = new HostWriter       ();
      $this->restaurantLoader = new RestaurantLoader ();
    }
    
    
    public function setRequest ($request)
    {
      $this->request = $request;
    }
    
    
    public function process ()
    {
      $host              = null;
      $plainPassword     = null;
      $encryptedPassword = null;
      $adminLoginStatus  = null;
      $isUsernameUnique  = false;
      $hostName          = null;
      $hostUsername      = null;
      
      $adminLoginStatus  = $this->checkAuthorization ();
      $host              = new Host ();
      $hostName          = $this->request->name;
      $hostUsername      = $this->request->username;
      $plainPassword     = $this->request->password;
      $encryptedPassword = PasswordEncryptionManager::encryptPassword ($plainPassword);
      $isUsernameUnique  = $this->checkIfUsernameIsUnique ($hostUsername);
      
      if (! $isUsernameUnique)
      {
        throw new Exception
        (
          sprintf ('A user with username "%s" already exists.',
                   $hostUsername)
        );
      }
      
      $this->setHostName   ($host, $hostName);
      $this->setRestaurant ($host, $adminLoginStatus);
      $host->email             = $hostUsername;   # TODO: Clarify, if E-mail is sufficient replacement for username.
      $host->encryptedPassword = $encryptedPassword;
      
      $this->hostWriter->setHost ($host);
      $this->hostWriter->persist ();
      
      if ($host->id == 0)
      {
        throw new Exception
        (
          "A problem occured: The host could not be persisted " ."
          in the database."
        );
      }
      
      return $host;
    }
    
    
    
    private function checkAuthorization ()
    {
      $isAuthorized = false;
      
      if (LoginManager::isLoggedIn ())
      {
        $loginStatus  = LoginManager::getStatus ();
        $isAuthorized = $loginStatus->isAdministrator ();
      }
      else
      {
        $isAuthorized = false;
      }
      
      if (! $isAuthorized)
      {
        throw new Exception
        (
          "Unauthorized access: Only an administrator " .
          "may register a host."
        );
      }
      
      return $loginStatus;
    }
    
    private function checkIfUsernameIsUnique ($hostUsername)
    {
      $existingHostWithThisName = null;
      
      $existingHostWithThisName = $this->hostLoader->getHostByEmail ($hostUsername);
      
      return ($existingHostWithThisName == null);
    }
    
    private function setRestaurant (Host $host, $adminLoginStatus)
    {
      $restaurant   = null;
      $restaurantID = null;
      
      if ($adminLoginStatus->hasAttribute ("restaurant_id"))
      {
        $restaurantID = $adminLoginStatus->getAttributeValue ("restaurant_id");
        $restaurant   = $this->restaurantLoader->getRestaurantByID ($restaurantID);
        
        if ($restaurant != null)
        {
          $host->restaurant = $restaurant;
        }
        else
        {
          throw new Exception
          (
            "No restaurant found for administrator. Cannot create host."
          );
        }
      }
      else
      {
        throw new Exception
        (
          "Administrator has no restaurant ID. Cannot create host."
        );
      }
    }
    
    private function setHostName (Host $host, $name)
    {
//      $nameTokens         = null;
//      $numberOfNameTokens = 0;
//      
//      if (($name === null) || (empty ($host)))
//      {
//        $host->firstName = "";
//        $host->lastName  = "";
//      }
//      else
//      {
//        $nameTokens         = explode (" ", $name);
//        $numberOfNameTokens = count ($nameTokens);
//        
//        if ($numberOfNameTokens >= 1)
//        {
//          $host->firstName = $nameTokens[0];
//          
//          if ($numberOfNameTokens >= 2)
//          {
//            $host->lastName = $nameTokens[1];
//          }
//          else
//          {
//            $host->lastName = "";
//          }
//        }
//        else
//        {
//          $host->firstName = "";
//        }
//      }
      
      $host->setFullName ($name);
    }
  }

?>
