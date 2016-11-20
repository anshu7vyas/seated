<?php

require_once ('authorization/loginImports.php');
require_once ('authorization/PasswordEncryptionManager.php');
require_once ('controller/AdministratorLoader.php');
require_once ('controller/EncodedNameMatcher.php');
require_once ('controller/HostCreationProcessor.php');
require_once ('controller/ReservationCreationProcessor.php');
require_once ('controller/ReservationJSONfier.php');
require_once ('controller/ReservationLoader.php');
require_once ('controller/RestaurantCreationProcessor.php');
require_once ('controller/RestaurantUpdateProcessor.php');
require_once ('controller/RestaurantImageJSONfier.php');
require_once ('controller/RestaurantImageLoader.php');
require_once ('controller/RestaurantImageProcessor.php');
require_once ('controller/RestaurantLoader.php');
require_once ('controller/RestaurantJSONfier.php');
require_once ('controller/SearchProcessor.php');
require_once ('controller/SearchProcessorResultJSONfier.php');
require_once ('controller/RatingJSONfier.php');
require_once ('controller/RatingLoader.php');
require_once ('controller/RatingWriter.php');
require_once ('model/ReservationState.php');
require_once ('model/RestaurantModel.php');
require_once ('model/SearchParameters.php');
require_once ('model/Rating.php');
require_once ('utils/HelperFunctions.php');
require_once ('utils/ObjectInspector.php');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function createReservation ()
{
  $request             = \Slim\Slim::getInstance()->request ();
  $decodedJSONRequest  = json_decode ($request->getBody ());
  $reservationCreator  = new ReservationCreationProcessor ();
  $createdReservation  = null;

  $reservationCreator->setRequest ($decodedJSONRequest);
  $createdReservation = $reservationCreator->process ();

  if ($createdReservation != null)
  {
    getReservationById ($createdReservation->id);
  }
  else
  {
    throw new Exception ("Could not create reservation.");
  }
}

function updateReservation($id){
    require_once ('model/Diner.php');
    require_once ('model/Host.php');
    require_once ('controller/HostLoader.php');
    require_once ('model/Reservation.php');
    require_once ('model/ReservationState.php');
    require_once ('controller/ReservationStateLoader.php');
    require_once ('model/RestaurantTable.php');
    require_once ('controller/ReservationLoader.php');
    require_once ('controller/RestaurantTableLoader.php');
    require_once ('controller/ReservationWriter.php');

    $reservationLoader      = new ReservationLoader      ();
    $reservationStateLoader = new ReservationStateLoader ();
    // $reservationState = $reservationStateLoader->getStateByID (ReservationState::RESERVED);
    $request = \Slim\Slim::getInstance()->request ();
    $new_res = json_decode ($request->getBody ());

    $reservation = $reservationLoader->getReservationByID ($id);
    //$reservation = new Reservation ();
    $reservation->diner_id     = $new_res->diner_id;
    $reservation->table_id  = $new_res->table_id;
    // $reservation->host_id     = $new_res->host_id;
    $reservation->restaurant_id     = $new_res->restaurant_id;
    $reservation->partySize = $new_res->party_size;
    $reservation->partyName = $new_res->party_name;
    //$reservation->date      = $new_res->date;
    $reservation->time      = $new_res->date . " " . $new_res->time ;

    setPropertyIfExists ($reservation, 'notes',    $new_res, 'notes');
    setPropertyIfExists ($reservation, 'requests', $new_res, 'requests');

    $reservation->state  = $reservationStateLoader->getStateByID($new_res->state_id);
    $reservationWriter = new ReservationWriter ();
    $reservationWriter->reservation = $reservation;

    $id2 = 0;

    try
    {
      $id2 = $reservationWriter->update ($id);
    }
    catch (Exception $ex)
    {
      //returnResult('update', false, $id2);
      print (json_encode
      (
        array
        (
          'action'  => 'update',
          'success' => false,
          'id'      => intval ($id),
          'message' => $ex->getMessage ()
        )
      ));
      return;
    }

    getReservationById($id);
}

