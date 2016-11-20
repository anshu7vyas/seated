<?php
  
  // 04.12.2015
  
  require_once ("authorization/LoginManager.php");
  require_once ("authorization/UserType.php");
  require_once ("controller/RestaurantImageLoader.php");
  require_once ("controller/RestaurantImageWriter.php");
  require_once ("model/RestaurantImage.php");
  require_once ("utils/ObjectInspector.php");
  
  
  class RestaurantImageProcessor
  {
    //const BASE_URL = "http://sfsuswe.com/~f15g08/uploads/";
    //TODO: Set the BASE_URL to the correct environment  
    const BASE_URL = "/home/Chris.Buchhold/public_html/uploads/";
    const PATH_FRONTEND = "public/uploads/";
    //const BASE_URL = "/home/kaveh.yousefi/public_html/branch/sprint4_backend/uploads/";
    
    private $baseURL;
    private $pathFrontEnd;
    private $restaurantImageWriter;
    
    
    public function __construct ()
    {
      $this->baseURL               = self::BASE_URL;
      $this->pathFrontEnd          = self::PATH_FRONTEND;
      $this->restaurantImageWriter = new RestaurantImageWriter ();
    }
    
    
    public function setBaseURL ($baseURL)
    {
      $this->baseURL = $baseURL;
    }
    
    
    public function createRestaurantImage ($decodedJSONRequest)
    {
      $restaurantImage     = null;
      $base64EncodedImage  = null;
      $base64DecodedImage  = null;
      $destinationFilePath = null;
      
      $restaurantImage     = new RestaurantImage ();
      $base64EncodedImage  = $decodedJSONRequest->file_data;
      $originalFileName    = $decodedJSONRequest->original_file_name;
      $description         = $decodedJSONRequest->description;
      $destinationFilePath = $this->baseURL . $originalFileName;
      $base64DecodedImage  = $this->decodeImage ($base64EncodedImage);
      
      $this->writeDecodedImageToFile ($base64DecodedImage, $destinationFilePath);
      
      $restaurantImage->originalName = $originalFileName;
      $restaurantImage->name         = $originalFileName;
      $restaurantImage->pathToFile   = $this->pathFrontEnd . $originalFileName;
      $restaurantImage->description  = $description;
      $restaurantImage->isCover      = $decodedJSONRequest->is_cover;
      $restaurantImage->restaurant   = $this->getRestaurant ($decodedJSONRequest);
      
      $this->restaurantImageWriter->setRestaurantImage($restaurantImage);
      $this->restaurantImageWriter->persist ();
      
      //printf ('<img src="%s" alt="File path=%s" />', $destinationFilePath, $destinationFilePath);
      
      return $restaurantImage;
    }
    
    
    
    ////////////////////////////////////////////////////////////////////
    // -- Implementation of auxiliary methods.                     -- //
    ////////////////////////////////////////////////////////////////////
    
    private function decodeImage ($base64EncodedImage)
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
    
    // Stores the binary image data to the destination file.
    private function writeDecodedImageToFile ($base64DecodedImage, $destinationFileName)
    {
      touch ($destinationFileName);
      chmod ($destinationFileName, 0775);
      // Open the newly created copy image file for writing binary data.
      $fileHandleForDecodedImage = fopen ($destinationFileName, "wb");
      
      // Write the decoded Base64 data into the copy file, and close it.
      fwrite ($fileHandleForDecodedImage, $base64DecodedImage);
      fclose ($fileHandleForDecodedImage);
    }
    
    private function getRestaurant ($request)
    {
      $restaurantLoader = new RestaurantLoader    ();
      $restaurantID     = $this->getRestaurantID  ($request);
      
      return $restaurantLoader->getRestaurantByID ($restaurantID);
    }
    
    private function getRestaurantID ($request)
    {
      if (ObjectInspector::hasProperty ($request, "restaurant_id"))
      {
        return $request->restaurant_id;
      }
      else if (LoginManager::isLoggedInAsUserOfThisType (UserType::USER_TYPE_ADMIN))
      {
        return LoginManager::getStatus ()->getAttributeValue ("restaurant_id");
      }
      else
      {
        return 1;
      }
    }
  }
  
?>
