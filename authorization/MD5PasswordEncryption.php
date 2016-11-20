<?php
  
  /* Handles the encryption and the comparison of passwords using the
   * rather insecure MD5 hashing algorithm.
   * 
   * Date:   06.12.2015
   * Author: Kaveh Yousefi
   */
  
  require_once ("authorization/PasswordEncryption.php");
  
  
  class      MD5PasswordEncryption
  implements PasswordEncryption
  {
    public function __construct ()
    {
    }
    
    
    public function encryptPassword
    (
      $passwordAsPlainText,
      $encryptionParameters = null
    )
    {
      return md5 ($passwordAsPlainText);
    }
    
    public function doesPlainPasswordMatchEncryptedPassword
    (
      $plainPasswordToCheck,
      $encryptedPasswordToMatchAgainst,
      $encryptionParameters = null
    )
    {
      $plainPasswordEncrypted = $this->encryptPassword ($plainPasswordToCheck);
      
      return $this->doEncryptedPasswordsMatch
      (
        $plainPasswordEncrypted,
        $encryptedPasswordToMatchAgainst
      );
    }
    
    public function doEncryptedPasswordsMatch
    (
      $encryptedPasswordToCheck,
      $encryptedPasswordToMatchAgainst
    )
    {
      return ($encryptedPasswordToCheck == $encryptedPasswordToMatchAgainst);
    }
  }

?>
