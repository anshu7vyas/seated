<?php
require_once ("../model/RestaurantImage.php");
require_once ("../controller/RestaurantImageWriter.php");
require_once ("../utils/DatabaseConnectionProvider.php");

$image = new RestaurantImage();

$target_dir = "../uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
$baseURL = "http://sfsuswe.com/~f15g08/uploads/";
$errorMessage;

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $errorMessage = "File is not an image";
        $uploadOk = 0;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    $errorMessage = "File already exists";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 1000000) {
    $errorMessage = "Your file is too large (not over 1MB).";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
    $errorMessage = "Only JPG, JPEG, PNG files are allowed";
    $uploadOk = 0;
}

$imageName = basename( $_FILES["fileToUpload"]["name"]);
$restaurantID = null;
$imageDescription = null;
$imageIsCover = true;

if(isset($_POST['restaurantID'])) {
    $restaurantID = $_POST["restaurantID"];
}

if(isset($_POST['description'])) {
    $imageDescription = $_POST["description"];
}

if(isset($_POST['isCover'])) {
    $imageIsCover = $_POST["isCover"];
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    $errorMessage = $errorMessage . " Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $image->restaurant = $restaurantID;
        $image->originalName = $imageName;
        $image->name = null;
        $image->pathToFile = $baseURL.$imageName;
        $image->description = $imageDescription;
        $image->isCover = $imageIsCover;
        $imageWriter = new RestaurantImageWriter();
        $imageWriter->setRestaurantImage($image);
        $imageID = $imageWriter->persist();
        $errorMessage = "The file ". $imageName. " has been uploaded.";
    } else {
        $errorMessage = "There was an error uploading your file.";
    }
}
echo $errorMessage;
?> 