function getReservationById($id){
    //TODO below is how reservation by Id should be set from database classes.
    #$db = new DatabaseManager();
    #$result = $db->getReservation_ById($id);

    $jsonResultObject   = null;
    $phpResultObject    = null;
    $reservationLoader  = new ReservationLoader ();
    $loadedReservation  = null;

    $loadedReservation = $reservationLoader->getReservationByID ($id);

    if ($loadedReservation != null)
    {
      $reservationJSONfier = new ReservationJSONfier ();

      $jsonResultObject = $reservationJSONfier->createJSONString ($loadedReservation);
    }
    else
    {
      $phpResultObject = array
      (
        'message' => 'No such reservation exists.'
      );

      $jsonResultObject = json_encode ($phpResultObject);
    }

    print ($jsonResultObject);
}

function getReservationsByDate ($date)
{
    $jsonResult            = null;
    $requestedDate         = null;
    $reservationsForToday  = null;
    $reservationsOnThatDay = null;
    $jsonReadyReservations = null; // Reservations as PHP-to-JSON array.
    $reservationLoader     = null;
    $reservationJSONfier   = null;

    checkIfUserIsAdministratorOrHost("Only an administrator may request the reservations.");
    $loginStatus  = LoginManager::getStatus ();
    $restaurantID = $loginStatus->getAttributeValue ("restaurant_id");

    $requestedDate = DateTime::createFromFormat ("Y-m-d", $date);

    $jsonReadyReservations = array                   ();
    $reservationLoader     = new ReservationLoader   ();
    $reservationsForToday  = array                   ();
    $reservationJSONfier   = new ReservationJSONfier ();

    $reservationsOnThatDay = $reservationLoader->getReservationsByRestaurantIDAndDate
    (
      $restaurantID,
      $requestedDate
    );

    foreach ($reservationsOnThatDay as $reservation)
    {
      $jsonReadyReservation    = $reservationJSONfier->createPHPObjectToJSONfy ($reservation);
      $jsonReadyReservations[] = $jsonReadyReservation;
    }

    $jsonResult = json_encode ($jsonReadyReservations);

    print ($jsonResult);
}

function searchForRestaurant ()
{
  require_once ('utils/HelperFunctions.php');
  require_once ('model/SearchParameters.php');
  require_once ('controller/EncodedNameMatcher.php');
  require_once ('controller/SearchProcessorResultJSONfier.php');

  $nameMatcher  = new EncodedNameMatcher ();

  $jsonResult       = null;
  $searchParameters = null;

  $searchParameters = new SearchParameters ();
  $searchParameters->cityName  = $nameMatcher->getDecodedCityName ($_GET['city']);
  $searchParameters->time      = $_GET['time'];
  $searchParameters->date      = $_GET['date'];
  $searchParameters->partySize = $_GET['party_size'];

  if (isset ($_GET['cuisine']))
  {
    $searchParameters->categories = $_GET['cuisine'];
  }

  $foundRestaurants = null;
  $searchProcessor  = new SearchProcessor ($searchParameters);
  $searchProcessor->search                ();
  $foundRestaurants = $searchProcessor->getFoundSearchResultRestaurants ();

  $resultsJSONfier = new SearchProcessorResultJSONfier ();
  $jsonResult      = $resultsJSONfier->createJSONString ($searchProcessor);

  print ($jsonResult);
}

