<?php
  
  /* Models a category.
   * 
   * Date:   02.11.2015
   * Author: Kaveh Yousefi
   */
  
  
  class Category
  {
    public $id;
    public $labelText;
    public $pathToImage;
    public $description;
    
    
    public function __construct ()
    {
      $this->id          = null;
      $this->labelText   = null;
      $this->pathToImage = null;
      $this->description = null;
    }
    
    
    public function getID ()
    {
      return $this->id;
    }
    
    public function setID ($id)
    {
      $this->id = $id;
    }
    
    public function getLabelText ()
    {
      return $this->label;
    }
    
    public function setLabelText ($labelText)
    {
      $this->labelText = $labelText;
    }
    
    public function getPathToImage ()
    {
      return $this->pathToImage;
    }
    
    public function setPathToImage ($pathToImage)
    {
      $this->pathToImage = $pathToImage;
    }
    
    public function getDescription ()
    {
      return $this->description;
    }
    
    public function setDescription ($description)
    {
      $this->description = $description;
    }
  }
  
?>
