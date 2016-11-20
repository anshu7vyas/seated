<?php

  /* Provides an abstraction layer for accessing the encryption of
   * passwords.
   * 
   * Date:   06.12.2015
   * Author: Kaveh Yousefi
   */
  
  
  require_once ("authorization/MD5PasswordEncryption.php");
  
  
  class PasswordEncryptionManager
  {
    private static $passwordEncryption;
    
    
    private function __construct ()
    {
    }
    
    
    public static function encryptPassword
    (
      $passwordAsPlainText,
      $encryptionParameters = null
    )
    {
      self::initializeEncryptionIfNecessary ();
      return self::$passwordEncryption->encryptPassword
      (
        $passwordAsPlainText,
        $encryptionParameters
      );
    }
    
    public static function doesPlainPasswordMatchEncryptedPassword
    (
      $plainPasswordToCheck,
      $encryptedPasswordToMatchAgainst,
      $encryptionParameters = null
    )
    {
      self::initializeEncryptionIfNecessary ();
      return self::$passwordEncryption->doesPlainPasswordMatchEncryptedPassword
      (
        $plainPasswordToCheck,
        $encryptedPasswordToMatchAgainst,
        $encryptionParameters
      );
    }
    
    public static function doEncryptedPasswordsMatch
    (
      $encryptedPasswordToCheck,
      $encryptedPasswordToMatchAgainst
    )
    {
      self::initializeEncryptionIfNecessary ();
      return self::$passwordEncryption->doEncryptedPasswordsMatch
      (
        $encryptedPasswordToCheck,
        $encryptedPasswordToMatchAgainst
      );
    }
    
    
    
    ////////////////////////////////////////////////////////////////////
    // -- Implementation of auxiliary methods.                     -- //
    ////////////////////////////////////////////////////////////////////
    
    private static function initializeEncryptionIfNecessary ()
    {
      if (self::$passwordEncryption == null)
      {
        self::$passwordEncryption = new MD5PasswordEncryption ();
      }
    }
  }
  
?>
