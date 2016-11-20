angular.module('services.image', ['ngResource'])

.factory('Image', ['$http', function($http){

  var Image = {};

  Image.upload = function(image){
    return $http.post('api/images/upload', image);
  }

  Image.get = function(){

  }

  return Image;
}]);