function searchForReservationsOfRestaurant ($restaurant)
{
  require_once ('utils/HelperFunctions.php');
  require_once ('model/SearchParameters.php');
  require_once ('controller/EncodedNameMatcher.php');
  require_once ('controller/SearchProcessorResultJSONfier.php');

  $nameMatcher  = new EncodedNameMatcher ();

  $jsonResult       = null;
  $searchParameters = null;

  $searchParameters = new SearchParameters ();
  $searchParameters->restName  = $nameMatcher->getDecodedRestaurantName($restaurant);
  $searchParameters->time      = $_GET['time'];
  $searchParameters->date      = $_GET['date'];
  $searchParameters->partySize = $_GET['party_size'];

  $foundRestaurants = null;
  $searchProcessor  = new SearchProcessor ($searchParameters);
  $searchProcessor->search                ();
  $foundRestaurants = $searchProcessor->getFoundSearchResultRestaurants ();

  $resultsJSONfier = new SearchProcessorResultJSONfier ();
  $resultsJSONfier->setReduceSingleResultToNonArray (true);
  $resultsJSONfier->getSearchResultJSONfier()->setReturnsBlocksOnly (true);
  $jsonResult      = $resultsJSONfier->createJSONString ($searchProcessor);

  print ($jsonResult);
}

function searchForKeyword ()
{
  require_once ("controller/KeywordSearch.php");

  $jsonEncodedResults = null;
  $searchResults      = null;
  $keywordSearch      = null;
  $queryString        = null;

  //$parameters    = array ();
  // parse_str ($queryString, $parameters);

  $queryString   = $_SERVER["QUERY_STRING"];
  $keywordSearch = new KeywordSearch ();
  $keywordSearch->setSearchString    ($queryString);

  $searchResults      = $keywordSearch->process ();
  $jsonEncodedResults = json_encode             ($searchResults);

  print ($jsonEncodedResults);
}

function searchForPopularityAndCity ($cityName)
{
  require_once ("controller/EncodedNameMatcher.php");
  require_once ("controller/PopularitySearch.php");
  require_once ("controller/RestaurantJSONfier.php");

  $jsonEncodedResults      = null;
  $searchResults           = null;
  $popularitySearch        = null;
  $restaurantJSONfier      = null;
  $restaurantsReadyForJSON = array ();
  $nameMatcher             = null;

  //$parameters    = array ();
  // parse_str ($queryString, $parameters);

  $restaurantJSONfier = new RestaurantJSONfier ();
  $popularitySearch   = new PopularitySearch ();
  $nameMatcher        = new EncodedNameMatcher ();

  $popularitySearch->setCityName              ($nameMatcher->getDecodedCityName ($cityName));
  $searchResults = $popularitySearch->process ();

  // Convert each restaurant into a JSON-ready PHP array.
  foreach ($searchResults as $restaurant)
  {
    $restaurantsReadyForJSON[] = $restaurantJSONfier->createPHPObjectToJSONfy ($restaurant);
  }

  $jsonEncodedResults = json_encode ($restaurantsReadyForJSON);

  print ($jsonEncodedResults);
}

function searchForCities ()
{
  require_once ("controller/CitySearch.php");
  require_once ("controller/CitySearchJSONfier.php");

  $jsonResultString   = null;
  $citySearch         = new CitySearch         ();
  $citySearchJSONfier = new CitySearchJSONfier ();

  $citySearch->process ();
  $jsonResultString = $citySearchJSONfier->createJSONString ($citySearch);

  print ($jsonResultString);
}


function createRestaurant ()
{
  $request           = \Slim\Slim::getInstance()->request ();
  $requestData       = json_decode ($request->getBody ());
  $restaurantCreator = new RestaurantCreationProcessor ();
  $restaurant        = null;

  $restaurantCreator->setRequest ($requestData);
  $restaurant = $restaurantCreator->process ();

  if ($restaurant != null)
  {
    returnResult ('createRestaurant', true, $restaurant->id);
  }
  else
  {
    returnResult ('createRestaurant', false, 0);
  }
}

function getRestaurantByID ($id)
{
  if (is_numeric ($id))
  {
    $restaurantLoader   = new RestaurantLoader ();
    $restaurant         = $restaurantLoader->getRestaurantByID($id);
    $restaurantJSONfier = new RestaurantJSONfier ();
    $jsonResult         = $restaurantJSONfier->createJSONString ($restaurant);

    print ($jsonResult);
  }
  else
  {
    getRestaurantByName ($id);
  }
}

