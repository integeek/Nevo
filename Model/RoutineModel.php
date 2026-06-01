<?php
  require_once("Bdd.php");
  Class RoutineModel {
    /**
     * Fetches all active routines for child by child ID from database, including their steps, ordered by creation date
     * Each routine includes JSON array of its steps with step details (id, name, is_completed)
     * @param {int} $child_id - ID of child
     * @return {array} list of routines with their steps as associative arrays, empty list if no routines found
     */
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

    /**
     * Marks routine step as completed in database
     * @param {int} $step_id - ID of step to complete
     * @return {void}
     */
    public static function completeStep($step_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE routine_step SET is_completed = true WHERE id = :id");
      $stmt->execute([':id' => $step_id]);
    }

    /**
     * Marks routine step as not completed in database
     * @param {int} $step_id - ID of step to uncomplete
     * @return {void}
     */
    public static function uncompleteStep($step_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE routine_step SET is_completed = false WHERE id = :id");
      $stmt->execute([':id' => $step_id]);
    }

    /**
     * Marks routine as completed in database and adds XP to child's total XP
     * @param {int} $routine_id - ID of routine to complete
     * @param {int} $child_id - ID of child completing routine
     * @param {int} $xp_value - amount of XP to add to child's total
     * @return {void}
     */
    public static function completeRoutine($routine_id, $child_id, $xp_value) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE routine SET is_completed = true WHERE id = :id");
      $stmt->execute([':id' => $routine_id]);
      $stmt = $db->prepare("UPDATE child SET xp = xp + :xp WHERE id = :child_id");
      $stmt->execute([':xp' => $xp_value, ':child_id' => $child_id]);
    }

    /**
     * Marks routine as not completed in database and removes XP from child's total XP
     * @param {int} $routine_id - ID of routine to uncomplete
     * @param {int} $child_id - ID of child
     * @param {int} $xp_value - amount of XP to remove from child's total
     * @return {void}
     */
    public static function uncompleteRoutine($routine_id, $child_id, $xp_value) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE routine SET is_completed = false WHERE id = :id");
      $stmt->execute([':id' => $routine_id]);
      $stmt = $db->prepare("UPDATE child SET xp = xp - :xp WHERE id = :child_id");
      $stmt->execute([':xp' => $xp_value, ':child_id' => $child_id]);
    }
  }
?>