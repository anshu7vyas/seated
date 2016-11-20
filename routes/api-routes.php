<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

require_once ('utils/HelperFunctions.php');
require_once ('controller/apiRouteFunctions.php');
$app->post ('/api/reservations/',            'createReservation');
$app->post ('/api/reservations/:id',         'updateReservation');
$app->get  ('/api/reservations/:id',         'getReservationById');
$app->get  ('/api/reservations/date/:date', 'getReservationsByDate');

$app->get('/api/search/restaurants/', 'searchForRestaurant');
$app->get('/api/search/autocomplete', 'searchForKeyword');
$app->get('/api/search/popular/:city_name', 'searchForPopularityAndCity');
$app->get('/api/search/cities', 'searchForCities');
$app->get('/api/search/reservations/:restaurant', 'searchForReservationsOfRestaurant');

$app->post ('/api/restaurants/signup', 'createRestaurant');
$app->get  ('/api/restaurants/:id',    'getRestaurantByID');
$app->get  ('/api/restaurants/:name',  'getRestaurantByName');
$app->post ('/api/restaurants/edit',   'updateRestaurant');

$app->get('/api/restaurants/:id/images', 'getAllImagesByRestaurant');

$app->post ('/api/restaurants/:id/hosts', 'createHost');
$app->get  ('/api/restaurants/:id/hosts', 'getHostByID');
$app->post ('/api/restaurants/:restaurant_id/hosts/:host_id', 'updateHost');

$app->post ('/api/rating/:id', 'createRatingForRestaurant');
$app->get  ('/api/rating/:id', 'getAllRatingsForRestaurant');

// Test route by Kaveh Yousefi. Can be removed at any time.
$app->post ('/api/host/login',   'loginAsHost');
$app->get  ('/api/host/logout',  'logoutAsHost');
$app->post ('/api/admin/login',  'loginAsAdmin');
$app->get  ('/api/admin/logout', 'logoutAsAdmin');

$app->post ('/api/host/registration/', 'registerAsHost');

$app->post ('/api/images/upload/',  'uploadImage');
$app->post ('/api/images/update',   'updateImages');

?>

