<?php
  
  # TODO:
  #  (02) Clarify image uploading.
  
  
  /* Encapsulates the restaurant creation process from a JSON input
   * object.
   * 
   * Date:   20.11.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("controller/AdministratorLoader.php");
  require_once ("controller/AdministratorWriter.php");
  require_once ("controller/RestaurantImageWriter.php");
  require_once ("controller/RestaurantLoader.php");
  require_once ("controller/RestaurantTableWriter.php");
  require_once ("controller/RestaurantWriter.php");
  require_once ("controller/LocationLoader.php");
  require_once ("model/Administrator.php");
  require_once ("model/GeoCoordinates.php");
  require_once ("model/Location.php");
  require_once ("utils/ObjectInspector.php");
  
  
  class RestaurantCreationProcessor
  {
    private $request;         // The restaurant data as JSON objected.
    private $restaurantLoader;
    private $restaurantWriter;
    private $locationWriter;
    private $locationLoader;
    private $tableWriter;
    private $imageWriter;
    private $administratorLoader;
    private $administratorWriter;
    
    
    public function __construct ()
    {
      $this->request               = null;
      $this->restaurantLoader      = new RestaurantLoader      ();
      $this->restaurantWriter      = new RestaurantWriter      ();
      $this->locationWriter        = new LocationWriter        ();
      $this->locationLoader        = new LocationLoader        ();
      $this->tableWriter           = new RestaurantTableWriter ();
      $this->imageWriter           = new RestaurantImageWriter ();
      $this->administratorLoader   = new AdministratorLoader   ();
      $this->administratorWriter   = new AdministratorWriter   ();
    }
    
    
    public function setRequest ($request)
    {
      $this->request = $request;
    }
    
    
    // Creates a new restaurant, stores it in database, and returns it.
    public function process ()
    {
      if ($this->request == null)
      {
        throw new Exception ("JSON request object is NULL.");
      }
      
      $createdRestaurantID = 0;
      $request             = null;
      $restaurant          = null;
      $admin               = null;
      $location            = null;
      
      $request    = $this->request;
      $restaurant = new RestaurantModel ();
      $location   = new Location();
      $admin      = $this->createAdmin  ($request);
      
      $restaurant->name = $request->name;
      $restaurant->setStreetWithHouseNumber ($request->street);
      $restaurant->administrator = $admin;
      
      $location->city =$request->city;
      $location->state =$request->state;
      $location->zip =$request->zip;
      $restaurant->location = $location;
      
      $restaurant->city = $request->city;
      $restaurant->state = $request-> state;
      
      $this->processLocation ($restaurant);
      $this->restaurantWriter->setRestaurant ($restaurant);
      $createdRestaurantID = $this->restaurantWriter->persist ();
      
      if ($createdRestaurantID <= 0)
      {
        throw new Exception ("Could not create or persist restaurant.");
      }
      
      /*
      $this->processCategories ($restaurant, $request->categories);
      $this->processTables     ($restaurant, $request->tables);
      $this->processImages     ($restaurant, $request->images);
      */
      
      return $restaurant;
    }
    
    
    
    ////////////////////////////////////////////////////////////////////
    // -- Implementation of auxiliary methods.                     -- //
    ////////////////////////////////////////////////////////////////////
    
    private function createAdmin ($request)
    {
      $this->checkAdministratorEmail ($request->email);
      
      $admin = new Administrator ();
      
      $admin->firstName         = $request->first_name;
      $admin->lastName          = $request->last_name;
      $admin->email             = $request->email;
      $admin->phoneNumber       = $request->phone;
      $admin->encryptedPassword = PasswordEncryptionManager::encryptPassword ($request->password);
      
      $this->administratorWriter->setAdministrator ($admin);
      $this->administratorWriter->persist          ();
      
      return $admin;
    }
    
    private function checkAdministratorEmail ($email)
    {
      $administratorWithThisEmail = $this->administratorLoader
                                         ->getAdministratorByEmail ($email);
      
      if ($administratorWithThisEmail != null)
      {
        $exceptionText = sprintf
        (
          'There is already an administrator associated with the ' .
          'e-mail address "%s". The e-mail address is an ' .
          'administrator\'s unique username.',
          $email
        );
        throw new Exception ($exceptionText);
      }
    }
    
    
    private function processLocation (RestaurantModel $restaurant)
    {
      $hasLocation = ObjectInspector::hasProperty ($restaurant, "location");
      $hasStreet   = ObjectInspector::hasProperty ($this->request, "street");
      
      if ($hasLocation)
      {
        $location = $this->getLocation ($restaurant);
        
        $restaurant->location = $location;
      }
      
      if ($hasStreet)
      {
        $restaurant->setStreetWithHouseNumber ($this->request->street);
      }
      
      $restaurant->updateLatitudeAndLongitude ();
    }
    
    private function getLocation ($request)
    {
      $city     = $request->location->city;
      $state    = $request->location->state;
      $zip      = $request->location->zip;
      echo $city;
      echo $state;
      echo $zip;
      $location = $this->locationLoader->getLocationByCityStateAndZip
      (
        $city,
        $state,
        $zip
      );
      
      // No such location? => Create and store a new one.
      if ($location == null)
      {
        $location = new Location ();
        $location->city         = $city;
        $location->state        = $state;
        $location->zip          = $zip;
        $location->neighborhood = $city;
        
        $this->locationWriter->setLocation ($location);
        $this->locationWriter->persist     ();
      }
      
      return $location;
    }
  }

?>