function getRestaurantByName ($name)
{
  $nameMatcher        = new EncodedNameMatcher ();
  $restaurantLoader   = new RestaurantLoader ();
  $restaurant         = $restaurantLoader->getRestaurantByName ($nameMatcher->getDecodedRestaurantName ($name));
  $restaurantJSONfier = new RestaurantJSONfier ();
  $jsonResult         = $restaurantJSONfier->createJSONString ($restaurant);

  print ($jsonResult);
}

function updateRestaurant ()
{
  $request                   = \Slim\Slim::getInstance()->request ();
  $requestData               = json_decode ($request->getBody ());
  $restaurantUpdateProcessor = new RestaurantUpdateProcessor ();
  $updatedRestaurant         = null;
  $restaurantJSONfier        = new RestaurantJSONfier ();

  $restaurantUpdateProcessor->setRequest ($requestData);
  $updatedRestaurant = $restaurantUpdateProcessor->process ();

  if ($updatedRestaurant != null)
  {
    $jsonResultString = $restaurantJSONfier->createJSONString ($updatedRestaurant);
    print ($jsonResultString);
  }
  else
  {
    throw new Exception ("Restaurant could not be updated.");
  }
}


// Test function by Kaveh Yousefi. Can be deleted at any time.
function loginAsHost ()
{
  $request     = \Slim\Slim::getInstance()->request ();
  $requestData = json_decode ($request->getBody ());

  $jsonResultString  = null;
  $jsonReadyPHPArray = null;
  $loginCredentials  = null;
  $encryptedPassword = $encryptedPassword = PasswordEncryptionManager::encryptPassword($requestData->password);

  $loginCredentials = new LoginCredentials ();
  //$loginCredentials->setUserName ($requestData->email);
  $loginCredentials->setUserName ($requestData->username);
  $loginCredentials->setPassword ($encryptedPassword);
  $loginCredentials->setUserType (UserType::USER_TYPE_HOST);

  try
  {
    $loginStatus = null;

    $loginStatus = LoginManager::login ($loginCredentials);
  }
  catch (Exception $ex)
  {
    printf
    (
      '<p>Something is seriously wrong with your credentials.<p>
       <p>I got the following exception:</p>
       <p style="color : red;">%s</p>',
      $ex->getMessage ()
    );
  }

  if ($loginStatus != null)
  {
    $hostLoader = null;
    $host       = null;

    $hostLoader = new HostLoader ();
    $host       = $hostLoader->getHostByID ($loginStatus->userID);

    $jsonReadyPHPArray = array
    (
      "id"            => $host->id,
      "restaurant_id" => $host->getRestaurantID (),
      "name"          => $host->getFullName (),
      "username"      => $host->email
    );
  }

  $jsonResultString = json_encode ($jsonReadyPHPArray);

  print ($jsonResultString);
}

// Test function by Kaveh Yousefi. Can be deleted at any time.
function logoutAsHost ()
{
  require_once ("authorization/loginImports.php");

  if (LoginManager::isLoggedIn ())
  {
    $loginStatus = null;

    $loginStatus = LoginManager::getStatus ();
    printf ("<p>Status info: %s</p>", $loginStatus);
    printf ("<p>Goodbye, %s.</p>", $loginStatus->getUserName ());
    LoginManager::logout ();
    printf ("<p>Still logged in? - [%d]</p>", LoginManager::isLoggedIn ());
  }
  else
  {
    print ("<p>Question: How will you log out, if your not logged in?</p>");
  }
}

