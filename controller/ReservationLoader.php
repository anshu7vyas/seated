<?php
  
  require_once ("controller/DinerLoader.php");
  require_once ("controller/HostLoader.php");
  require_once ("model/Reservation.php");
  require_once ("ReservationStateLoader.php");
  require_once ("RestaurantTableLoader.php");
  
  
  class ReservationLoader
  {
    private $dinerLoader;
    private $tableLoader;
    private $stateLoader;
    private $hostLoader;
    private $restaurantLoader;
    
    
    public function __construct ()
    {
      $this->dinerLoader      = new DinerLoader            ();
      $this->tableLoader      = new RestaurantTableLoader  ();
      $this->stateLoader      = new ReservationStateLoader ();
      $this->hostLoader       = new HostLoader             ();
      $this->restaurantLoader = new RestaurantLoader       ();
    }
    
    
    public function getReservations ()
    {
      $reservations = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                table_id,
                diner_id,
                state_id,
                restaurant_id,
                time,
                party_size,
                party_name,
                reserved_via,
                special_requests,
                restaurant_notes,
                hold_expires,
                created_on,
                last_modify
         FROM   Reservation;
        "
      );
      
      $reservations = $this->createReservationsFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $reservations;
    }
    
    public function getReservationByID ($reservationID)
    {
      $reservation  = null;
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                table_id,
                diner_id,
                state_id,
                restaurant_id,
                time,
                party_size,
                party_name,
                reserved_via,
                special_requests,
                restaurant_notes,
                hold_expires,
                created_on,
                last_modify
         FROM   Reservation
         WHERE  id = ?;
        "
      );
      
      $sqlStatement->bind_param ("s", $reservationID);
      
      $reservations = $this->createReservationsFromSqlStatement ($sqlStatement);
      
      if (empty ($reservations))
      {
        $reservation = null;
      }
      else
      {
        $reservation = $reservations[0];
      }
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $reservation;
    }
    
    public function getReservationsByHostID ($hostID)
    {
      $reservations = array ();
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                table_id,
                diner_id,
                state_id,
                restaurant_id,
                time,
                party_size,
                party_name,
                reserved_via,
                special_requests,
                restaurant_notes,
                hold_expires,
                created_on,
                last_modify
         FROM   Reservation
         WHERE  host_id = ?;
        "
      );
      
      $sqlStatement->bind_param ("s", $hostID);
      
      $reservations = $this->createReservationsFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $reservations;
    }
    
    public function getReservationByRestaurantID ($restaurantID)
    {
      $reservations = null;
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                table_id,
                diner_id,
                state_id,
                restaurant_id,
                time,
                party_size,
                party_name,
                reserved_via,
                special_requests,
                restaurant_notes,
                hold_expires,
                created_on,
                last_modify
         FROM   Reservation
         WHERE  restaurant_id = ?;
        "
      );
      
      $sqlStatement->bind_param ("s", $restaurantID);
      
      $reservations = $this->createReservationsFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $reservations;
    }
    
    public function getReservationByTableID ($tableID)
    {
      $reservations = null;
      $dbConnection = null;
      $sqlStatement = null;
      
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                table_id,
                diner_id,
                state_id,
                restaurant_id,
                time,
                party_size,
                party_name,
                reserved_via,
                special_requests,
                restaurant_notes,
                hold_expires,
                created_on,
                last_modify
         FROM   Reservation
         WHERE  table_id = ?;
        "
      );
      
      $sqlStatement->bind_param ("s", $tableID);
      
      $reservations = $this->createReservationsFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $reservations;
    }
    
    // Comparing dates: -> "http://stackoverflow.com/questions/10483123/comparing-timestamp-dates-in-mysql-with-date-only-parameter"
    public function getReservationsByDate (DateTime $dateTime)
    {
      $reservations = null;
      $dbConnection = null;
      $sqlStatement = null;
      $dateString   = null;
      
      $dateString   = $dateTime->format ("Y-m-d");
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                table_id,
                diner_id,
                state_id,
                restaurant_id,
                time,
                party_size,
                party_name,
                reserved_via,
                special_requests,
                restaurant_notes,
                hold_expires,
                created_on,
                last_modify
         FROM   Reservation
         WHERE  DATE(time) = ?;
        "
      );
      
      $sqlStatement->bind_param ("s", $dateString);
      
      $reservations = $this->createReservationsFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $reservations;
    }
    
    public function getReservationsByRestaurantIDAndDate
    (
      $restaurantID,
      DateTime $dateTime
    )
    {
      $reservations = null;
      $dbConnection = null;
      $sqlStatement = null;
      $dateString   = nulL;
      
      $dateString   = $dateTime->format ("Y-m-d");
      $dbConnection = DatabaseConnectionProvider::createConnection ();
      $sqlStatement = $dbConnection->prepare
      (
        "SELECT id,
                table_id,
                diner_id,
                state_id,
                restaurant_id,
                time,
                party_size,
                party_name,
                reserved_via,
                special_requests,
                restaurant_notes,
                hold_expires,
                created_on,
                last_modify
         FROM   Reservation
         WHERE  DATE(time)    = ?
                AND
                restaurant_id = ?;
        "
      );
      
      $sqlStatement->bind_param ("ss", $dateString, $restaurantID);
      
      $reservations = $this->createReservationsFromSqlStatement ($sqlStatement);
      
      $sqlStatement->close ();
      $dbConnection->close ();
      
      return $reservations;
    }
    
    
    private function createReservationsFromSqlStatement ($sqlStatement)
    {
      $reservations = array ();
      
      if ($sqlStatement->execute ())
      {
        $sqlStatement->bind_result
        (
          $id,
          $tableID,
          $dinerID,
          $stateID,
          $restaurantID,
          $time,
          $partySize,
          $partyName,
          $reservedVia,
          $specialRequests,
          $restaurantNotes,
          $holdExpires,
          $createdOn,
          $lastModify
        );
        
        while ($row = $sqlStatement->fetch ())
        {
          $reservation = new Reservation ();
          $reservation->id          = $id;
          $reservation->table       = $this->tableLoader->getTableByID ($tableID);
          $reservation->table_id    = $tableID;
          //$reservation->host        = $this->hostLoader->getHostByID   ($hostID);
          $reservation->diner       = $this->dinerLoader->getDinerByID ($dinerID);
          $reservation->diner_id    = $dinerID;
          $reservation->state       = $this->stateLoader->getStateByID ($stateID);
          $reservation->state_id    = $stateID;
          $reservation->restaurant  = $this->restaurantLoader->getRestaurantByID ($restaurantID);
          $reservation->restaurant_id = $restaurantID;
          $reservation->partySize   = $partySize;
          $reservation->partyName   = $partyName;
          $reservation->time        = $time;
          $reservation->reservedVia = $reservedVia;
          $reservation->requests    = $specialRequests;
          $reservation->notes       = $restaurantNotes;
          $reservation->holdExpires = $holdExpires;
          $reservation->createdOn   = $createdOn;
          $reservation->lastModify  = $lastModify;
          
          $reservations[] = $reservation;
        }
      }
      
      return $reservations;
    }
  }

?>
