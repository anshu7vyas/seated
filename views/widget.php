<!DOCTYPE html>
<html>
<head>
  <?php require 'base.html' ?>
  <title>Seater by Seated</title>
  <link rel="stylesheet" type="text/css" href="public/vendor/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="public/vendor/normalize.css">
  <link rel="stylesheet" type="text/css" href="public/css/general.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.4.0/css/pikaday.css">
  <script type="text/javascript" src="public/vendor/moment.min.js"></script>
  <script type="text/javascript" src="public/vendor/angular.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.4.0/pikaday.js"></script>
  <style type="text/css">

    body{
      width: 100%;
      height: 100%;
    }

    #main-container{
      position: absolute;
      width: 100%;
      height: 1600px;
      text-align: center;
      background-color: #FFF8F8;
      transition: all 0.8s ease;
    }

    #main-container.search{
      top: 0px;
    }

    #main-container.select{
      top: -400px;
    }

    #main-container.confirm{
      top: -800px;
    }

    #main-container.done{
      top: -1200px;
    }

    #search-view{
      height: 400px;
      width: 100%;
    }

    .search-params{
      display: block;
      margin: 5% 0;
      padding: 5%;
      text-align: center;
      background-color: #CFC9C9;
      color: #547AAD;
      border-radius: 0;
    }
    .search-params select{
      margin: 2%;
    }
    ul{
      margin: 0;
      padding: 0;
    }

    h3{
      margin: 0;
      padding: 6% 3% 3% 3%;
    }

    #select-view{
      height: 400px;
      width: 100%;
    }

    #confirm-view{
      height: 400px;
      width: 100%;
    }

    #confirm-view .button{
      margin: 0 2%;
    }

    #confirm-view div{
      margin: 1% 0;
    }

    #confirm-view input{
      text-align: center;
      background-color: #CFC9C9;
      color: #547AAD;
      border-radius: 0;
      margin: 1% 0;
      width: 100%;
    }

    #confirm-view textarea{
      width: 100%;
      margin: 0;
      background-color: #CFC9C9;
    }

    .marketing{
      margin: 2%;
      display: inline-block;
    }

    #confirm-view{
      height: 400px;
      width: 100%;
    }


  </style>
</head>
<body>
  <div id="main-container" ng-class="view" ng-app="widgetApp" ng-controller="widgetContoller">
    <div id="search-view">
      <h3>Schedule a Reservation</h3>
      <div class="search-params">
        <span>Party Size:</span>
        <select ng-model="search.party_size">
          <option ng-selected="{{size.value == search.party_size}}" ng-repeat="size in options.size" value="{{size.value}}">{{size.display}}</option>
        </select>
      </div>

      <div id="date" class="search-params">
        <input class="input-values" id="datepicker" placeholder="Select Date"></input>
      </div>

      <div class="search-params">
        <span>Time:</span>
        <select ng-model="search.time">
          <option ng-repeat="time in options.time" value="{{time.value}}">{{time.display}}</option>
        </select>
      </div>
      <p class="marketing">powered by <a href="//sfsuswe.com/~f15g08" target="_blank">seated</a></p>
      <a href ng-click="searchBlocks()"><div class="button">Search</div></a>

    </div>
    <div id="select-view">
      <h3>Select a Reservation</h3>
      <span>{{blocks.length}} reservations available for {{search.party_size}} around {{search.time | humanTime}} on {{search.date | humanDate}}:</span>
      <ul>
        <a href>
          <li class="button search-params" ng-repeat="block in blocks | limitTo: 3" ng-click="selectReservation(block)">
            {{block.time | humanTime}}
          </li>
        </a>
      </ul>
      <a href ng-click="view = 'search'"><div class="button">Back</div></a>
    </div>
    <div id="confirm-view">
      <h3>Confirm your booking!</h3>
      <div>
        <span>For {{selectedReservation.party_size}} people</span>
        <span>on {{selectedReservation.date | humanDate}}</span>
        <span>at {{selectedReservation.time | humanTime}}</span>
      </div>
      <div id="name">
        <input ng-model="selectedReservation.party_name" type="text" id="party-name" placeholder="Party Name">
      </div>
      <div id="phone">
        <input ng-model="selectedReservation.guest_phone" type="text" id="phone-number" placeholder="Phone Number">
      </div>
      <div id="email">
        <input ng-model="selectedReservation.guest_email" type="text" id="email" placeholder="Email">
      </div>
      <div id="special-requests">
        <textarea ng-model="selectedReservation.requests" id="special-requests" rows="2" placeholder="Any special requests (optional)"></textarea>
      </div>
      <a href ng-click="view = 'select'"><div class="button">Back</div></a>
      <a href ng-click="confirm()"><div class="button">Confirm</div></a>
      <p class="marketing">powered by <a href="//sfsuswe.com/f15g08">seated</a></p>
    </div>
    <div id="done-view">
        <h2>Confirmed!</h2>
        <div class="search-params">{{selectedReservation.party_name}}</div>
        <div class="search-params">Party of {{selectedReservation.party_size}}</div>
        <div class="search-params">{{selectedReservation.date | humanDate}}</div>
        <div class="search-params">{{selectedReservation.time | humanTime}}</div>
        <p class="marketing">powered by <a href="//sfsuswe.com/f15g08">seated</a></p>
    </div>
  </div>
