<?php
  
  /* Faciliates parsing GET request query strings.
   * 
   * Date:   28.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  class QueryProcessor
  {
    private $queryString;
    private $queryParameters;
    
    
    public function __construct ()
    {
      $this->queryString = null;
    }
    
    
    public function setQueryString ($queryString)
    {
      $this->queryString = $queryString;
    }
    
    
    public function process ()
    {
      if ($this->queryString !== null)
      {
        $this->queryParameters = array ();
        parse_str ($this->queryString, $this->queryParameters);
      }
      else
      {
        $this->queryParameters = array ();
      }
    }
    
    
    public function getQueryParameters ()
    {
      return $this->queryParameters;
    }
    
    public function hasQueryParameterOfName ($queryParameterName)
    {
      return isset ($this->queryParameters[$queryParameterName]);
    }
    
    public function getQueryParameterValue ($queryParameterName)
    {
      if (! $this->hasQueryParameterOfName ($queryParameterName))
      {
        throw new Exception ("Invalid query parameter name: " . $queryParameterName);
      }
      
      return $this->queryParameters[$queryParameterName];
    }
    
    public function getQueryParameterValueOrSubstitute
    (
      $queryParameterName,
      $substituteValue = null
    )
    {
      if ($this->hasQueryParameterOfName ($queryParameterName))
      {
        return $this->queryParameters[$queryParameterName];
      }
      else
      {
        return $substituteValue;
      }
    }
  }

?>
