<?php

  /* Encapsulates the restaurant update process from a JSON input
   * object.
   * 
   * Date:   08.12.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("controller/AdministratorLoader.php");
  require_once ("controller/CategoryLoader.php");
  require_once ("controller/LocationLoader.php");
  require_once ("controller/LocationWriter.php");
  require_once ("controller/RestaurantCategorizationWriter.php");
  require_once ("controller/RestaurantImageWriter.php");
  require_once ("controller/RestaurantLoader.php");
  require_once ("controller/RestaurantTableWriter.php");
  require_once ("controller/RestaurantWriter.php");
  require_once ("model/Category.php");
  require_once ("model/Location.php");
  require_once ("model/RestaurantModel.php");
  require_once ("model/RestaurantTable.php");
  require_once ("utils/ObjectInspector.php");
  
  
  class RestaurantUpdateProcessor
  {
    private $request;
    private $restaurantLoader;
    private $restaurantWriter;
    private $administratorLoader;
    private $restaurantTableWriter;
    private $locationLoader;
    private $locationWriter;
    private $categoryLoader;
    private $categorizationWriter;
    private $imageProcessor;
    private $imageWriter;
    
    
    public function __construct ()
    {
      $this->request               = null;
      $this->restaurantLoader      = new RestaurantLoader      ();
      $this->restaurantWriter      = new RestaurantWriter      ();
      $this->administratorLoader   = new AdministratorLoader   ();
      $this->restaurantTableWriter = new RestaurantTableWriter ();
      $this->locationLoader        = new LocationLoader        ();
      $this->locationWriter        = new LocationWriter        ();
      $this->categoryLoader        = new CategoryLoader        ();
      $this->categorizationWriter  = new RestaurantCategorizationWriter ();
      $this->imageProcessor        = new RestaurantImageProcessor ();
      $this->imageWriter           = new RestaurantImageWriter    ();
    }
    
    
    public function setRequest ($request)
    {
      $this->request = $request;
    }
    
    
    public function process ()
    {
      if ($this->request === null)
      {
        throw new Exception ("JSON request object is NULL.");
      }
      if (! LoginManager::isLoggedInAsUserOfThisType (UserType::USER_TYPE_ADMIN))
      {
        throw new Exception ("Only administrators may update a restaurant.");
      }
      
      $restaurant    = null;
      $restaurantID  = null;
      $administrator = null;
      
      $restaurantID  = LoginManager::getStatus ()->getAttributeValue ("restaurant_id");
      $adminID       = LoginManager::getStatus ()->getUserID         ();
      $administrator = $this->administratorLoader->getAdministratorByID ($adminID);
      
      if ($restaurantID == null)
      {
        throw new Exception ("No restaurant ID found.");
      }
      
      $restaurant = $this->restaurantLoader->getRestaurantByID ($restaurantID);
      
      if ($restaurant == null)
      {
        throw new Exception ("Found no matching restaurant.");
      }
      
      $this->setPropertyIfItExistsInRequest ($restaurant, "name", "name");
      $this->setPropertyIfItExistsInRequest ($restaurant, "shortDescription", "short_description");
      $this->setPropertyIfItExistsInRequest ($restaurant, "longDescription",  "long_description");
      $this->setPropertyIfItExistsInRequest ($restaurant, "priceRange", "price_range");
      
      # TABLES
      $this->processTables ($restaurant);
      
      $this->setPropertyIfItExistsInRequest ($restaurant, "email", "restaurant_email");
      $this->setPropertyIfItExistsInRequest ($restaurant, "phoneNumber", "restaurant_phone");
      $this->setPropertyIfItExistsInRequest ($restaurant, "inet_address", "url");
      
      # LOCATION
      $this->processLocation ($restaurant);
      
      $this->setPropertyIfItExistsInRequest ($restaurant, "mealDurationTime", "meal_duration_time");
      
      # HOURS
      $this->processHours ($restaurant);
      
      # IMAGES
      $this->processCoverImage ($restaurant);
      $this->processImages     ($restaurant);
      
      # CATEGORIES (CUISINES)
      $this->processCategories ($restaurant);
      
      $this->setPropertyIfItExistsInRequest ($administrator, "email", "email");
      $this->setPropertyIfItExistsInRequest ($administrator, "phoneNumber", "phone");
      
      $restaurant->lastModify = date ("Y-m-d H:i:s");
      
      $this->restaurantWriter->setRestaurant ($restaurant);
      $this->restaurantWriter->update        ();
      
      return $restaurant;
    }
    
    
    
    ////////////////////////////////////////////////////////////////////
    // -- Implementation of auxiliary methods.                     -- //
    ////////////////////////////////////////////////////////////////////
    
    // Must be persisted, AFTER the restaurant is written.
    // Reason: restaurant_id must be known.
    private function processTables (RestaurantModel $restaurant)
    {
      $hasTables = ObjectInspector::hasProperty($this->request, "tables");
      
      if ($hasTables)
      {
        $tableDataSets = $this->request->tables;
        
        $this->restaurantWriter->setRestaurant   ($restaurant);
        $this->restaurantWriter->deleteAllTables ();
        
        foreach ($tableDataSets as $tableData)
        {
          $restaurantTable                = new RestaurantTable ();
          $restaurantTable->restaurant    = $restaurant;
          $restaurantTable->numberOfSeats = $tableData->seats;
          $restaurantTable->description   = $tableData->name;
          
          $this->restaurantTableWriter->setRestaurantTable ($restaurantTable);
          $this->restaurantTableWriter->persist            ();
        }
      }
    }
    
    private function processLocation (RestaurantModel $restaurant)
    {
//      $hasLocation = ObjectInspector::hasProperty ($this->request, "location_id");
      $hasLocation = ObjectInspector::hasProperty ($this->request, "location");
      $hasStreet   = ObjectInspector::hasProperty ($this->request, "street");
      
      if ($hasLocation)
      {
        $location = $this->getLocation ($this->request);
        
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
      
      /*
      if (ObjectInspector::hasProperty ($request, "location_id"))
      {
        $locationID = $request->location_id;
        return $this->locationLoader->getLocationByID ($locationID);
      }
      else
      {
        return null;
      }
      */
    }
    
    private function processHours (RestaurantModel $restaurant)
    {
      $hasHours = ObjectInspector::hasProperty ($this->request, "hours");
      
      if ($hasHours)
      {
//        $hoursPerDays   = $this->request->hours;
//        $hoursForMonday = $hoursPerDays->monday;
//        
//        $restaurant->opening = $hoursForMonday->open;
//        $restaurant->closing = $hoursForMonday->close;
        $hoursPerDays = $this->request->hours;
        
        foreach ($hoursPerDays as $weekdayName => $timeData)
        {
          $operationTime = $restaurant->getOperationTime ($weekdayName);
          $operationTime->openingTime = $timeData->open;
          $operationTime->closingTime = $timeData->close;
        }
      }
    }
    
    private function processCoverImage ($restaurant)
    {
      $hasCoverImage = ObjectInspector::hasProperty ($this->request, "cover_image");
      
      if ($hasCoverImage)
      {
        $this->restaurantWriter->setRestaurant        ($restaurant);
        $this->restaurantWriter->deleteAllCoverImages ();
        
        $coverImageData  = $this->request->cover_image;
        $this->imageProcessor->createRestaurantImage ($coverImageData);
//        $restaurantImage = $this->imageProcessor->createRestaurantImage ($coverImageData);
//        
//        $this->imageWriter->setRestaurantImage ($restaurantImage);
//        $this->imageWriter->persist            ();
      }
    }
    
    private function processImages (RestaurantModel $restaurant)
    {
      $hasImages = ObjectInspector::hasProperty ($this->request, "images");
      
      if ($hasImages)
      {
        $imageDataSet = $this->request->images;
        
        $this->restaurantWriter->setRestaurant           ($restaurant);
        $this->restaurantWriter->deleteAllNonCoverImages ();
        
        foreach ($imageDataSet as $imageData)
        {
//          $restaurantImage = $this->imageProcessor->createRestaurantImage ($imageData);
//          $this->imageWriter->setRestaurantImage ($restaurantImage);
//          $this->imageWriter->persist            ();
          $this->imageProcessor->createRestaurantImage ($imageData);
        }
      }
    }
    
    // Must be persisted, AFTER the restaurant is written.
    // Reason: restaurant_id must be known.
