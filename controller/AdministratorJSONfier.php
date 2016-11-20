<?php

  /* Creates a JSON string representation of an Administrator.
   * 
   * Date:   03.12.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("model/Administrator.php");
  
  
  class AdministratorJSONfier
  {
    public function __construct ()
    {
    }
    
    
    public function createJSONString (Administrator $administrator)
    {
      $jsonString         = null;
      $jsonReadyPHPObject = null;
      
      $jsonReadyPHPObject = array
      (
        "id"         => $administrator->id,
        "first_name" => $administrator->firstName,
        "last_name"  => $administrator->lastName,
        "email"      => $administrator->email,
        "phone"      => $administrator->phoneNumber,
        "position"   => $administrator->position,
      );
      
      $jsonString = json_encode ($jsonReadyPHPObject);
      
      return $jsonString;
    }
  }
?>
