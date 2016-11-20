<?php

  /* Encapsulates the reservation creation process from a JSON input
   * object.
   *
   * Date:   03.12.2015
   * Author: Kaveh Yousefi
   */

  require_once ('authorization/LoginManager.php');
  require_once ('authorization/LoginStatus.php');
  require_once ('authorization/UserType.php');
  require_once ('controller/DinerLoader.php');
  require_once ('controller/DinerWriter.php');
  require_once ('controller/HostLoader.php');
  require_once ('controller/ReservationLoader.php');
  require_once ('controller/ReservationStateLoader.php');
  require_once ('controller/RestaurantTableLoader.php');
  require_once ('controller/ReservationWriter.php');
  require_once ('model/Diner.php');
  require_once ('model/Host.php');
  require_once ('model/Reservation.php');
  require_once ('model/ReservationState.php');
  require_once ('model/RestaurantTable.php');
  require_once ('utils/ObjectInspector.php');


  class ReservationCreationProcessor
  {
    private $request;


    public function __construct ()
    {
      $this->request = null;
    }


    public function setRequest ($request)
    {
      $this->request = $request;
    }


    public function process ()
    {
      $reservation            = new Reservation            ();
      $reservationWriter      = new ReservationWriter      ();
      $reservationStateLoader = new ReservationStateLoader ();
      $reservationState       = $reservationStateLoader->getStateByID (ReservationState::RESERVED);
      $request                = $this->request;
      $dinerLoader            = null;
      $diner                  = null;
      $dinerID                = 0;
      $dateAndTime            = null;
      $userType               = null;

      if (! property_exists ($request, "guest_email"))
      {
        throw new Exception ("Need e-mail of guest.");
      }

      $guestEmail  = $request->guest_email;

      $userType    = $this->getUserType            ();
      $dinerLoader = new DinerLoader               ();
      $diner       = $dinerLoader->getDinerByEmail ($guestEmail);

      if ($diner == null)
      {
        $dinerWriter = new DinerWriter ();
        $diner       = new Diner       ();

        $diner->firstName    = '';
        $diner->lastName     = '';
        $diner->mobileNumber = null;
        $this->setGuestEmail ($diner, $request, $userType);
        $this->setGuestPhone ($diner, $request, $userType);

        $dinerWriter->setDiner             ($diner);
        $dinerID   = $dinerWriter->persist ();
      }

      $dateAndTime = sprintf ('%s %s', $request->date, $request->time);

      $this->setTableID ($reservation, $request, $userType);
      $reservation->partySize     = $request->party_size;
      $reservation->partyName     = $request->party_name;
      $reservation->time          = $dateAndTime;
      $reservation->reservedVia   = $request->reserved_via;
      $reservation->restaurant_id = $request->restaurant_id;

      $this->setNotes    ($reservation, $request, $userType);
      $this->setRequests ($reservation, $request, $userType);

      $reservation->diner         = $diner;
      $reservation->diner_id      = $diner->id;
      $reservation->state_id      = $reservationState->id;
      $reservation->state         = $reservationState;
      $reservation->lastModify    = date ('Y-m-d H:i:s');

      $reservationWriter->setReservation ($reservation);
      $reservationWriter->persist        ();

      return $reservation;
    }



    ////////////////////////////////////////////////////////////////////
    // -- Implementation of auxiliary methods.                     -- //
    ////////////////////////////////////////////////////////////////////

    private function setTableID (Reservation $reservation, $request, $userType)
    {
      $hasTableID = ObjectInspector::hasProperty ($request, "table_id");

      if ($userType == UserType::USER_TYPE_SIMPLE)
      {
        if ($hasTableID)
        {
          throw new Exception ('The "table_id" is forbidden for guests.');
        }
        else
        {
          $reservation->table_id = 1; ## TODO: CHANGE TO AUTOMATIC CREATION:
        }
      }
      else if ($userType == UserType::USER_TYPE_HOST)
      {
        if ($hasTableID)
        {
          $reservation->table_id = $request->table_id;
        }
        else
        {
          $reservation->table_id = 1;
        }
      }
      else
      {
          $reservation->table_id = 1;
      }
    }

    private function setNotes (Reservation $reservation, $request, $userType)
    {
      $hasNotes = ObjectInspector::hasProperty ($request, "notes");

      if ($userType == UserType::USER_TYPE_SIMPLE)
      {
        if ($hasNotes)
        {
          throw new Exception ('The "notes" is forbidden for guests.');
        }
      }
      else if ($userType == UserType::USER_TYPE_HOST)
      {
        if ($hasNotes)
        {
          $reservation->notes = $request->notes;
        }
        else
        {
          $reservation->notes = null;
        }
      }
      else
      {
        $reservation->notes = null;
      }
    }

    private function setRequests (Reservation $reservation, $request, $userType)
    {
      $hasRequests = ObjectInspector::hasProperty ($request, "requests");
      if ($hasRequests)
      {
        $reservation->requests = $request->requests;
      }
      else
      {
        $reservation->requests = null;
      }
    }

    private function setGuestEmail (Diner $diner, $request, $userType)
    {
      $hasGuestEmail = ObjectInspector::hasProperty ($request, "guest_email");

      if ($userType == UserType::USER_TYPE_HOST)
      {
        if ($hasGuestEmail)
        {
          $diner->email = $request->guest_email;
        }
        else
        {
          $diner->email = null;
        }
      }
      else
      {
        if ($hasGuestEmail)
        {
          $diner->email = $request->guest_email;
        }
        else
        {
          throw new Exception ('Require field "guest_email" is missing.');
        }
      }
    }

    private function setGuestPhone (Diner $diner, $request, $userType)
    {
      $hasGuestPhone = ObjectInspector::hasProperty ($request, "guest_phone");

      if ($userType == UserType::USER_TYPE_HOST)
      {
        if ($hasGuestPhone)
        {
          $diner->phoneNumber = $request->guest_phone;
        }
        else
        {
          $diner->phoneNumber = null;
        }
      }
      else
      {
        if ($hasGuestPhone)
        {
          $diner->phoneNumber = $request->guest_phone;
        }
        else
        {
          throw new Exception ('Require field "guest_phone" is missing.');
        }
      }
    }

    private function getUserType ()
    {
      if (LoginManager::isLoggedIn ())
      {
        return LoginManager::getStatus ()->getUserType ();
      }
      else
      {
        return UserType::USER_TYPE_SIMPLE;
      }
    }
  }
?>
