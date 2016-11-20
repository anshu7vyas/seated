angular.module('services.search', [
  'services.restaurant'
])

.factory('Search', ['Restaurant', '$http', function(Restaurant, $http){

  var Search = {};

  Search.getCities = function(success, failure){
    $http.get('api/search/cities',{})
    .then(function(successData){
      success(successData.data);
    }, function(errorMessage){
      failure(errorMessage);
    });
  }
  Search.getRestaurants = function(params, success, failure) {
    $http.get('api/search/restaurants', {
      params: params
    })
    .then(function(successData){
      var results = successData.data;
      for (var i = results.length - 1; i >= 0; i--) {
        results[i] = mergeBlocksWithRestaurant(results[i]);
      }
      success(results);
    }, function(errorMessage){
      failure(errorMessage);
    });
  }

  Search.getReservations = function(restaurantDashCase, params, success, failure){
    $http.get('api/search/reservations/' + restaurantDashCase, {
      params: params
    })
    .then(function(successData){
      success(successData.data);
    }, function(error){
      failure(error);
    })
  }

  Search.getPopular = function(cityString, success, failure){
    $http.get('api/search/popular/' + cityString, {})
    .then(function(successData){
      success(successData.data);
    }, function(errorMessage){
      failure(errorMessage);
    });
  }

  Search.getKeywords = function(city, query){
    return $http.get('api/search/autocomplete', {
      params: {city: city, query: query}
    })
    .then(function(successData){
      return successData.data;
    })
  }

  var mergeBlocksWithRestaurant = function(blocks){
    Restaurant.get({id: blocks.restaurant_id})
    .$promise.then(function(data){
      angular.extend(blocks, data);
    });
    return blocks;
  }

  return Search;

}]);