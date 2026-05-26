<?php
  require_once("Bdd.php");
  Class QuestModel {
    public static function getQuestsByChildId($child_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("SELECT * FROM quest WHERE child_id = :child_id ORDER BY created_at ASC");
      $stmt->execute([':child_id' => $child_id]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function completeQuest($quest_id, $child_id, $xp_value) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE quest SET is_completed = true, completed_at = NOW() WHERE id = :id");
      $stmt->execute([':id' => $quest_id]);
      $stmt = $db->prepare("UPDATE child SET xp = xp + :xp WHERE id = :child_id");
      $stmt->execute([':xp' => $xp_value, ':child_id' => $child_id]);
    }
  }
?>