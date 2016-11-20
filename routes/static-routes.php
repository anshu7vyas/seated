<?php
// defining pretty URL routes for views

$app->get(
  '/restaurants/',
  function(){
    require('views/restaurants.php');
  }
);

$app->get(
  '/',
  function(){
    require('views/diner.php');
  }
);

$app->get(
  '/widget',
  function(){
    require('views/widget.php');
  }
);
