<?php
  require_once("Bdd.php");
  Class ChildModel {
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
  }
?>