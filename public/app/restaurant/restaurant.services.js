angular.module('services.foh', [
  'ngCookies',
  'services.restaurant',
  'services.reservation',
  'seated.filters'
])

.factory('FOH', ['Restaurant', 'Reservation', '$http', '$cookies', '$filter', function(Restaurant, Reservation, $http, $cookies, $filter){
  var FOH = {};
  FOH.current_user = false;

  FOH.loadCurrentUser = function(){
    if($cookies.get('seateduser')){
      FOH.current_user = $cookies.getObject('seateduser').attributes;
      FOH.current_user.type = $cookies.getObject('seateduser').userType;
    }
  }

  FOH.loadCurrentRestaurant = function(){
    return Restaurant.get({id: this.current_user.restaurant_id});
  }

  FOH.login = function(user, pass, success, failure){
    var re = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    if (re.test(user)){
      $http.post('api/admin/login', {email: user, password: pass})
      .then(function(user){
        FOH.loadCurrentUser();
        success(user.data);
      }, function(){
        failure(error);
      });
    } else {
      $http.post('api/host/login', {username: user, password: pass})
      .then(function(user){
        FOH.loadCurrentUser();
        success(user.data);
      }, function(error){
        failure(error);
      });
    }
  }

  FOH.logout = function(success, failure){
    if(FOH.current_user.name){
      $http.get('api/admin/logout')
      .then(success, failure);
    } else {
      $http.get('api/host/logout')
      .then(success, failure);
    }
  }

  FOH.register = function(form){
    return $http.post('api/restaurants/signup', form);
  }

  FOH.getReservations = function(date){
    date = $filter('toSqlDate')(date);
    return Reservation.getDate({date: date});
  }

  FOH.cancel = function(res){
    res.date = $filter('toSqlDate')(res.date);
    res.state_id = 3;
    res.$save();
  }

  FOH.save = function(res) {
    res.date = $filter('toSqlDate')(res.date);
    res.$save()
  }

  FOH.create = function(res) {
    res.date = $filter('toSqlDate')(res.date);
    res.restaurant_id = FOH.current_user.restaurant_id;
    res.reserved_via = 2;
    res = new Reservation(res);
    return res.$save();
  }

  FOH.seat = function(res){
    res.date = $filter('toSqlDate')(res.date);
    res.state_id = 4;
    res.$save();
  }

  return FOH;



}]);