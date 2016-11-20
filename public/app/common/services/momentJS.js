angular.module('services.momentJS', [])

.factory('Moment', function ($window) {
    if($window.moment){
      $window._thirdParty = $window._thirdParty || {};
      $window._thirdParty.moment = $window.moment;
      try { delete $window.moment; } catch (e) {$window.moment = undefined;}
    var moment = $window._thirdParty.moment;
    return moment;
  }
});