angular.module('services.diner', [
  'services.reservation',
  'services.restaurant'
])

.factory('Diner', ['Reservation', 'Restaurant', function(Reservation, Restaurant){
  var Diner = {};

  Diner.restaurant;
  Diner.reservation;
  Diner.city;

  Diner.selectedRestaurant = function(restaurant){
    if(typeof restaurant == 'undefined'){
      return Diner.restaurant;
    } else {
      Diner.restaurant = Restaurant.get({id: restaurant.id});
      return Diner.restaurant;
    }
  }

  Diner.selectedReservation = function(reservation) {
    if(typeof reservation == 'undefined'){
      return Diner.reservation;
    } else {
      Diner.reservation = reservation;
      return Diner.reservation;
    }
  }

  Diner.makeReservation = function(reservation, s_callback, err_callback) {
    Reservation.save(reservation, function(success){
      s_callback(success);
    }, function (error){
      err_callback(error);
    });
  }

  Diner.toDashCase = function(name){
    var dashCase = angular.lowercase(name);
    dashCase = dashCase.replace(/[^a-zA-Z ]/g, "");
    dashCase = dashCase.replace(/ /g, '-');
    return dashCase;
  }

  Diner.decodeDashCase = function(name){
    if(typeof name == 'undefined'){
      return "";
    }
    var dashCase = name.replace(/-/g, ' ');
    dashCase = dashCase.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
    return dashCase;
  }

  return Diner;
}]);