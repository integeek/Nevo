<?php
  require_once("Bdd.php");
  Class FeelingModel {
    /**
     * Publishes new feeling entry for child into database
     * @param {string} $emoji - emoji icon representing child's feeling
     * @param {string} $text - optional note written by child to describe their feeling
     * @return {void}
     */
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

    /**
     * Gets all feeling entries for child from database, ordered by creation date (from most recent first to oldest)
     * @param {int} $child_id - ID of child
     * @return {array} list of feeling entries as associative arrays, empty list if no entries found
     */
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