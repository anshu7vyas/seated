<?php
  
  /* Provides helper functions for handling objects.
   * 
   * Date:   03.12.2015
   * Author: Kaveh Yousefi
   */
  
  
  class ObjectInspector
  {
    private function __construct ()
    {
    }
    
    
    public static function getObjectPropertyOrSubstitute
    (
      $object,
      $objectPropertyName,
      $substituteValue = null
    )
    {
      if (property_exists ($object, $objectPropertyName))
      {
        return $object->$objectPropertyName;
      }
      else
      {
        return $substituteValue;
      }
    }
    
    public static function hasProperty ($object, $propertyName)
    {
      return property_exists ($object, $propertyName);
    }
    
    /**
     * Sets an objects's property, if the another object contains
     * a particular property name.
     *
     * @param type $receivingObject            The object to modify by
     *                                         setting its property.
     * @param type $propertyOfReceivingObject  The $receivingObject's
     *                                         property name to potentially
     *                                         set.
     * @param type $objectToQueryProperty      The object whose property
     *                                         should be queried.
     * @param type $propertyOfObjectToQuery    Property to be queried.
     */
     public static function setPropertyIfQueriedObjectContainsIt
     (
       $receivingObject,
       $propertyOfReceivingObject,
       $objectToQueryProperty,
       $propertyOfObjectToQuery
     )
     {
       if ($receivingObject == null)
       {
         throw new Exception ("Receiving object is null.");
       }
       if ($objectToQueryProperty == null)
       {
         throw new Exception ("Object to query is null.");
       }
       
       if (property_exists ($objectToQueryProperty, $propertyOfObjectToQuery))
       {
         $receivingObject->$propertyOfReceivingObject
            = $objectToQueryProperty->$propertyOfObjectToQuery;
       }
     }
  }

?>
