<?php
  require_once("Bdd.php");
  Class ParentModel {
    /**
     * Checks if parent with given email exists in database
     * @param {string} $email - email to check
     * @return {array|false} parent's ID if parent with given email exists, false otherwise
     */
    public static function checkEmail($email) {
      $db = Bdd::getInstance();
      $sql = $db -> prepare("SELECT id FROM parent WHERE email = :email");
      $sql-> bindValue(":email", $email, PDO::PARAM_STR);
      $sql->execute();
      return $sql->fetch();
    }
    /**
     * Creates new parent user in database and returns new user's ID
     * @param {string} $name - full name of parent
     * @param {string} $email - email of parent
     * @param {string} $pass - hashed password of parent
     * @return {string} ID of the newly created parent
     */
    public static function createUser($name, $email, $pass) {
      $db = Bdd::getInstance();
      $sql = "INSERT INTO parent (fullname, email, password) VALUES ('$name', :email, '$pass')";
      $stmt = $db->prepare($sql);
      $stmt->execute([
        ':email' => $email,
      ]);
      return $db->lastInsertId();
    }

    /**
     * Fetches parent's details from database by email for login, returning false if no parent with given email found
     * @param {string} $email - email address to look up
     * @return {array|false} associative array of parent's details if parent with given email exists, false otherwise
     */
    public static function login($email) {
      $db = Bdd::getInstance();
      $sql = "SELECT * FROM parent WHERE email = :email";
      $query = $db -> prepare($sql);
      $query -> bindValue(":email", $_POST["email"], PDO::PARAM_STR);
      $query -> execute();
      return $query -> fetch();
    }

    public static function getUserById($id) {
      $db = Bdd::getInstance();
      $sql = "SELECT * FROM parent WHERE id = :id";
      $query = $db -> prepare($sql);
      $query->execute([
        "id" => $id,
      ]);
      return $query->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Generates password reset token for parent with given email, saves token hash and expiration time in database, and returns generated token (or false if no parent with given email found)
     * @param {string} $email - email of parent requesting reset
     * @param {string} $expiricy - expiricy datetime for token
     * @return {string|false} generated reset token if parent with given email exists and false otherwise
     */
    public static function generateResetToken($email, $expiricy) {
      $db = Bdd::getInstance();
      $token = bin2hex(random_bytes(16));
      $token_hash = hash("sha256", $token);
      $sql = "UPDATE parent SET reset_token_hash = :reset_token, reset_token_expires_at= :reset_expires WHERE email = :email";
      $query = $db -> prepare($sql);
      $query->execute([
        "email" => $email,
        "reset_token" => $token_hash,
        "reset_expires" => $expiricy
      ]);

      if ($query->rowCount() > 0) {
        return $token;
      }
      return false;
    }

    /**
     * Looks up parent in database by reset token hash, returning parent's details if valid token found (token hash matches or token not invalid) or false otherwise
     * @param {string} $token_hash - hash of reset token
     * @return {array|false} associative array of parent's details if valid token found and false otherwise
     */
    public static function getUserByResetToken($token_hash) {
      $db = Bdd::getInstance();
      $sql = "SELECT * FROM parent WHERE reset_token_hash = :reset_token";
      $query = $db -> prepare($sql);
      $query->execute([
        "reset_token" => $token_hash,
      ]);
      return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Updates parent's password in database and clears reset token hash and expiration time for parent with given ID
     * @param {string} $password - new hashed password to set for parent
     * @param {int} $id - ID of parent to update
     * @return {void}
     */
    public static function resetPassword($password, $id) {
      $db = Bdd::getInstance();
      $sql = "UPDATE parent SET password= :password, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id=:id";
      $query = $db -> prepare($sql);
      $query->execute([
        "password" => $password,
        "id" => $id,
      ]);
    }
  }
?>
