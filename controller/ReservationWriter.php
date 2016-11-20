<?php

  require_once ("model/Reservation.php");
  require_once ('utils/DatabaseConnectionProvider.php');


  class ReservationWriter
  {
    public $reservation;


    public function __construct ()
    {
      $this->reservation = null;
    }


    public function setReservation (Reservation $reservation)
    {
      $this->reservation = $reservation;
    }


    public function persist ()
    {
      $reservationID = 0;
      $dbConnection  = null;
      $sqlStatement  = null;
      $diner         = null;
      $reservation   = null;

      $reservation = $this->reservation;
      $diner       = $reservation->diner;
      //$table       = $reservation->table;

      $dbConnection     = DatabaseConnectionProvider::createConnection ();
      $reservationState = $reservation->getStateID ();

      // Persist the RESERVATION.
      $sqlStatement = $dbConnection->prepare
      (
        "INSERT INTO Reservation (table_id,
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
                                  last_modify)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);"
      );

      $sqlStatement->bind_param("ssssssssssss",
                                $reservation->table_id,
                                $reservation->diner_id,
                                $reservationState,
                                $reservation->restaurant_id,
                                $reservation->time,
                                $reservation->partySize,
                                $reservation->partyName,
                                $reservation->reservedVia,
                                $reservation->requests,
                                $reservation->notes,
                                $reservation->holdExpires,
                                $reservation->lastModify);
      $sqlStatement->execute   ();
      $reservationID = $sqlStatement->insert_id;

      $this->reservation->id = $reservationID;

      $sqlStatement->close ();
      $dbConnection->close ();

      return $reservationID;
    }

    public function update ($id)
    {
      $hasError         = false;
      $exceptionMessage = null;
      $dbConnection     = null;
      $sqlStatement     = null;
      $diner            = null;
      $reservation      = null;

      $reservation = $this->reservation;
      $diner       = $reservation->diner;

      $dbConnection     = DatabaseConnectionProvider::createConnection ();
      $reservationState = ($reservation->state != null) ? $reservation->state->id : 1;
      $requestedVia     = "1";
      $sqlStatement     = $dbConnection->prepare
      (
        "UPDATE Reservation
         SET    table_id         = ?,
                diner_id         = ?,
                party_size       = ?,
                party_name       = ?,
                state_id         = ?,
                restaurant_id    = ?,
                time             = ?,
                reserved_via     = ?,
                special_requests = ?,
                restaurant_notes = ?
        WHERE   id = ?;"
      );

      $sqlStatement->bind_param("sssssssssss",
                                $reservation->table_id,
                                $reservation->diner_id,
                                $reservation->partySize,
                                $reservation->partyName,
                                $reservationState,
                                $reservation->restaurant_id,
                                $reservation->time,
                                $requestedVia,
                                $reservation->requests,
                                $reservation->notes,
                                $id);
      $sqlStatement->execute ();

      if ($sqlStatement)
      {
        $hasError         = ($sqlStatement->errno != 0);
        $exceptionMessage = $sqlStatement->error;
        $sqlStatement->close ();
      }
      else
      {
        $hasError         = true;
        $exceptionMessage = "Reservation could not be updated.";
      }

      $dbConnection->close ();

      if ($hasError)
      {
        throw new Exception ($exceptionMessage);
      }

      return $id;
    }
  }
?>