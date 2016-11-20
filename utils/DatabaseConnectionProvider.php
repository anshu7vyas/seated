<?php

require_once ("databaseConnectionInfo.php");

/* A centralized point creating MySQL connections.
 * 
 * Date: 10.11.2015
 * 
 * @author Kaveh
 */
class DatabaseConnectionProvider
{
  const DATABASE_HOST_NAME = DATABASE_HOST_NAME;
  const DATABASE_USER_NAME = DATABASE_USER_NAME;
  const DATABASE_PASSWORD  = DATABASE_PASSWORD;
  const DATABASE_DB_NAME   = DATABASE_DB_NAME;
  
  
  private function __construct ()
  {
  }
  
  
  public static function createConnection ()
  {
    $connection = new mysqli
    (
      self::DATABASE_HOST_NAME,
      self::DATABASE_USER_NAME,
      self::DATABASE_PASSWORD,
      self::DATABASE_DB_NAME
    );
    
    return $connection;
  }
}
