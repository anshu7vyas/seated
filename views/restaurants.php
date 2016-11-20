<!DOCTYPE html>
<html>
<head>
  <title>Host Interface</title>
  <?php require 'base.html' ?>
  <?php require('vendor-links.html'); ?>
  <link rel="stylesheet" type="text/css" href="public/css/general.css">
  <script type="text/javascript" src="public/app/common/services/reservations.js"></script>
  <script type="text/javascript" src="public/app/common/services/restaurants.js"></script>
  <script type="text/javascript" src="public/app/common/services/images.js"></script>
  <script type="text/javascript" src="public/app/common/services/momentJS.js"></script>
  <script type="text/javascript" src="public/app/common/filters.js"></script>
  <script type="text/javascript" src="public/app/restaurant/restaurant.services.js"></script>
  <script type="text/javascript" src="public/app/restaurant/restaurant.js"></script>
</head>
<body ng-app="restaurantApp">
<div id="view" ng-view ng-cloak></div>

</body>
</html>