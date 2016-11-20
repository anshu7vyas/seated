<?php

  /* Defines the valid user types to be registered/logged in.
   * 
   * Date:   25.11.2015
   * Author: Kaveh Yousefi
   */
  
  class UserType
  {
    const USER_TYPE_SIMPLE = 0;   // No role. => Not logged in.
    const USER_TYPE_ADMIN  = 1;
    const USER_TYPE_HOST   = 2;
    const USER_TYPE_DINER  = 3;
    
    // Provide a printable name for each user type constant.
    public static $userTypeNames = array
    (
      UserType::USER_TYPE_SIMPLE => "simple user",
      UserType::USER_TYPE_ADMIN  => "administrator",
      UserType::USER_TYPE_HOST   => "host",
      UserType::USER_TYPE_DINER  => "diner"
    );
    
    
    private function __construct ()
    {
    }
    
    
    public static function getUserTypeNameFor ($userType)
    {
      if (isset (self::$userTypeNames[$userType]))
      {
        return self::$userTypeNames[$userType];
      }
      else
      {
        return null;
      }
    }
  }

?>
