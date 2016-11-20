<?php

  /* Encapsulates the data necessary during a login and/or session.
   * 
   * Date:   25.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  require_once ("authorization/UserType.php");
  
  
  class LoginStatus
  {
    const USER_ID_NONE = 0;
    
    public $userType;
    public $attributes;
    public $userName;
    public $userID;     // Depends on user type (can be hostID, adminID, ...).
    
    
    public function __construct ()
    {
      $this->userType   = UserType::USER_TYPE_SIMPLE;
      $this->userName   = null;
      $this->attributes = array ();
      $this->userID     = self::USER_ID_NONE;
    }
    
    
    public function getUserType ()
    {
      return $this->userType;
    }
    
    public function setUserType ($userType)
    {
      $this->userType = $userType;
    }
    
    public function isOfThisUserType ($userTypeToCompareTo)
    {
      return ($this->userType == $userTypeToCompareTo);
    }
    
    public function isAdministrator ()
    {
      return $this->isOfThisUserType (UserType::USER_TYPE_ADMIN);
    }
    
    public function isHost ()
    {
      return $this->isOfThisUserType (UserType::USER_TYPE_HOST);
    }
    
    public function isDiner ()
    {
      return $this->isOfThisUserType (UserType::USER_TYPE_DINER);
    }
    
    public function isSimpleUser ()
    {
      return $this->isOfThisUserType (UserType::USER_TYPE_SIMPLE);
    }
    
    public function setAttribute ($attributeName, $attributeValue)
    {
      $this->attributes[$attributeName] = $attributeValue;
    }
    
    public function getAttributeValue ($attributeName)
    {
      return $this->attributes[$attributeName];
    }
    
    public function hasAttribute ($attributeName)
    {
      return isset ($this->attributes[$attributeName]);
    }
    
    public function hasAttributeWithValue ($attributeName, $attributeValue)
    {
      if ($this->hasAttribute ($attributeName))
      {
        return ($this->attributes[$attributeName] == $attributeValue);
      }
      else
      {
        return false;
      }
    }
    
    public function getUserTypeName ()
    {
      $userTypeName = UserType::getUserTypeNameFor ($this->userType);
      
      if ($userTypeName != null)
      {
        return $userTypeName;
      }
      else
      {
        return "UNKNOWN";
      }
    }
    
    
    public function getUserName ()
    {
      return $this->userName;
    }
    
    public function setUserName ($userName)
    {
      $this->userName = $userName;
    }
    
    public function getUserID ()
    {
      return $this->userID;
    }
    
    public function setUserID ($userID)
    {
      $this->userID = $userID;
    }
    
    public function hasUserID ()
    {
      return ($this->userID !== self::USER_ID_NONE);
    }
    
    
    public function __toString ()
    {
      return sprintf ("LoginStatus(userType=%s, userID=%s)",
                      $this->getUserTypeName (),
                      $this->getUserID       ());
    }
  }
  
?>
