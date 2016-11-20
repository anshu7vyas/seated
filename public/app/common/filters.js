angular.module('seated.filters', [])

.filter('humanTime', function(){
  return function(time){
    var time = moment(time, 'hh:mm').format('h:mma');
    return time;
  }
})

.filter('humanDate', function(){
  return function(date, size){
    var date = moment(date)
    if(size == 'full'){
      return date.format('dddd, D MMMM YYYY');
    }
  }
})

.filter('fromSqlDate', function(){
  return function(date){
    var dateObj = new Date(Date.parse(date));
    console.log(dateObj);
    return dateObj;
  }
})

.filter('toSqlDate', function(){
  return function(date){
    var date = moment(date);
    return date.format('YYYY-MM-DD');
  }
})

.filter('toDashCase', function(){
  return function(name){
    var dashCase = angular.lowercase(name);
    dashCase = dashCase.replace(/[^a-zA-Z ]/g, "");
    dashCase = dashCase.replace(/ /g, '-');
    return dashCase;
  }
})

.filter('capitalizeWord', function(){
  return function(name){
    return name.charAt(0).toUpperCase() + name.slice(1);
  }
})

.filter('money', function(){
  return function(money_int){
    var range = ['', '$', '$$', '$$$', '$$$$'];
    return range[money_int];
  }
});


