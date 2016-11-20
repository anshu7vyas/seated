<?php

  /* Creates a JSON string representation of a Reservation.
   *
   * Date:   18.11.2015
   * Author: Kaveh Yousefi
   */

  require_once ("authorization/LoginManager.php");
  require_once ("authorization/LoginStatus.php");
  require_once ("authorization/UserType.php");
  require_once ("model/Reservation.php");
  require_once ("model/ReservationState.php");


  class ReservationJSONfier
  {
    public function __construct ()
    {
    }


    public function createJSONString (Reservation $reservation)
    {
      $jsonResultObject = null;
      $phpResultObject  = null;

      $phpResultObject  = $this->createPHPObjectToJSONfy ($reservation);
      $jsonResultObject = json_encode ($phpResultObject);

      return $jsonResultObject;
    }

    public function createPHPObjectToJSONfy (Reservation $reservation)
    {
      $phpResultObject = null;
      $loginStatus     = null;

      if (LoginManager::isLoggedIn ())
      {
        $loginStatus = LoginManager::getStatus ();
      }
      else
      {
        $loginStatus = null;
      }

      $phpResultObject = array
      (
        //'host_id'          => $reservation->host_id,
        'party_size'       => $reservation->partySize,
        'party_name'       => $reservation->partyName,
        'date'             => $this->getReservationDate ($reservation->time),
        'time'             => $this->getReservationTime ($reservation->time),
        'reserved_via'     => $reservation->reservedVia,
        'guest_email'      => $reservation->diner->email,
        'guest_phone'      => $reservation->diner->phoneNumber,
        'diner_id'         => $reservation->diner->id,
        'restaurant_id'    => $reservation->restaurant_id,
        'table_id'         => $reservation->table_id,
        'requests'         => $reservation->requests,
        'notes'            => $reservation->notes,
        'id'               => $reservation->id,
        'state_id'         => $reservation->state_id
      );

      // Remove data forbidden for unauthorized users.
      if ($loginStatus != null)
      {
        $userType = $loginStatus->getUserType ();

        switch ($userType)
        {
          case UserType::USER_TYPE_SIMPLE :
            unset ($phpResultObject['table_id']);
            unset ($phpResultObject['notes']);
            break;
          default :
            break;
        }
      }
      else
      {
        unset ($phpResultObject['table_id']);
        unset ($phpResultObject['notes']);
      }

      return $phpResultObject;
    }


    private function getReservationDate ($reservationTimestamp)
    {
      $date   = null;
      $tokens = explode(' ', $reservationTimestamp);

      $date = $tokens[0];

      return $date;
    }

    private function getReservationTime ($reservationTimestamp)
    {
      $time   = null;
      $tokens = explode(' ', $reservationTimestamp);

      $time = $tokens[1];

      return $time;
    }
  }

?>
