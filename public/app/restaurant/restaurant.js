angular.module('restaurantApp', [
  'ngRoute',
  'ngResource',
  'services.reservation',
  'services.image',
  'services.foh',
  'services.momentJS',
  'ui.bootstrap',
  'naif.base64',
  'seated.filters'
])

.config(['$routeProvider', function($routeProvider){
  $routeProvider
  .when('/', {
    templateUrl: 'public/app/restaurant/splash.tpl.html',
    controller : 'restSplashController'
  })
  .when('/host', {
    templateUrl: 'public/app/restaurant/host.tpl.html',
    controller: 'hostController'
  })
  .when('/admin', {
    templateUrl: 'public/app/restaurant/admin.tpl.html',
    controller: 'adminController'
  })
  .when('/login', {
    templateUrl: 'public/app/restaurant/login.tpl.html',
    controller: 'loginController'
  })
  .otherwise({redirectTo: '/login'});
}])

.run(['FOH', function(FOH){
  FOH.loadCurrentUser();
}])

.filter('activeReservations', [function() {
  return function (reservations) {
    if(reservations){
      var activeReservations = [];
      for (var i = reservations.length - 1; i >= 0; i--) {
        if(!reservations[i].canceled && !reservations[i].seated) {
          activeReservations.push(reservations[i]);
        }
      }
      return activeReservations;
    }
  }
}])

.directive('convertToNumber', function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attrs, ngModel) {
      ngModel.$parsers.push(function(val) {
        return parseInt(val, 10);
      });
      ngModel.$formatters.push(function(val) {
        return '' + val;
      });
    }
  };
})

.controller('headerController', ['FOH', '$scope', '$location', function(FOH, $scope, $location){
  $scope.current_user = FOH.current_user;
  $scope.login = function(){
    $location.path('login');
  }
  $scope.logout = function(){
    FOH.logout(function(){
      FOH.current_user = false;
      $location.url('restaurants');
    }, function(){
      alert("sorry, please try logging out again");
    });
  }

  $scope.route = function(route){
    $location.path(route);
  }
}])

.controller('restSplashController', ['FOH', '$scope', '$location', '$anchorScroll', function(FOH, $scope, $location, $anchorScroll){

  $scope.scrollToForm = function(){
    $anchorScroll('signupForm');
  }

  $scope.form = {
    state: 'CA'
  }

  $scope.register = function(){
    FOH.register($scope.form)
    .then(function(data){
      FOH.login($scope.form.email, $scope.form.password, function(user){
        $location.path('admin');
      }, function(){
        alert("something went wrong!");
      })
    }, function(error){
      console.log(error);
    })
  }
}])

.controller('hostController', ['FOH', '$scope', '$interval', '$filter', function(FOH, $scope, $interval, $filter){

  FOH.loadCurrentRestaurant()
  .$promise.then(function(restaurant){
    $scope.restaurant = restaurant;
  });

  $scope.date = {
    selected: new Date(),
    today: function(){
      this.selected = new Date();
    }
  }
  $scope.$watch('date.selected', function(oldDate, newDate){
    updateReservations();
  });

  var defaultReservation = JSON.stringify({
    date: moment(new Date()).format('YYYY-MM-DD'),
    party_size: '2',
    requests: '',
    time: '17:00:00'
  });
  $scope.newReservation = JSON.parse(defaultReservation);

  $scope.selectedReservation = $scope.newReservation;
  $scope.editable = true;
  $scope.toggleEdit = function(){
    $scope.editable = !$scope.editable;
  }

  function updateReservations(){
    FOH.getReservations($scope.date.selected)
    .$promise.then(function(data){
      $scope.reservations = data;
    });
  }
  updateReservations();
  var updating = $interval(updateReservations, 5000);
  $scope.$on('$destroy',function(){
    $interval.cancel(updating);
  });

  $scope.select = function(reservation){
    if(reservation === $scope.newReservation){
      $scope.editable = true;
    } else {
      $scope.editable = false;
    }
    $scope.selectedReservation = reservation;
  }

  $scope.seat = function(reservation){
    FOH.seat(reservation);
  }

  $scope.cancel = function(reservation){
    FOH.cancel(reservation);
    $scope.select($scope.newReservation);
  }

  $scope.save = function(reservation){
    if(reservation == $scope.newReservation){
      FOH.create(reservation);
      updateReservations();
      $scope.newReservation = JSON.parse(defaultReservation);
      $scope.select($scope.newReservation);
    } else {
      FOH.save(reservation);
    }
  }

}])

.controller('adminController', ['FOH', 'Image', '$scope', function(FOH, Image, $scope){

  FOH.loadCurrentRestaurant()
  .$promise.then(function(restaurant){
    $scope.restaurant = restaurant;
    console.log($scope.restaurant);
  });

  $scope.newTable = {seats: 2, name: "32"};

  $scope.addTable = function(){
    $scope.restaurant.tables.push($scope.newTable);
    $scope.newTable = {seats: 2, name: "32"};
  }

  $scope.uploadImage = function(){
    console.log($scope.image.upload)
    Image.upload($scope.image.upload)
    .then(function(data){
      console.log(data);
    })
  }

  $scope.save = function(){
    $scope.restaurant.$save();
  }
}])

.controller('dateSelectionController', ['$scope', function($scope){

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
}])

.controller('loginController', ['FOH', '$scope', '$location', function(FOH, $scope, $location){
  $scope.login = function(){
    FOH.login($scope.user.username, $scope.user.password, function(user){
      console.log(FOH.current_user);
      if(FOH.current_user.type == 1){
        $location.path('admin');
      }
      if(FOH.current_user.type == 2){
        $location.path('host');
      }
    }, function(error){
      alert(error);
    })
  }
}]);
