<?php
  require_once("Bdd.php");
  Class RewardModel {

    /**
     * Fetches all rewards for child by child ID from database, ordered by XP cost (from cheapest to most expensive)
     * @param {int} $child_id - ID of child
     * @return {array} list of rewards as associative arrays, empty list if no rewards found
     */
    public static function getRewardsByChildId($child_id) {
      $db = Bdd::getInstance();
      $sql = "SELECT * FROM reward WHERE child_id = :child_id ORDER BY xp_cost ASC";
      $stmt = $db->prepare($sql);
      $stmt->execute([
        ':child_id' => $child_id
      ]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches reward by ID from database
     * @param {int} $reward_id - ID of reward to fetch
     * @return {array|false} associative array of reward's details if found and false otherwise
     */
    public static function getRewardById($reward_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("SELECT * FROM reward WHERE id = :id");
      $stmt->execute([
        ':id' => $reward_id
      ]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Marks reward as purchased in database and deducts XP cost from child's total XP
     * @param {int} $reward_id - ID of reward to purchase
     * @param {int} $child_id - ID of child buying reward
     * @param {int} $xp_cost - amount of XP to deduct from child's total XP for purchasing reward
     * @return {void}
     */
    public static function buyReward($reward_id, $child_id, $xp_cost) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE reward SET is_completed = true, completed_at = NOW() WHERE id = :id");
      $stmt->execute([':id' => $reward_id]);
      $stmt = $db->prepare("UPDATE child SET xp = xp - :xp_cost WHERE id = :child_id");
      $stmt->execute([':xp_cost' => $xp_cost, ':child_id' => $child_id]);
    }

    /**
     * Creates new reward for child in database and returns new reward's ID
     * @param {string} $name - Name of reward
     * @param {string} $icon - Icon for reward
     * @param {int} $xp_cost - XP cost of reward
     * @param {string} $type - Type of reward (in_app or real)
     * @param {int} $child_id - ID of child for whom reward is created
     * @param {int} $parent_id - ID of parent who created reward
     * @return {int} ID of newly created reward
     */
    public static function createReward($name, $icon, $xp_cost, $type, $child_id, $parent_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("INSERT INTO reward (name, icon, xp_cost, type, is_completed, child_id, created_by) VALUES (:name, :icon, :xp_cost, :type, false, :child_id, :created_by) RETURNING id");
      $stmt->execute([
        ':name' => $name,
        ':icon' => $icon,
        ':xp_cost' => (int) $xp_cost,
        ':type' => $type,
        ':child_id' => (int) $child_id,
        ':created_by' => (int) $parent_id,
      ]);
      return (int) $stmt->fetchColumn();
    }

    /**
     * Updates reward information (name, XP cost, type) in database
     * @param {int} $id - ID of reward to update
     * @param {string} $name - Updated name of reward
     * @param {int} $xp_cost - Updated XP cost of reward
     * @param {string} $type - Updated type of reward
     * @param {int} $child_id - ID of child for whom reward is created
     * @return {void}
     */
    public static function updateReward($id, $name, $xp_cost, $type, $child_id) {
      $db   = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE reward SET name = :name, xp_cost = :xp_cost, type = :type WHERE id = :id AND child_id = :child_id");
      $stmt->execute([
        ':name' => $name,
        ':xp_cost' => (int) $xp_cost,
        ':type' => $type,
        ':id' => (int) $id,
        ':child_id' => (int) $child_id,
      ]);
    }

    /**
     * Deletes reward from database
     * @param {int} $id - ID of reward to delete
     * @param {int} $child_id - ID of child for whom reward is created
     * @return {void}
     */
    public static function deleteReward($id, $child_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("DELETE FROM reward WHERE id = :id AND child_id = :child_id");
      $stmt->execute([':id' => (int) $id, ':child_id' => (int) $child_id]);
    }
  }
?>