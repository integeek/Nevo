<?php
  require_once("Bdd.php");
  Class ChildModel {
    /**
     * Gets current XP of child by ID from database, returning 0 if child not found
     * @param {int} child_id - ID of child 
     * @return {int} child's XP or 0 if child not found
     */
    public static function getXpByChildId($child_id) {
      $db   = Bdd::getInstance();
      $stmt = $db->prepare("SELECT xp FROM child WHERE id = :id");
      $stmt->execute([':id' => $child_id]);
      $row  = $stmt->fetch(PDO::FETCH_ASSOC);
      return $row ? (int) $row['xp'] : 0;
    }
    
    /**
     * Gets child details by ID from database
     * @param {int} child_id - ID of child
     * @return {array|false} assosciative array of child details or false if child not found
     */
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