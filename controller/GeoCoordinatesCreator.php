<?php

  /* Creates geographic coordinates.
   * 
   * Date:   09.12.2015
   * Author: Kaveh Yousefi
   */
  
  
  class GeoCoordinatesCreator
  {
    private function __construct ()
    {
    }
    
    
    public static function getGeoCoordinatesFromStreetAndLocation
    (
      $street,
      Location $location = null
    )
    {
      $geoCoordinates = null;
      $addressString  = null;
      
      $addressString  = self::getAddressStringFromStreetAndLocation ($street, $location);
      
      if ($addressString != null)
      {
        $geoCoordinates = GeoCoordinatesConverter::getGeoCoordinatesFromAddressString ($addressString);
      }
      else
      {
        $geoCoordinates = null;
      }

      return $geoCoordinates;
    }

    // Example for address string: -> "https://developers.google.com/maps/documentation/javascript/geocoding"
    public static function getAddressStringFromStreetAndLocation
    (
      $street,
      Location $location = null
    )
    {
      $addressString = null;

      if (($location == null) || ($street == null))
      {
        return null;
      }
      
      $addressString = sprintf
      (
        "%s %s, %s, %s, USA",
        $location->zip,
        $street,
        $location->state,
        $location->city
      );

      return $addressString;
    }
  }
  
  
?>