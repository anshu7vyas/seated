<?php

  /* Converts geo-coordinates from and to addresses.
   * 
   * Date:   2015-11-10
   * Author: Kaveh Yousefi
   */

  require_once ("model/GeoCoordinates.php");
  
  
  class GeoCoordinatesConverter
  {
    private function __construct ()
    {
    }
    
    
    // -> "http://nazcalabs.com/blog/how-to-get-gps-coordinates-from-an-address-with-php-and-google-maps/"
    public static function getGeoCoordinatesFromAddressString ($address)
    {
      $geoCoordinates = null;
      $encodedAddress = urlencode         ($address);
      $url            = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=" . $encodedAddress;
      $response       = file_get_contents ($url);
      $json           = json_decode       ($response, true);
      $status         = $json['status'];
      
      if ($status == 'OK')
      {
        $latitude  = $json['results'][0]['geometry']['location']['lat'];
        $longitude = $json['results'][0]['geometry']['location']['lng'];
        $geoCoordinates = new GeoCoordinates ($latitude, $longitude);
      }
      else
      {
        $geoCoordinates = null;
      }

      return $geoCoordinates;
    }

    // -> "http://99webtools.com/blog/get-address-from-latitudelongitude-in-php/"
    public static function getAdressStringFromGeoCoordinates (GeoCoordinates $geoCoordinates)
    {
      $url    = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' .
                trim ($geoCoordinates->getLatitude  ()) .',' .
                trim ($geoCoordinates->getLongitude ()) .'&sensor=false';
      $json   = @file_get_contents ($url);
      $data   = json_decode        ($json);
      $status = $data->status;

      if ($status == "OK")
      {
        return $data->results[0]->formatted_address;
      }
      else
      {
        return false;
      }
    }
  }
?>
