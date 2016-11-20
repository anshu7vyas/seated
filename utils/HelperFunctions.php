<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function returnResult($action, $success, $id){
    echo json_encode(array (
        'action' => $action,
        'success' => $success,
        'id' => intval($id),
    ));
}

function isRightRestaurant (RestaurantModel $restaurant, $desiredRestaurantID){
    if (($desiredRestaurantID === null) || ($desiredRestaurantID === 0)){
        return true;
    }
    else if ($restaurant->id == null){
        return false;
    }
    else{
        return ($restaurant->id == $desiredRestaurantID);
    }
}
  
function hasRightLocation (RestaurantModel $restaurant, $locationID){
    if (($locationID === null) || ($locationID === 0)){
        return true;
    }
    else if ($restaurant->location == null){
        return false;
    }
    else{
        return ($restaurant->location->id == $locationID);
    }
}
  
function hasRightCuisine ($restaurant, $desiredCuisine){
    if ($desiredCuisine == null){
        return true;
    }
    else if ($restaurant->cuisine == null){
        return false;
    }
    else{
        return ($restaurant->cuisine == $desiredCuisine);
    }
}
  
function hasRightCategories ($restaurant, $desiredCategoryIDs){
    if ($desiredCategoryIDs == null){
        return true;
    }
    else if (empty ($desiredCategoryIDs)){
        return true;
    }
    else{
        // Collect the restaurant's category IDs.
        $categoryIDsOfRestaurant = array ();
        foreach ($restaurant->categories as $restaurantCategory){
            $categoryIDsOfRestaurant[] = $restaurantCategory->id;
        }
        // Check if each desired category ID is in the restaurant's.
        foreach ($desiredCategoryIDs as $categoryIDToCheck){
            if (! in_array ($categoryIDToCheck, $categoryIDsOfRestaurant)){
                return false;
            }
        }
        return true;
    }
}
  
function hasEnoughSeats ($table, $partySize){
    return ($table->numberOfSeats >= $partySize);
}
  
function convertSQLTimestampToPHPDateTime ($sqlTimestamp){
    $phpDateTimeObject           = null;
    $sqlTimeAsMonthDayYearString = null;
    
    $sqlTimeAsMonthDayYearString = date ('m/d/Y', $sqlTimestamp);
    $phpDateTimeObject           = DateTime::createFromFormat
    (
      'm/d/Y',
      $sqlTimeAsMonthDayYearString
    );    
    return $phpDateTimeObject;
}
  
function getTablesForRestaurant ($restaurant){
    require_once ('controller/RestaurantTableLoader.php');    
    $tablesForRestaurant = null;
    $tableLoader         = null;
    $tableLoader         = new RestaurantTableLoader ();
    $tablesForRestaurant = $tableLoader->getTablesByRestaurantID ($restaurant->id);
    return $tablesForRestaurant;
}
  
function getReservationsForTable ($table){
    require_once ('controller/ReservationLoader.php');
    $reservationLoader    = new ReservationLoader ();
    $reservationsForTable = $reservationLoader->getReservationByTableID ($table->id);
    return $reservationsForTable;
}
  
function getReservationsForTableOnCertainDay ($reservationsForTable, $desiredDay){
    $reservationsForTheDay = array ();
    foreach ($reservationsForTable as $reservation){
        $reservationDay = date ("Y-m-d", strtotime ($reservation->time));
      
        if ($desiredDay == $reservationDay){
            $reservationsForTheDay[] = $reservation;
        }
    }
    return $reservationsForTheDay;
}
  
// Maps the hours [0, 23] to a boolean value {true = free, false = reserved}.
function getTimeslotMapForReservationsForTableOnCertainDay ($reservationForTableOnDay){
    $freeTimeslots = array ();
    $occupiedHours = array ();
    
    // Collect the reserved hours into the "$occupiedHours" array.
    foreach ($reservationForTableOnDay as $reservation){
        $reservationHour = date ("H", strtotime ($reservation->time));
        $occupiedHours[] = intval ($reservationHour);
    }
    // Check if hour for being reserved or not.
    for ($hour = 0; $hour <= 23; $hour++){
        if (in_array ($hour, $occupiedHours)){
            $freeTimeslots[$hour] = false;
        }
        else{
            $freeTimeslots[$hour] = true;
        }
    }
    return $freeTimeslots;
}
  
// Maps the hours [0, 23] to a boolean value {true = free, false = reserved}.
function getTimeslotMapForTable ($table, $desiredDay){
    $reservationsForTable = getReservationsForTable ($table);
    $reservationsOnDay    = getReservationsForTableOnCertainDay($reservationsForTable, $desiredDay);
    $freeTimeslots        = getTimeslotMapForReservationsForTableOnCertainDay ($reservationsOnDay);
    return $freeTimeslots;
}
  
function getTimeslotSuggestions ($timeslotMap, $desiredHour){
    $suggestions                         = array ();
    $suggestions['hasMatch']             = false;
    $suggestions['isPerfectMatch']       = false;
    $suggestions['timeslotBefore']       = null;
    $suggestions['timeslotAt']           = null;
    $suggestions['timeslotAfter']        = null;
    $suggestions['hourDifferenceBefore'] = null;
    $suggestions['hourDifferenceAfter']  = null;
    
    if ($timeslotMap[$desiredHour] == true){
        $suggestions['hasMatch']       = true;
        $suggestions['isPerfectMatch'] = true;
        $suggestions['timeslotAt']     = $desiredHour;
    }
    else{
        $suggestions['isPerfectMatch'] = false;
        $suggestions['timeslotAt']     = null;
    }
    
    // Search for the neares free hour before and after the "$desiredHour".
    for ($hour = 0; $hour <= 23; $hour++){
        if ($timeslotMap[$hour] == true){
            if ($hour < $desiredHour){
                $suggestions['hasMatch']       = true;
                $suggestions['timeslotBefore'] = $hour;
            }
            else{
                $suggestions['hasMatch']      = true;
                $suggestions['timeslotAfter'] = $hour;
                break;
            }
        }
    }
    
    if ($suggestions['timeslotBefore'] !== null){
        $suggestions['hourDifferenceBefore'] = abs ($suggestions['timeslotBefore'] - $desiredHour);
    }
    
    if ($suggestions['timeslotAfter'] !== null){
        $suggestions['hourDifferenceAfter'] = abs ($suggestions['timeslotAfter'] - $desiredHour);
    }
    
    $suggestions['totalHourDifference'] = ($suggestions['hourDifferenceBefore'] + $suggestions['hourDifferenceAfter']);
    return $suggestions;
}
  
function convertHourToTime ($hour){
    if ($hour !== null){
        return sprintf ('%02d:00:00', $hour);
    }
    else{
        return null;
    }
}
?>