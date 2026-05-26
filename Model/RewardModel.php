<?php
  require_once("Bdd.php");
  Class RewardModel {

    public static function getRewardsByChildId($child_id) {
      $db   = Bdd::getInstance();
      $sql  = "SELECT * FROM reward WHERE child_id = :child_id ORDER BY xp_cost ASC";
      $stmt = $db->prepare($sql);
      $stmt->execute([
        ':child_id' => $child_id
      ]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getRewardById($reward_id) {
      $db   = Bdd::getInstance();
      $stmt = $db->prepare("SELECT * FROM reward WHERE id = :id");
      $stmt->execute([
        ':id' => $reward_id
      ]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function buyReward($reward_id, $child_id, $xp_cost) {
      $db = Bdd::getInstance();

      $stmt = $db->prepare("UPDATE reward SET is_completed = true, completed_at = NOW() WHERE id = :id");
      $stmt->execute([
        ':id' => $reward_id
      ]);
      $stmt = $db->prepare("UPDATE child SET xp = xp - :xp_cost WHERE id = :child_id");
      $stmt->execute([
        ':xp_cost' => $xp_cost,
        ':child_id' => $child_id
      ]);
    }
  }
?>