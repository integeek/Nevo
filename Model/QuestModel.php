<?php
  require_once("Bdd.php");
  Class QuestModel {
    /**
     * Fetches all quests for child by child ID from database, ordered by creation date (from most recent first to oldest)
     * @param {int} $child_id - ID of child
     * @return {array} list of quests as associative arrays, empty list if no quests found
     */
    public static function getQuestsByChildId($child_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("SELECT * FROM quest WHERE child_id = :child_id ORDER BY created_at ASC");
      $stmt->execute([':child_id' => $child_id]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Marks quest as completed in database and adds XP to child's total XP
     * @param {int} $quest_id - ID of quest to mark as completed
     * @param {int} $child_id - ID of child completing quest
     * @param {int} $xp_value - amount of XP to add to child's total XP for completing quest
     * @return {void}
     */
    public static function completeQuest($quest_id, $child_id, $xp_value) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE quest SET is_completed = true, completed_at = NOW() WHERE id = :id");
      $stmt->execute([':id' => $quest_id]);
      $stmt = $db->prepare("UPDATE child SET xp = xp + :xp WHERE id = :child_id");
      $stmt->execute([':xp' => $xp_value, ':child_id' => $child_id]);
    }
  }
?>