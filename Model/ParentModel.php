<?php
  require_once("Bdd.php");
  Class ParentModel {
    public static function checkEmail($email) {
      $db = Bdd::getInstance();
      $sql = $db -> prepare("SELECT id FROM parent WHERE email = :email");
      $sql-> bindValue(":email", $email, PDO::PARAM_STR);
      $sql->execute();
      return $sql->fetch();
    }
    public static function createUser($name, $email, $pass) {
      $db = Bdd::getInstance();
      $sql = "INSERT INTO parent (fullname, email, password) VALUES ('$name', :email, '$pass')";
      $stmt = $db->prepare($sql);
      $stmt->execute([
        ':email' => $email,
      ]);
      return $db->lastInsertId();
    }

    public static function login($email) {
      $db = Bdd::getInstance();
      $sql = "SELECT * FROM parent WHERE email = :email";
      $query = $db -> prepare($sql);
      $query -> bindValue(":email", $_POST["email"], PDO::PARAM_STR);
      $query -> execute();
      return $query -> fetch();
    }
    
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

    public static function getUserByResetToken($token_hash) {
      $db = Bdd::getInstance();
      $sql = "SELECT * FROM parent WHERE reset_token_hash = :reset_token";
      $query = $db -> prepare($sql);
      $query->execute([
        "reset_token" => $token_hash,
      ]);
      return $query->fetch(PDO::FETCH_ASSOC);
    }

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