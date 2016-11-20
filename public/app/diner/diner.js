angular.module('dinerApp', [
  'ngRoute',
  'ngResource',
  'services.reservation',
  'services.restaurant',
  'services.search',
  'services.diner',
  'seated.filters',
  'ui.bootstrap',
  'uiGmapgoogle-maps',
])

.config(['$routeProvider', function($routeProvider){
  // seated.com/
  $routeProvider.when('/', {
    templateUrl: 'public/app/diner/splash.tpl.html',
    controller : 'splashController'
  })
  // seated.com/#/search?params=...
  .when('/search', {
    templateUrl: 'public/app/diner/search.tpl.html',
    controller : 'searchController',
    reloadOnSearch: false
  })
  // seated.com/#/restaurant/foreign-cinema
  .when('/restaurant/:restaurant', {
    templateUrl: 'public/app/diner/restaurant-view.tpl.html',
    controller: 'restaurantViewContoller',
    reloadOnSearch: false
  })
  .when('/confirm', {
    templateUrl: 'public/app/diner/confirm.tpl.html',
    controller: 'confirmController'
  })
  // seated.com/#/san-francisco
  .when('/:city', {
    templateUrl: 'public/app/diner/city.tpl.html',
    controller: 'cityController'
  });
}])

.controller('splashController', ['Search', 'Diner', '$rootScope', '$scope', '$location', function(Search, Diner, $rootScope, $scope, $location){
  $rootScope.pageTitle = 'Welcome to Seated';
  $rootScope.city = false;
  $scope.cities = [];
  $scope.citySelection = "";
  Search.getCities(function(data){
    $scope.cities = data;
    for (var i = $scope.cities.length - 1; i >= 0; i--) {
      if($scope.cities[i].city){
        $scope.cities[i].img_path = "public/images/" + $scope.cities[i].city.replace(/(\w)\w*\W*/g, function (_, i) {return i.toLowerCase();}) + ".jpg";
      }
    }
  }, function(error){
    console.log(error);
  });
  $scope.selectCity = function(city){
    Diner.city = Diner.toDashCase(city);
    $location.path(Diner.city);
  }
}])

.controller('cityController', ['Restaurant', 'Diner', 'Search', '$rootScope', '$scope', '$routeParams', '$location', '$filter', function(Restaurant, Diner, Search, $rootScope, $scope, $routeParams, $location, $filter){
  $rootScope.pageTitle = 'Search for Tables in Your City';
  if(!Diner.city){
    Diner.city = $routeParams.city
  }
  $rootScope.city = Diner.decodeDashCase(Diner.city);
  Search.getPopular(Diner.city, function(data){
    $scope.popular = data;
  }, function(error){
    console.log(error);
  });
  $scope.searchParams = {
    'party_size' : '2',
    'date' : new Date(),
    'time' : '19:00:00',
    'city' : Diner.city
  }

  $scope.search = {};


  $scope.selectRestaurant = function(restaurant){
    Diner.selectedRestaurant(restaurant);
    $scope.searchParams.date = moment($scope.searchParams.date).format('YYYY-MM-DD');
    $location.search($scope.searchParams).path('restaurant/' + Diner.toDashCase(restaurant.name));
  }
  $scope.searchText = true;
  $scope.search = function(){
    $scope.searchParams.date = moment($scope.searchParams.date).format('YYYY-MM-DD');
    if($scope.search.searchQuery.type == 'category'){
      $scope.searchParams.cuisine = $scope.search.searchQuery.value;
      $location.path('/search/').search($scope.searchParams);
    }
    if($scope.search.searchQuery.type == 'restaurant'){
      $location.path("/restaurant/" + Diner.toDashCase($scope.search.searchQuery.value)).search($scope.searchParams);
    }
    else {
    $location.path('/search/').search($scope.searchParams);
    }
  }
}])

