<!DOCTYPE html>
<html ng-app="dinerApp">
<head>
  <title>{{pageTitle}}</title>
  <?php require 'base.html' ?>
  <?php require('vendor-links.html'); ?>
  <link rel="stylesheet" type="text/css" href="public/css/general.css">
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCPvJ5Z8Jr40Rr-tnImV8EMYOiBrYTxYPw&callback=initMap"></script>
  <script type="text/javascript" src="public/vendor/lodash-3.10.1.min.js"></script>
  <script type="text/javascript" src="public/vendor/angular-simple-logger.min.js"></script>
  <script type="text/javascript" src="public/vendor/angular-google-maps-2.2.1.min.js"></script>
  <script type="text/javascript" src="public/app/common/services/reservations.js"></script>
  <script type="text/javascript" src="public/app/common/services/restaurants.js"></script>
  <script type="text/javascript" src="public/app/common/services/search.js"></script>
  <script type="text/javascript" src="public/app/common/filters.js"></script>
  <script type="text/javascript" src="public/app/diner/diner.services.js"></script>
  <script type="text/javascript" src="public/app/diner/diner.js"></script>
</head>
<body>
<div id="view" ng-view ng-cloak></div>

</body>
</html>