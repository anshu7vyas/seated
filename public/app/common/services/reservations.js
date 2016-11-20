angular.module('services.reservation', ['ngResource'])

.factory('Reservation', ['$resource', function($resource){
  var Reservation = $resource('api/reservations/:id',
    {id:'@id'},
    {
      getDate: {method:'GET', params:{date: '@date'}, url:'api/reservations/date/:date', isArray:true}
    });

  return Reservation;
}]);