.controller('searchController', ['Diner', 'Search', '$rootScope', '$scope', '$location', '$routeParams', function(Diner, Search, $rootScope, $scope, $location, $routeParams){
  $rootScope.pageTitle = 'Reservations Available for Your Search';
  if(!Diner.city){
    Diner.city = $routeParams.city
  }
  $rootScope.city = Diner.decodeDashCase(Diner.city);
  $scope.searchParams = $location.search();
  $scope.searchText = true;
  $scope.searchResults = [];
  updateSearch = function(){
    $location.search($scope.searchParams);
    Search.getRestaurants($scope.searchParams, function(restaurants){
      $scope.searchResults = restaurants;
    }, function(error){
      console.log(error);
    });
  }
  updateSearch();

  $scope.search = function(){
    console.log($scope.searchResults);
    $scope.searchParams.date = moment($scope.searchParams.date).format('YYYY-MM-DD');
    if($scope.search.searchQuery.type == 'category'){
      $scope.searchParams.cuisine = $scope.search.searchQuery.value;
      updateSearch();
    }
    if($scope.search.searchQuery.type == 'restaurant'){
      $location.path("/restaurant/" + Diner.toDashCase($scope.search.searchQuery.value)).search($scope.searchParams);
    }
    else {
      updateSearch();
    }
  }

  $scope.selectRestaurant = function(restaurant){
    Diner.selectedRestaurant(restaurant);
    $scope.searchParams.date = moment($scope.searchParams.date).format('YYYY-MM-DD');
    $location.search($scope.searchParams).path('restaurant/' + Diner.toDashCase(restaurant.name));
  }

  $scope.selectReservation = function(restaurant, block) {
    Diner.selectedRestaurant(restaurant)
    .$promise.then(function(){
      $scope.searchParams.date = moment($scope.searchParams.date).format('YYYY-MM-DD');
      Diner.selectedReservation({restaurant_id: Diner.selectedRestaurant().id, time: block.time, date: block.date, party_size: $scope.searchParams.party_size});
      console.log(Diner.selectedReservation());
      $location.search({}).path('confirm');
    });
  }

}])

.controller('searchBarController', ['Search', 'Diner', '$scope', function(Search, Diner, $scope){

  $scope.minDate = new Date();

  $scope.open = function($event) {
    $scope.status.opened = true;
  };

  $scope.options = {
    formatYear: 'yy',
    startingDay: 1,
    showWeeks : 'false'
  };

  $scope.format = 'MMM dd, yyyy';

  $scope.status = {
    opened: false
  };

  $scope.getKeywords = function(query){
    return Search.getKeywords(Diner.city, query);
  }
}])

.controller('typeAheadController', ['Search', '$scope', '$location', function(Search, $scope, $location) {
  Search.getCities(function(data){
    $scope.cities = data;
  }, function(error){
    console.log(error);
  });
  $scope.citySelection = "";

  $scope.selectCity = function(city){
    $location.path(angular.lowercase($scope.citySelection.city).replace(' ', '-'));
  }
}])

.controller('restaurantViewContoller', ['Diner', 'Search', '$rootScope', '$scope', '$location', '$routeParams', function(Diner, Search, $rootScope, $scope, $location, $routeParams){
  Diner.selectedRestaurant({id: $routeParams.restaurant});
  $scope.restaurant = Diner.selectedRestaurant();
  $scope.restaurant.$promise.then(function(){
    loadMap();
    $rootScope.pageTitle = $scope.restaurant.name;
  })
  $rootScope.city = Diner.decodeDashCase(Diner.city);
  $scope.searchParams = ($location.search().date ? $location.search() :
    {
      'party_size' : '2',
      'date' : new Date(),
      'time' : '19:00:00',
    });

  var updateSearch = function(){
    $scope.searchParams.date = moment($scope.searchParams.date).format('YYYY-MM-DD');
    $location.search($scope.searchParams);
    Search.getReservations($routeParams.restaurant, $scope.searchParams, function(data){
      $scope.blocks = data;
    }, function(error){
      console.log(error);
    });
  }
  updateSearch();

  $scope.search = function(){
    updateSearch();
  }

  $scope.selectReservation = function(block) {
    Diner.selectedReservation({restaurant_id: Diner.selectedRestaurant().id, time: block.time, date: block.date, party_size: $routeParams.party_size});
    $location.search({}).path('confirm');
  }

  var loadMap = function(){
    $scope.map = {};
    $scope.map.map = { center: { latitude: $scope.restaurant.location.latitude, longitude: $scope.restaurant.location.longitude }, zoom: 15 };
    $scope.map.marker = {
      id: 0,
      coords: {
        latitude: $scope.restaurant.location.latitude,
        longitude: $scope.restaurant.location.longitude
      },
      options: { draggable: false },
    }
    $scope.map.options = {scrollwheel: false, draggable: false, mapTypeControl: false, streetViewControl: false, zoomControl: false};
  }


}])

.controller('confirmController', ['Diner', '$rootScope', '$scope', '$location', function(Diner, $rootScope, $scope, $location){
  $rootScope.pageTitle = "Confirm Your Reservation";
  $rootScope.city = Diner.decodeDashCase(Diner.city);
  if(!Diner.selectedReservation()){
    $location.url('');
  }
  $scope.reservation = Diner.selectedReservation();
  $scope.restaurant = Diner.selectedRestaurant();
  $scope.confirmed = false;

  $scope.reserve = function(){
    $scope.reservation.reserved_via = 1;
    Diner.makeReservation($scope.reservation, function(success){
      $scope.confirmed = true;
    }, function(error){
      console.log("your reservation could not be made because " + error);
    });
  }


}]);