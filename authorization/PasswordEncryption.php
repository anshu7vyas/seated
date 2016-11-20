<?php
  
  /* Implementing classes handle the encryption and comparison of
   * passwords using a certain algorithm.
   * 
   * Date:   06.12.2015
   * Author: Kaveh Yousefi
   */
  
  
  interface PasswordEncryption
  {
    public function encryptPassword
    (
      $passwordAsPlainText,
      $encryptionParameters = null
    );
    
    public function doesPlainPasswordMatchEncryptedPassword
    (
      $plainPasswordToCheck,
      $encryptedPasswordToMatchAgainst,
      $encryptionParamters = null
    );
    
    public function doEncryptedPasswordsMatch
    (
      $encryptedPasswordToCheck,
      $encryptedPasswordToMatchAgainst
    );
  }

?>
