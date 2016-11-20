<?php

  /* Encodes and decodes image in and from the Base64 encoding.
   * 
   * Date:   08.12.2015
   * Author: Kaveh Yousefi
   */
  
  
  class Base64ImageEncoding
  {
    // Do not prepend "data:image/..." part.
    const ENCODE_WITHOUT_METADATA = 0;
    // Prepend "data:image/..." part.
    const ENCODE_WITH_METADATA    = 1;
    
    
    public function __construct ()
    {
    }
    
    
    public function getDecodedImageData ($base64EncodedImage)
    {
      $base64DecodedImage = null;
      $decodablePart      = null;
      $containsMetadata   = false;
      
      $containsMetadata = (strpos ($base64EncodedImage, "data:image") !== FALSE);
      
      if ($containsMetadata)
      {
        $base64EncodedImageParts = explode (",", $base64EncodedImage);
        $decodablePart           = $base64EncodedImageParts[1];
      }
      else
      {
        $decodablePart = $base64EncodedImage;
      }
      
      $base64DecodedImage = base64_decode ($base64EncodedImage);
      
      return $base64DecodedImage;
    }
    
    public function getEncodedImageDataFromFileByPath
    (
      $filePathOfImageToEncode,
      $prependMetadata = self::ENCODE_WITH_METADATA
    )
    {
      if (! file_exists ($filePathOfImageToEncode))
      {
        $exceptionText = sprintf
        (
          'The file "%s" does not exist.',
          $filePathOfImageToEncode
        );
        throw new Exception ($exceptionText);
      }
      
      $base64EncodedString = null;  // Final encoded data.
      $base64EncodedImage  = null;  // Encoded image without metadata.
      $contentOfImageFile  = null;
      $imageFileType       = null;
      
      $imageFileType       = pathinfo ($filePathOfImageToEncode,
                                       PATHINFO_EXTENSION);
      $contentOfImageFile  = file_get_contents ($filePathOfImageToEncode);
      $base64EncodedImage  = base64_encode     ($contentOfImageFile);
      
      switch ($prependMetadata)
      {
        case self::ENCODE_WITHOUT_METADATA :
          $base64EncodedString = $base64EncodedImage;
          break;
        case self::ENCODE_WITH_METADATA :
          $base64EncodedString = sprintf
          (
            "data:image/%s;base64,%s",
            $imageFileType,
            $base64EncodedImage
          );
          break;
        default :
          $exceptionText = sprintf
          (
            'Invalid value for the $prependMetadata parameter: %s.',
            $prependMetadata
          );
          throw new Exception ($exceptionText);
      }
      
      return $base64EncodedString;
    }
    
    public function getEncodedRestaurantImage
    (
      RestaurantImage $restaurantImage,
      $prependMetadata = self::ENCODE_WITH_METADATA
    )
    {
      if ($restaurantImage == null)
      {
        throw new Exception ("The RestaurantImage is NULL.");
      }
      
      return $this->getEncodedImageDataFromFileByPath
      (
        $restaurantImage->pathToFile,
        $prependMetadata
      );
    }
  }
  
?>
