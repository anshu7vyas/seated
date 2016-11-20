<?php
  
  /* Converts a RestaurantImage into a JSON encoded string.
   * 
   * Date:   08.12.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("controller/Base64ImageEncoding.php");
  
  
  class RestaurantImageJSONfier
  {
    private $base64Encoding;
    
    
    public function __construct ()
    {
      $this->base64Encoding = new Base64ImageEncoding ();
    }
    
    
    public function createJSONString (RestaurantImage $restaurantImage)
    {
      $jsonResultObject = null;
      $phpResultObject  = null;

      $phpResultObject  = $this->createPHPObjectToJSONfy ($restaurantImage);
      $jsonResultObject = json_encode ($phpResultObject);

      return $jsonResultObject;
    }
    
    public function createPHPObjectToJSONfy (RestaurantImage $restaurantImage)
    {
      $jsonReadyPHPArray = null;
      
      $jsonReadyPHPArray = array
      (
        "id"            => $restaurantImage->id,
        "restaurant"    => $restaurantImage->restaurant->id,
        "original_name" => $restaurantImage->originalName,
        "name"          => $restaurantImage->name,
        "path_to_file"  => $restaurantImage->pathToFile,
        "description"   => $restaurantImage->description,
        "is_cover"      => $restaurantImage->isCover,
        //"data"          => $this->encodeRestaurantImage ($restaurantImage),
      );
      
      return $jsonReadyPHPArray;
    }
    
    
    private function encodeRestaurantImage (RestaurantImage $restaurantImage)
    {
      return $this->base64Encoding->getEncodedRestaurantImage
      (
        $restaurantImage,
        Base64ImageEncoding::ENCODE_WITH_METADATA
      );
    }
  }

?>