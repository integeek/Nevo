<?php
  require_once("Bdd.php");
  Class FeelingModel {
    public static function publishFeeling($emoji, $text) {
      $db = Bdd::getInstance();
      $sql = "INSERT INTO feeling (emoji, text, child_id) VALUES (:emoji, :text, :child_id)";
      $stmt = $db-> prepare($sql);
      $stmt-> execute([
        ':emoji'    => $emoji,
        ':text'     => $text,
        ':child_id' => 1,
      ]);
    }

    public static function getFeelingsByChildId($child_id) {
      $db   = Bdd::getInstance();
      $sql  = "SELECT * FROM feeling WHERE child_id = :child_id ORDER BY created_at DESC";
      $stmt = $db->prepare($sql);
      $stmt->execute([
        ':child_id' => $child_id
      ]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  }
?>