function loginAsAdmin ()
{

  $request     = \Slim\Slim::getInstance()->request ();
  $requestData = json_decode ($request->getBody ());

  $jsonResultString  = null;
  $jsonReadyPHPArray = null;
  $encryptedPassword = PasswordEncryptionManager::encryptPassword($requestData->password);

  $loginCredentials  = new LoginCredentials ();
  $loginCredentials->setUserName ($requestData->email);
  $loginCredentials->setPassword ($encryptedPassword);
  $loginCredentials->setUserType (UserType::USER_TYPE_ADMIN);

  $loginStatus = LoginManager::login ($loginCredentials);

  if ($loginStatus != null)
  {
    $administratorLoader = new AdministratorLoader ();
    $restaurantLoader    = new RestaurantLoader    ();
    $administrator       = $administratorLoader->getAdministratorByEmail($requestData->email);
    $restaurantsForAdmin = $restaurantLoader->getRestaurantsByAdminID ($administrator->id);
    $restaurant          = $restaurantsForAdmin[0];

    $jsonReadyPHPArray = array
    (
      "first_name"    => $administrator->firstName,
      "last_name"     => $administrator->lastName,
      "email"         => $administrator->email,
      "phone"         => $administrator->phoneNumber,
      "restaurant_id" => $restaurant->id,
      "name"          => $administrator->getFullName ()
    );
  }

  $jsonResultString = json_encode ($jsonReadyPHPArray);

  print ($jsonResultString);
}

function logoutAsAdmin ()
{
  if (LoginManager::isLoggedIn ())
  {
    LoginManager::logout ();
  }

  printf ("(Still) Logged in as Admin? - [%d]", LoginManager::isLoggedIn ());
}

function registerAsHost ()
{
  $request     = \Slim\Slim::getInstance()->request ();
  $requestData = json_decode ($request->getBody ());

  $hostCreationProcessor = new HostCreationProcessor ();
  $hostCreationProcessor->setRequest ($requestData);
  $createdHost           = $hostCreationProcessor->process ();

  print ($createdHost);
}



function uploadImage ()
{
  $request       = \Slim\Slim::getInstance()->request ();
  $requestData   = json_decode ($request->getBody ());
  $createdImage  = null;
  $imageJSONfier = new RestaurantImageJSONfier ();

  $restaurantImageProcessor = new RestaurantImageProcessor ();
  $createdImage = $restaurantImageProcessor->createRestaurantImage ($requestData);

  print ($imageJSONfier->createJSONString ($createdImage));
}

function updateImages ()
{
  $request                  = \Slim\Slim::getInstance()->request ();
  $requestData              = json_decode ($request->getBody ());
  $imageJSONfier            = new RestaurantImageJSONfier  ();
  $restaurantImageProcessor = new RestaurantImageProcessor ();
  $jsonReadyPHPImages       = array ();

  foreach ($requestData as $imageData)
  {
    $createdImage         = $restaurantImageProcessor->createRestaurantImage ($imageData);
    $jsonReadyPHPImages[] = $imageJSONfier->createPHPObjectToJSONfy ($createdImage);
  }

  print (json_encode ($jsonReadyPHPImages));
}


/**
 * Sets an objects's property, if the decoded JSON object contains
 * a particular property name.
 *
 * @param type $receivingObject            The object to modify by
 *                                         setting its property.
 * @param type $propertyOfReceivingObject  The $receivingObject's
 *                                         property name to potentially
 *                                         set.
 * @param type $decodedJSONObject          The decoded JSON object.
 * @param type $jsonPropertyNameToCheck    The JSON object's property
 *                                         name to check for existence.
 */
function setPropertyIfExists
(
  $receivingObject,
  $propertyOfReceivingObject,
  $decodedJSONObject,
  $jsonPropertyNameToCheck
)
{
  if (property_exists ($decodedJSONObject, $jsonPropertyNameToCheck))
  {
    $receivingObject->$propertyOfReceivingObject = $decodedJSONObject->$jsonPropertyNameToCheck;
  }
}

function getAllImagesByRestaurant ($id){
    //getImagesByRestaurantID($id);

  $restaurantImageLoader   = new RestaurantImageLoader ();
  $restaurantImages        = $restaurantImageLoader->getImagesByRestaurantID ($id);
  $restaurantImageJSONfier = new RestaurantImageJSONfier ();
  $jsonReadyPHPArray       = array ();

  if (count ($restaurantImages) > 0)
  {
    foreach ($restaurantImages as $restaurantImage)
    {
      $phpImageData      = null;

      try
      {
        $phpImageData = $restaurantImageJSONfier->createPHPObjectToJSONfy ($restaurantImage);
      }
      catch (Exception $ex)
      {
        printf ('<span style="color : red;">Exception: %s</span>', $ex->getMessage ());
      }

      if ($phpImageData !== null)
      {
        $jsonReadyPHPArray[] = $phpImageData;

        /*
        printf ('<div><img src="%s" alt="The image %s." title="%s" /></div>',
                $phpImageData['data'],
                $phpImageData['description'],
                $phpImageData['description']);
         *
         */
      }
      else
      {
        //print ("<div>NO DATA = NO IMAGE!</div>");
      }
    }
  }

  print (json_encode ($jsonReadyPHPArray));
}

