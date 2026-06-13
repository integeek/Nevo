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
     * Creates a new quest in database
     * @param {string} $name - Name of quest
     * @param {string} $icon - Icon name for quest
     * @param {int} $xp_value - XP value for completing quest
     * @param {int} $child_id - ID of child
     * @param {int} $parent_id - ID of parent creating quest
     * @return {int} ID of created quest
     */
    public static function createQuest($name, $icon, $xp_value, $child_id, $parent_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("INSERT INTO quest (name, icon, xp_value, child_id, created_by) VALUES (:name, :icon, :xp_value, :child_id, :created_by) RETURNING id");
      $stmt->execute([
        ':name' => $name,
        ':icon' => $icon,
        ':xp_value' => (int) $xp_value,
        ':child_id' => (int) $child_id,
        ':created_by' => (int) $parent_id,
      ]);
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return $result['id'] ?? null;
    }

    /**
     * Updates a quest in database
     * @param {int} $id - ID of quest to update
     * @param {string} $name - Updated name
     * @param {string} $icon - Updated icon
     * @param {int} $xp_value - Updated XP value
     * @param {int} $child_id - Child ID (for verification)
     * @return {void}
     */
    public static function updateQuest($id, $name, $icon, $xp_value, $child_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE quest SET name = :name, icon = :icon, xp_value = :xp_value WHERE id = :id AND child_id = :child_id");
      $stmt->execute([
        ':name' => $name,
        ':icon' => $icon,
        ':xp_value' => (int) $xp_value,
        ':id' => (int) $id,
        ':child_id' => (int) $child_id,
      ]);
    }

    /**
     * Deletes a quest from database
     * @param {int} $quest_id - ID of quest to delete
     * @param {int} $child_id - ID of child (for verification)
     * @return {void}
     */
    public static function deleteQuest($quest_id, $child_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("DELETE FROM quest WHERE id = :id AND child_id = :child_id");
      $stmt->execute([':id' => (int) $quest_id, ':child_id' => (int) $child_id]);
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