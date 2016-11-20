<?php

  /* Allows logging in through a cookie.
   *
   * Date:   02.12.2015
   * Author: Kaveh Yousefi
   */


  require_once ("authorization/LoginCredentials.php");
  require_once ("authorization/LoginOperator.php");
  require_once ("authorization/LoginStatus.php");
  require_once ("authorization/PasswordEncryptionManager.php");
  require_once ("controller/AdministratorLoader.php");
  require_once ("controller/HostLoader.php");
  require_once ("model/Administrator.php");
  require_once ("model/Host.php");


  class      CookieBasedLoginOperator
  implements LoginOperator
  {
    const COOKIE_NAME = "seatedlogin";
    const READABLE_COOKIE_NAME = "seateduser";

    private $passwordEncryption;


    public function __construct ()
    {
      $this->passwordEncryption = new MD5PasswordEncryption ();
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

      switch ($credentials->userType)
      {
        case UserType::USER_TYPE_ADMIN :
          $loginStatus = $this->createAdministratorLoginStatus ($credentials);
          break;
        case UserType::USER_TYPE_HOST :
          $loginStatus = $this->createHostLoginStatus ($credentials);
          break;
      }

      if ($loginStatus !== null)
      {
        setcookie
        (
          self::COOKIE_NAME,
          serialize ($loginStatus),
          $this->getExpirationTime (),
          "/"
        );
        setcookie
        (
          self::READABLE_COOKIE_NAME,
          json_encode($loginStatus),
          $this->getExpirationTime (),
          "/"
        );
      }
      else
      {
        throw new Exception
        (
          "LoginManager->login(): You must provide a valid user name " .
          "and password to log in."
        );
      }

      return $loginStatus;
    }

    public function logout ()
    {
      setcookie (self::COOKIE_NAME,
                 null, -1, "/");
      setcookie (self::READABLE_COOKIE_NAME,
                 null, -1, "/");
      unset ($_COOKIE[self::COOKIE_NAME]);
      return true;
    }


    public function getStatus ()
    {
      if ($this->isLoggedIn ())
      {
        return unserialize ($_COOKIE[self::COOKIE_NAME]);
      }
      else
      {
        return null;
      }
    }

    public function isLoggedIn ()
    {
      return (isset ($_COOKIE[self::COOKIE_NAME]));
    }


    public function __toString ()
    {
      return "Im am the CookieBasedLoginOperator.";
    }



    ////////////////////////////////////////////////////////////////////
    // -- Implementation of auxiliary methods.                     -- //
    ////////////////////////////////////////////////////////////////////

    private function getExpirationTime ()
    {
      return time () + $this->convertMinutesToSeconds (300);
    }

    private function convertMinutesToSeconds ($numberOfMinutes)
    {
      return ($numberOfMinutes * 60);
    }

    private function createAdministratorLoginStatus (LoginCredentials $credentials)
    {
      $administrator = $this->tryToLoadAdministratorByEmail ($credentials->userName);

      if ($administrator == null)
      {
        throw new Exception ("Unknown username for admin.");
      }

      if (! PasswordEncryptionManager::doEncryptedPasswordsMatch ($administrator->encryptedPassword, $credentials->password))
      {
        throw new Exception ("Invalid password for admin.");
      }

      $loginStatus = new LoginStatus ();
      $loginStatus->setUserName  ($administrator->getFullName ());
      $loginStatus->setUserID    ($administrator->id);
      $loginStatus->setUserType  ($credentials->getUserType ());
      $loginStatus->setAttribute ("first_name",    $administrator->firstName);
      $loginStatus->setAttribute ("last_name",     $administrator->lastName);
      $loginStatus->setAttribute ("email",         $administrator->email);
      $loginStatus->setAttribute ("phone",         $administrator->phoneNumber);
      $loginStatus->setAttribute ("restaurant_id", $this->getRestaurantIDForAdmin ($administrator->id));
      $loginStatus->setAttribute ("name",          $administrator->getFullName ());

      return $loginStatus;
    }

    private function createHostLoginStatus (LoginCredentials $credentials)
    {
      $host = $this->tryToLoadHostByEmail ($credentials->userName);

      if ($host == null)
      {
        throw new Exception ("Unknown username for host.");
      }

      if (! PasswordEncryptionManager::doEncryptedPasswordsMatch ($host->encryptedPassword, $credentials->password))
      {
        throw new Exception ("Invalid password for host.");
      }

      $loginStatus = new LoginStatus ();
      $loginStatus->setUserName  ($host->getFullName ());
      $loginStatus->setUserID    ($host->id);
      $loginStatus->setUserType  ($credentials->getUserType ());
      $loginStatus->setAttribute ("id",            $host->id);
      $loginStatus->setAttribute ("restaurant_id", $host->getRestaurantID ());
      $loginStatus->setAttribute ("name",          $host->getFullName());
      $loginStatus->setAttribute ("username",      $host->email);   // TODO: CLARIFY.

      return $loginStatus;
    }

    private function tryToLoadHostByEmail ($email)
    {
      $hostLoader = new HostLoader              ();
      $host       = $hostLoader->getHostByEmail ($email);

      return $host;
    }

    private function tryToLoadAdministratorByEmail ($email)
    {
      $adminLoader   = new AdministratorLoader               ();
      $administrator = $adminLoader->getAdministratorByEmail ($email);

      return $administrator;
    }

    private function getRestaurantIDForAdmin ($administratorID)
    {
      $restaurantLoader    = new RestaurantLoader ();
      $restaurantsForAdmin = $restaurantLoader->getRestaurantsByAdminID ($administratorID);

      if (count ($restaurantsForAdmin))
      {
        $firstRestaurant = $restaurantsForAdmin[0];
        return $firstRestaurant->id;
      }
      else
      {
        return 0;
      }
    }
  }
?>
