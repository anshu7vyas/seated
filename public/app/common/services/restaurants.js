angular.module('services.restaurant', ['ngResource'])

.factory('Restaurant', ['$resource', '$http', function($resource){
  var Restaurant = $resource('api/restaurants/:id',
    {id:'@id'}
  );

  Restaurant.signup = function(form){
    $http.post('api/restaurants/signup', form)
    .then(function(success){
      return success;
    }, function(error){
      return error;
    });
  }

  return Restaurant;
}]);