<?php
  require_once("Bdd.php");
  Class RoutineModel {
    public static function getRoutinesWithStepsByChildId($child_id) {
      $db = Bdd::getInstance();

      $stmt = $db->prepare("
        SELECT r.*, 
               json_agg(json_build_object(
                 'id', rs.id,
                 'name', rs.name,
                 'is_completed', rs.is_completed
               ) ORDER BY rs.id) as steps
        FROM routine r
        LEFT JOIN routine_step rs ON rs.routine_id = r.id
        WHERE r.child_id = :child_id AND r.is_active = true
        GROUP BY r.id
        ORDER BY r.created_at ASC
      ");
      $stmt->execute([':child_id' => $child_id]);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function completeStep($step_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE routine_step SET is_completed = true WHERE id = :id");
      $stmt->execute([':id' => $step_id]);
    }

    public static function uncompleteStep($step_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE routine_step SET is_completed = false WHERE id = :id");
      $stmt->execute([':id' => $step_id]);
    }

    public static function completeRoutine($routine_id, $child_id, $xp_value) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE routine SET is_completed = true WHERE id = :id");
      $stmt->execute([':id' => $routine_id]);
      $stmt = $db->prepare("UPDATE child SET xp = xp + :xp WHERE id = :child_id");
      $stmt->execute([':xp' => $xp_value, ':child_id' => $child_id]);
    }

    public static function uncompleteRoutine($routine_id, $child_id, $xp_value) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE routine SET is_completed = false WHERE id = :id");
      $stmt->execute([':id' => $routine_id]);
      $stmt = $db->prepare("UPDATE child SET xp = xp - :xp WHERE id = :child_id");
      $stmt->execute([':xp' => $xp_value, ':child_id' => $child_id]);
    }
  }
?>