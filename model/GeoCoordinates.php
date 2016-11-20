<?php

  /* Models geo-coordinates.
   * 
   * Date:   2015-11-10
   * Author: Kaveh Yousefi
   */
  
  
  class GeoCoordinates
  {
    private $latitude;
    private $longitude;


    public function __construct ($latitude, $longitude)
    {
      $this->latitude  = $latitude;
      $this->longitude = $longitude;
    }


    public function getLatitude ()
    {
      return $this->latitude;
    }

    public function getLongitude ()
    {
      return $this->longitude;
    }
  }

?>