function createHost($id){
    require_once ('controller/HostCreationProcessor.php');

    $request = \Slim\Slim::getInstance()->request ();
    $requestData = json_decode ($request->getBody ());
    $hostCreator = new HostCreationProcessor();
    $host = null;
    $hostCreator->setRequest ($requestData);

    try{
        $host = $hostCreator->process ();
    }
    catch (Exception $ex){
    //print ($ex->getMessage ());
        header ($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        echo $ex;
    }
    if ($host != null){
        returnResult ('createHost', true, $host->id);
    }
    else{
        returnResult ('createHost', false, 0);
    }
}

function getHostByID ($id)
{
  $hostLoader = new HostLoader ();
  $host       = $hostLoader->getHostByID ($id);

  if ($host != null)
  {
    $jsonReadyPHPArray = array
    (
      "id"            => $host->id,
      "restaurant_id" => $host->getRestaurantID (),
      "name"          => $host->getFullName     (),
      "username"      => $host->email
    );
    $jsonResultString = json_encode ($jsonReadyPHPArray);

    print ($jsonResultString);
  }
  else
  {
    returnResult ("getHostByID", false, 0);
  }
}

// TODO: Clarify, if "id" of the host may be changed.
function updateHost ($restaurantID, $hostID)
{
  $request            = \Slim\Slim::getInstance()->request ();
  $decodedJSONRequest = json_decode ($request->getBody ());

  $hasUpdated           = false;
  $hostLoader           = new HostLoader           ();
  $hostWriter           = new HostWriter           ();
  $hostToUpdate         = $hostLoader->getHostByID ($hostID);
  $isAdministrator      = false;
  $isMatchingRestaurant = false;
  $loginStatus          = null;

  if (LoginManager::isLoggedIn ())
  {
    $loginStatus     = LoginManager::getStatus       ();
    $isAdministrator = $loginStatus->isAdministrator ();
  }
  else
  {
    $isAdministrator = false;
  }

  // Not logged in as an administrator? => FORBIDDEN.
  if (! $isAdministrator)
  {
    throw new Exception ("Only an administrator may update a host.");
  }

  // Administrator tries to access wrong restaurant? => FORBIDDEN.
  if (! $loginStatus->hasAttributeWithValue ("restaurant_id", $restaurantID))
  {
    $exceptionText = sprintf
    (
      "You are not permitted to update the restaurant, as your " .
      "restaurant ID (%d) does not match the requested restaurant ID (%d).",
      $loginStatus->getAttributeValue ("restaurant_id"),
      $restaurantID
    );
    throw new Exception ($exceptionText);
  }


  // No host with with $hostID? => ERROR.
  if ($hostToUpdate == null)
  {
    throw new Exception (sprintf ('No host of ID %d found.', $hostID));
  }

  // Host belongs to another restaurant? => FORBIDDEN.
  if ($hostToUpdate->getRestaurantID () != $loginStatus->getAttributeValue ("restaurant_id"))
  {
    $exceptionText = sprintf
    (
      "There is not host with your restaurant ID.",
      $loginStatus->getAttributeValue ("restaurant_id")
    );
    throw new Exception ($exceptionText);
  }


  if (ObjectInspector::hasProperty ($decodedJSONRequest, 'name'))
  {
    $hostToUpdate->setFullName ($decodedJSONRequest->name);
  }

  setPropertyIfExists ($hostToUpdate, 'email', $decodedJSONRequest, 'username');

  if (ObjectInspector::hasProperty ($decodedJSONRequest, 'password'))
  {
    $plainPassword     = $decodedJSONRequest->password;
    $encryptedPassword = PasswordEncryptionManager::encryptPassword ($plainPassword);
    $hostToUpdate->encryptedPassword = $encryptedPassword;
  }

  $hostWriter->setHost ($hostToUpdate);

  try
  {
    $hasUpdated = $hostWriter->update ();
  }
  catch (Exception $ex)
  {
    $hasUpdated = false;
    print ("EXCEPTION: " . $ex->getMessage ());
  }

  if ($hasUpdated)
  {
    //printf ("<p>Updated the host with id = %d.</p>", $hostToUpdate->id);

    returnResult ("updateHost", true, $hostToUpdate->id);
  }
  else
  {
    //printf ("<p>Somehow I could not update the host with id = %d.</p>", $hostToUpdate->id);
    returnResult ("updateHost", false, $hostToUpdate->id);
  }
}

function createRatingForRestaurant($id)
{
  $rating = new Rating();
  $ratingWriter = new RatingWriter();
  $request             = \Slim\Slim::getInstance()->request ();
  $decodedJSONRequest  = json_decode ($request->getBody ());
  $rating->username = $decodedJSONRequest->username;
  $rating->restaurantID = $decodedJSONRequest->restaurant_id;
  $rating->rating = $decodedJSONRequest->rating;
  $rating->description = $decodedJSONRequest->description;
  $ratingWriter->rating = $rating;

  if($ratingWriter->ratingAllowed($id, $rating->username) == null){
      $jsonReadyPHPObject = array(
        "message"    => "Rating creation failed.",
    );
    printf(json_encode ($jsonReadyPHPObject));
    return;
  }
  $createdRatingID = $ratingWriter->persist();

  if ($createdRatingID <= 0)
  {
    $jsonReadyPHPObject = array(
        "message"    => "Rating creation failed.",
    );
    printf(json_encode ($jsonReadyPHPObject));
  }else{
    $jsonReadyPHPObject = array(
        "message"    => "Rating created successful.",
    );
    printf(json_encode ($jsonReadyPHPObject));
  }
}

function getAllRatingsForRestaurant($id){
  require_once ("controller/RatingLoader.php");
  $ratings = getRatingsForRestaurant($id);
  $ratingJSONfier = new RatingJSONfier ();
  $jsonReadyPHPArray = array ();
  $averageRating = 0;
  if (count ($ratings) > 0){
    foreach ($ratings as $rating){
      $phpRatingData = null;
      $averageRating += $rating->rating;
      try{
        $phpRatingData = $ratingJSONfier->createPHPObjectToJSONfy ($rating);
      }
      catch (Exception $ex){
        printf ('<span style="color : red;">Exception: %s</span>', $ex->getMessage ());
      }

      if ($phpRatingData !== null){
        $jsonReadyPHPArray[] = $phpRatingData;
      }
    }

    $jsonReadyPHPObject = array(
        "average_rating"    => $averageRating = $averageRating/count($ratings),
    );

    array_unshift($jsonReadyPHPArray, $jsonReadyPHPObject);
  }
  echo (json_encode ($jsonReadyPHPArray));
}

function checkIfUserIsAdministrator($exceptionMessage = "You must be logged in as an administrator for this operation.")
{
  if (LoginManager::isLoggedInAsUserOfThisType (UserType::USER_TYPE_ADMIN))
  {
    return;
  } else {
    throw new Exception ($exceptionMessage);
  }
}

function checkIfUserIsAdministratorOrHost($exceptionMessage = "You must be logged in as an administrator for this operation.")
{
  if (LoginManager::isLoggedInAsUserOfThisType (UserType::USER_TYPE_ADMIN))
  {
    return;
  }
  if (LoginManager::isLoggedInAsUserOfThisType (UserType::USER_TYPE_HOST)){
    return;
  } else {
    throw new Exception ($exceptionMessage);
  }
}

?>