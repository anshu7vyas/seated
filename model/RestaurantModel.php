<?php

  /* Models a restaurant.
   *
   * Date:   01.11.2015
   * Author: Kaveh Yousefi
   */

  require_once ("controller/GeoCoordinatesConverter.php");
  require_once ("controller/GeoCoordinatesCreator.php");
  require_once ("model/Category.php");
  require_once ("model/RestaurantImage.php");
  require_once ("model/RestaurantOperationTime.php");
  
  
  class RestaurantModel
  {
    const MINUTES_PER_HOUR = 60;

    public $id;
    public $userName;
    public $password;
    public $name;
    public $countryId;
    public $city;
    public $state;
    public $zipCode;
    public $phoneNumber;
    public $email;
    public $opening;
    public $closing;
    public $inet_address;
    public $shortDescription;
    public $longDescription;
    public $images;
    public $categories;
    public $location;
    public $street;
    public $houseNumber;
    public $longitude;
    public $latitude;

    public $administrator;
    public $mealDurationTime;
    public $createdOn;
    public $lastModify;
    public $priceRange;
    public $operationTimes;  // Maps: weekdayName -> RestaurantOperationTime
    
    
    public function __construct ()
    {
      $this->id               = null;
      $this->userName         = null;
      $this->password         = null;
      $this->name             = null;
      $this->countryId        = -1;
      $this->city             = null;
      $this->zipCode          = null;
      $this->state            = null;
      $this->phoneNumber      = null;
      $this->email            = null;
      $this->opening          = null;
      $this->closing          = null;
      $this->inet_address     = null;
      $this->shortDescription = null;
      $this->longDescription  = null;
      $this->images           = null;
      $this->categories       = array ();
      $this->location         = null;
      $this->street           = null;
      $this->houseNumber      = null;
      $this->longitude        = 0.0;
      $this->latitude         = 0.0;
      
      $this->administrator    = null;
      $this->mealDurationTime = null;
      $this->createdOn        = null;
      $this->lastModify       = null;
      $this->priceRange       = 1;
      $this->operationTimes   = $this->createDefaultOperationTimes ();
    }


    public function getId ()
    {
      return $this->id;
    }

    public function setId ($id)
    {
      $this->id = $id;
    }

    public function getName ()
    {
      return $this->name;
    }

    public function setName ($name)
    {
      $this->name = $name;
    }

    public function getImages ()
    {
      return $this->images;
    }

    public function addImage (RestaurantImage $image)
    {
      $this->images[] = $image;
    }

    public function getShortDescription ()
    {
      return $this->shortDescription;
    }

    public function setShortDescription ($shortDescription)
    {
      $this->shortDescription = $shortDescription;
    }

    public function addCategory (Category $category)
    {
      $this->categories[] = $category;
    }

    public function getCategoryIDs ()
    {
      $categoryIDs = array ();

      foreach ($this->categories as $restaurantCategory)
      {
        $categoryIDs[] = $restaurantCategory->id;
      }

      return $categoryIDs;
    }

    public function getCategories ()
    {
      $categories = array ();

      foreach ($this->categories as $restaurantCategory)
      {
        $categories[] = $restaurantCategory;
      }

      return $categories;
    }

    public function getMealDurationTimeInMinutes ()
    {
      return ($this->mealDurationTime * self::MINUTES_PER_HOUR);
    }

    public function getStreetWithHouseNumber ()
    {
      return sprintf ("%s %s", $this->street, $this->houseNumber);
    }
    
    // -> "http://stackoverflow.com/questions/1530883/regex-to-split-a-string-only-by-the-last-whitespace-character"
    public function setStreetWithHouseNumber ($streetWithHouseNumber)
    {
      $streetTokens = preg_split ("/\s+(?=\S*+$)/", $streetWithHouseNumber);
      
      if (count ($streetTokens) >= 1)
      {
        $this->street = $streetTokens[0];
      }
      if (count ($streetTokens) >= 2)
      {
        $this->houseNumber = $streetTokens[1];
      }
    }
    
    
    public function getOperationTimes ()
    {
      return $this->operationTimes;
    }
    
    public function getOperationTime ($weekdayName)
    {
      return $this->operationTimes[$weekdayName];
    }
    
    public function setOperationTime ($weekdayName, $operationTime)
    {
      $this->operationTimes[$weekdayName] = $operationTime;
    }
    
    public function hasOperationTime ($weekdayName)
    {
      return isset ($this->operationTimes[$weekdayName]);
    }
    
    public function getOpeningTime ($weekdayName)
    {
      if ($this->hasOperationTime ($weekdayName))
      {
        return $this->getOperationTime ($weekdayName)->openingTime;
      }
      else
      {
        return null;
      }
    }
    
    public function getClosingTime ($weekdayName)
    {
      if ($this->hasOperationTime ($weekdayName))
      {
        return $this->getOperationTime ($weekdayName)->closingTime;
      }
      else
      {
        return null;
      }
    }
    
    public function isOpenOn ($weekdayName)
    {
      if ($this->hasOperationTime ($weekdayName))
      {
        return $this->getOperationTime ($weekdayName)->isOpen;
      }
      else
      {
        return false;
      }
    }
    
    
    public function updateLatitudeAndLongitude ()
    {
      $geoCoordinates = GeoCoordinatesCreator::getGeoCoordinatesFromStreetAndLocation
      (
        $this->getStreetWithHouseNumber (),
        $this->location
      );
      
      if ($geoCoordinates != null)
      {
        $this->latitude  = $geoCoordinates->getLatitude  ();
        $this->longitude = $geoCoordinates->getLongitude ();
      }
      else
      {
        $this->latitude  = 0.0;
        $this->longitude = 0.0;
      }
    }


    public function __toString ()
    {
      $asString = null;

      $asString = sprintf
      (
        "Restaurant(id=%s, name=%s, mealDurationMinutes=%s)",
        $this->id,
        $this->name,
        $this->getMealDurationTimeInMinutes ()
      );

      return $asString;
    }
    
    
    
    ////////////////////////////////////////////////////////////////////
    // -- Implementation of auxiliary methods.                     -- //
    ////////////////////////////////////////////////////////////////////
    
    private function createDefaultOperationTimes ()
    {
      $operationTimes = array ();
      $weekdayNames   = array
      (
        "monday",
        "tuesday",
        "wednesday",
        "thursday",
        "friday",
        "saturday",
        "sunday"
      );
      
      foreach ($weekdayNames as $weekdayName)
      {
        $operationTime = new RestaurantOperationTime ();
        $operationTime->weekdayName = $weekdayName;
        $operationTime->openingTime = "00:00:00";
        $operationTime->closingTime = "00:00:00";
        $operationTime->restaurant  = $this;
        
        $operationTimes[$weekdayName] = $operationTime;
      }
      
      return $operationTimes;
    }
  }

?>