//    private function processImages
//    (
//      RestaurantModel $restaurant,
//      $imageDataSet
//    )
//    {
//      foreach ($imageDataSet as $imageData)
//      {
//        $image               = new RestaurantImage ();
//        $image->restaurant   = $restaurant;
//        $image->originalName = $imageData->org_name;
//        $image->name         = $imageData->name;
//        $image->pathToFile   = $imageData->path_to_file;
//        $image->description  = $imageData->description;
//        $image->isCover      = $imageData->is_cover;
//        
//        $this->imageWriter->setRestaurantImage ($image);
//        $this->imageWriter->persist            ();
//      }
//    }
    
    // -> "http://stackoverflow.com/questions/4997252/get-post-from-multiple-checkboxes"
    private function processCategories
    (
      RestaurantModel &$restaurant
    )
    {
      if (! ObjectInspector::hasProperty ($this->request, "cuisine"))
      {
        return;
      }
      
      $categoryIDs = $this->request->cuisine;
      
      $this->categorizationWriter->setRestaurant         ($restaurant);
      $this->categorizationWriter->deleteCategorizations ();
      
      if (count ($categoryIDs) > 0)
      {
        $categoryObjects = $this->getCategoriesByIDs ($categoryIDs);
        
        foreach ($categoryObjects as $categoryObject)
        {
          $restaurant->addCategory ($categoryObject);
        }
        
//        $this->categorizationWriter->setRestaurant ($restaurant);
        $this->categorizationWriter->persist       ();
      }
    }
    
    // Convert an array of category IDs to an array of Category instances.
    private function getCategoriesByIDs ($categoryIDs)
    {
      $categoryObjects = array ();
      
      foreach ($categoryIDs as $categoryID)
      {
        $categoryObject    = $this->categoryLoader->getCategoryByID ($categoryID);
        $categoryObjects[] = $categoryObject;
      }
      
      return $categoryObjects;
    }
    
    private function setPropertyIfItExistsInRequest
    (
      $objectToModify,
      $propertyOfObjectToModify,
      $requestPropertyToQuery
    )
    {
      ObjectInspector::setPropertyIfQueriedObjectContainsIt
      (
        $objectToModify,
        $propertyOfObjectToModify,
        $this->request,
        $requestPropertyToQuery
      );
    }
  }
  
?>
