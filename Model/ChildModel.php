<?php
  require_once("Bdd.php");
  Class ChildModel {
    public static function getChildrenByParentId($parent_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("SELECT * FROM child WHERE parent_id = :parent_id ORDER BY created_at ASC");
      $stmt->execute([
        ':parent_id' => $parent_id
      ]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllChildren() {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("SELECT * FROM child ORDER BY created_at ASC");
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getXpByChildId($child_id) {
      $db   = Bdd::getInstance();
      $stmt = $db->prepare("SELECT xp FROM child WHERE id = :id");
      $stmt->execute([':id' => $child_id]);
      $row  = $stmt->fetch(PDO::FETCH_ASSOC);
      return $row ? (int) $row['xp'] : 0;
    }

    public static function getChildById($child_id) {
      $db   = Bdd::getInstance();
      $stmt = $db->prepare("SELECT * FROM child WHERE id = :id");
      $stmt->execute([
        ':id' => $child_id
      ]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function createChild($fullname, $age, $disease, $avatar, $password, $parent_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("
        INSERT INTO child (fullname, age, disease, avatar, password, xp, streak, parent_id)
        VALUES (:fullname, :age, :disease, :avatar, :password, 0, 0, :parent_id)
      ");
      $stmt->execute([
        ':fullname' => $fullname,
        ':age' => $age,
        ':disease' => $disease,
        ':avatar' => $avatar,
        ':password' => $password,
        ':parent_id' => $parent_id
      ]);
      return $db->lastInsertId();
    }

    public static function login($child_id, $password) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("SELECT * FROM child WHERE id = :id AND password = :password");
      $stmt->execute([
        ':id' => $child_id,
        ':password' => $password
      ]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function updatePin($child_id, $password) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE child SET password = :password WHERE id = :id");
      $stmt->execute([
        ':password' => $password,
        ':id' => $child_id
      ]);
    }
  }
?>
