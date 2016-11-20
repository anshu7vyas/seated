<?php

  /* Creates a JSON string representation of a RestaurantModel.
   *
   * Date:   28.11.2015
   * Author: Kaveh Yousefi
   */


  require_once ('authorization/LoginManager.php');
  require_once ('authorization/LoginStatus.php');
  require_once ('controller/RestaurantImageLoader.php');
  require_once ('controller/RestaurantTableLoader.php');
  require_once ('model/RestaurantModel.php');


  class RestaurantJSONfier
  {
    private $restaurantTableLoader;
    private $restaurantImageLoader;


    public function __construct ()
    {
      $this->restaurantTableLoader = new RestaurantTableLoader ();
      $this->restaurantImageLoader = new RestaurantImageLoader ();
    }


    public function createJSONString ($restaurant)
    {
      $jsonResultObject = null;
      $phpResultObject  = null;

      $phpResultObject  = $this->createPHPObjectToJSONfy ($restaurant);
      $jsonResultObject = json_encode ($phpResultObject);

      return $jsonResultObject;
    }

    public function createPHPObjectToJSONfy ($restaurant)
    {
      $phpResultObject  = null;
      $tableDataAsArray = null;
      $hasLocation      = false;
      $restaurantImages = null;

      if ($restaurant === null)
      {
        throw new Exception ("RestaurantJSONfier::createPHPObjectToJSONfy(): Restaurant is null.");
      }

      $tableDataAsArray = $this->getTableDataAsArray ($restaurant);
      $hasLocation      = ($restaurant->location != null);
      $restaurantImages = $this->loadRestaurantImages ($restaurant);

      $phpResultObject = array
      (
        'id'                 => $restaurant->id,
        'name'               => $restaurant->name,
        'short_description'  => $restaurant->shortDescription,
        'long_description'   => $restaurant->longDescription,
        'location'           => array
        (
          'city'         => $hasLocation ? $restaurant->location->city  : "",
          'state'        => $hasLocation ? $restaurant->location->state : "",
          'zip'          => $hasLocation ? $restaurant->location->zip   : "",
          'street'       => $restaurant->getStreetWithHouseNumber (),
          'longitude'    => $restaurant->longitude,
          'latitude'     => $restaurant->latitude
        ),
        'tables'             => $tableDataAsArray,
        'meal_duration_time' => $restaurant->mealDurationTime,
        'price_range'        => $restaurant->priceRange,
        'categories'         => $restaurant->getCategories(),
        'hours'              => $this->getOpeningAndClosingTimesPerWeekdays ($restaurant),
        'cover_img'          => $this->getCoverImageJSONString ($restaurantImages),
        'images'             => $this->getImagesJSONString     ($restaurantImages),
        'email'              => $restaurant->email,
        'phone'              => $restaurant->phoneNumber,
        'url'                => $restaurant->inet_address,
        'live'               => false
      );

      if ($this->isDiner ())
      {
        unset ($phpResultObject['amount_tables']);
        unset ($phpResultObject['amount_seats']);
        unset ($phpResultObject['meal_duration_time']);
      }

      return $phpResultObject;
    }



    ////////////////////////////////////////////////////////////////////
    // -- Implementation of auxiliary methods.                     -- //
    ////////////////////////////////////////////////////////////////////

    private function isDiner ()
    {
      if (LoginManager::isLoggedIn ())
      {
        $loginStatus = LoginManager::getStatus ();

        return ($loginStatus->isDiner () || $loginStatus->isSimpleUser ());
      }
      else
      {
        return true;
      }
    }

    private function getTableDataAsArray (RestaurantModel $restaurant)
    {
      $tableData = array ();
      $tables    = $this->getTablesForRestaurant ($restaurant);
      $tableArray = array();
      foreach($tables as $table){
        $tableArray[] = array($table->description, $table->numberOfSeats);
      }

      return $tableArray;
    }

    private function getTablesForRestaurant (RestaurantModel $restaurant)
    {
      return $this->restaurantTableLoader->getTablesByRestaurantID ($restaurant->id);
    }

    private function getAmountTables ($tables)
    {
      return count ($tables);
    }

    private function getAmountSeats ($tables)
    {
      $amountSeats = 0;

      foreach ($tables as $table)
      {
        $amountSeats = $amountSeats + $table->numberOfSeats;
      }

      return $amountSeats;
    }

//    private function getOpeningAndClosingTimesPerWeekdays (RestaurantModel $restaurant)
//    {
//      $openingAndClosingTimes = array ();
//      $weekdays               = array
//      (
//        'monday',
//        'tuesday',
//        'wednesday',
//        'thursday',
//        'friday',
//        'saturday',
//        'sunday'
//      );
//
//      foreach ($weekdays as $weekday)
//      {
//        $openingAndClosingTimes[$weekday] = array
//        (
//          'opening' => $restaurant->opening,
//          'closing' => $restaurant->closing
//        );
//      }
//
//      return $openingAndClosingTimes;
//    }

    private function getOpeningAndClosingTimesPerWeekdays (RestaurantModel $restaurant)
    {
      $openingAndClosingTimes = array ();
      $weekdays               = array
      (
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday'
      );

      foreach ($weekdays as $weekday)
      {
        $openingAndClosingTimes[$weekday] = array
        (
          'opening' => $restaurant->getOperationTime ($weekday)->openingTime,
          'closing' => $restaurant->getOperationTime ($weekday)->closingTime
        );
      }

      return $openingAndClosingTimes;
    }

    private function getCoverImageJSONString ($restaurantImages)
    {
      foreach ($restaurantImages as $restaurantImage)
      {
        if ($restaurantImage->isCover)
        {
          $coverImage = $restaurantImage;
          $jsonString = $coverImage->pathToFile;

          return $jsonString;
        }
      }

      return "";
    }

    private function getImagesJSONString ($restaurantImages)
    {
      $jsonReadyPHPArray = array ();

      foreach ($restaurantImages as $restaurantImage)
      {
        $jsonReadyPHPArray[] = array
        (
          "path"        => $restaurantImage->pathToFile,
          "name"        => $restaurantImage->name,
          "description" => $restaurantImage->description
        );
      }

      return json_encode ($jsonReadyPHPArray);
    }

    private function loadRestaurantImages (RestaurantModel $restaurant)
    {
      return $this->restaurantImageLoader->getImagesByRestaurantID ($restaurant->id);
    }
  }

?>