</body>
<script type="text/javascript">

  angular.module('widgetApp', [])

  .filter('humanTime', function(){
    return function(time){
      var time = moment(time, 'hh:mm').format('h:mma');
      return time;
    }
  })
  .filter('humanDate', function(){
    return function(date){
      var date = moment(date)
      return date.format('D MMM YYYY');
    }
  })
  .controller('widgetContoller', ['$http', '$scope', function($http, $scope){
    var restaurant = {};
    restaurant.name = "<?php echo $_GET["restaurant"]; ?>";
    restaurant.id = "<?php echo $_GET["id"]; ?>";

    $scope.view = 'search';

    $scope.options = {};
    $scope.options.size = [
      {value: '1', display: '1 Person'},
      {value: '2', display: '2 People'},
      {value: '3', display: '3 People'},
      {value: '4', display: '4 People'},
      {value: '5', display: '5 People'},
      {value: '6', display: '6 People'},
      {value: '7', display: '7 People'},
      {value: '8', display: '8 People'}
    ];
    $scope.options.time = [
      {value: '17:00:00', display: '5:00pm'},
      {value: '17:30:00', display: '5:30pm'},
      {value: '18:00:00', display: '6:00pm'},
      {value: '18:30:00', display: '6:30pm'},
      {value: '19:00:00', display: '7:00pm'},
      {value: '19:30:00', display: '7:30pm'},
      {value: '20:00:00', display: '8:00pm'},
      {value: '20:30:00', display: '8:30pm'},
      {value: '21:00:00', display: '9:00pm'},
      {value: '21:30:00', display: '9:30pm'},
      {value: '22:00:00', display: '10:00pm'},
      {value: '22:30:00', display: '10:30pm'},
      {value: '23:00:00', display: '11:00pm'},
      {value: '23:30:00', display: '11:30pm'}
    ]

    var date = new Pikaday({
      field: document.getElementById('datepicker'),
      format: 'D MMM YYYY',
      minDate: moment().toDate()
    });

    date.setDate(moment().format("YYYY-MM-DD"));

    var today = moment();
    $scope.search = {
      party_size: $scope.options.size[1].value,
      time: "19:00:00"
    };

    $scope.searchBlocks = function(){
      $scope.search.date = date.toString('YYYY-MM-DD');
      $http.get('api/search/reservations/' + restaurant.name, {
        params: $scope.search
      })
      .then(function(successData){
        $scope.blocks = successData.data;
        console.log($scope.blocks);
        $scope.view = 'select';
      }, function(error){
        failure(error);
      })
    }

    $scope.selectedReservation = {};

    $scope.selectReservation = function(block){
      $scope.view = 'confirm';
      $scope.selectedReservation.date = block.date;
      $scope.selectedReservation.time = block.time;
      $scope.selectedReservation.party_size = $scope.search.party_size;
      $scope.selectedReservation.restaurant_id = restaurant.id;
      $scope.selectedReservation.reserved_via = 3;
      console.log($scope.selectedReservation);
    }

    $scope.confirm = function(){
      $scope.view = 'done';
      // $http.post('api/reservations', $scope.selectedReservation)
      // .then(function(data){
      //   console.log(data.data);
      //   if(data.data.id){
      //     $scope.view = 'done';
      //   } else{
      //     alert("There may have been an error");
      //   }
      // });
    }

  }]);

</script>
</html>