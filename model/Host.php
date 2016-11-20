<?php


class Host
{
  const NO_RESTAURANT = 0;
  
  public $id;
  public $firstName;
  public $lastName;
  public $email;
  public $restaurant;
  public $position;
  public $encryptedPassword;
  
  
  public function __construct ()
  {
    $this->id                = null;
    $this->firstName         = null;
    $this->lastName          = null;
    $this->email             = null;
    $this->restaurant        = null;
    $this->position          = null;
    $this->encryptedPassword = null;
  }
  
  
  public function getFullName ()
  {
    return sprintf ("%s %s", $this->firstName, $this->lastName);
  }
  
  public function setFullName ($name)
  {
    $nameTokens         = null;
    $numberOfNameTokens = 0;
    
    if ($name === null)
    {
      $host->firstName = "";
      $host->lastName  = "";
    }
    else
    {
      $nameTokens         = explode (" ", $name);
      $numberOfNameTokens = count   ($nameTokens);
      
      if ($numberOfNameTokens >= 1)
      {
        $this->firstName = $nameTokens[0];

        if ($numberOfNameTokens >= 2)
        {
          $this->lastName = $nameTokens[1];
        }
        else
        {
          $this->lastName = "";
        }
      }
      else
      {
        $this->firstName = "";
      }
    }
  }
  
  public function getRestaurantID ()
  {
    if ($this->restaurant != null)
    {
      return $this->restaurant->id;
    }
    else
    {
      return self::NO_RESTAURANT;
    }
  }
  
  
  public function __toString ()
  {
    return sprintf ("Host(id=%d, firstName=%s, lastName=%s, restaurantID=%d)",
                    $this->id,
                    $this->firstName,
                    $this->lastName,
                    $this->getRestaurantID ());
  }
}
