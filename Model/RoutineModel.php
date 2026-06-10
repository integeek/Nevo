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
               COALESCE(
                 (SELECT json_agg(json_build_object('id', rs.id, 'name', rs.name, 'is_completed', rs.is_completed::int) ORDER BY rs.id)
                  FROM routine_step rs WHERE rs.routine_id = r.id),
                 '[]'::json
               ) AS steps
        FROM routine r
        WHERE r.child_id = :child_id AND r.is_active::int != 0
        ORDER BY r.created_at ASC
      ");
      $stmt->execute([':child_id' => (int) $child_id]);
      $routines = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($routines as &$routine) {
        $routine['steps'] = json_decode($routine['steps'], true) ?? [];
      }
      return $routines;
    }

    /**
     * Creates new routine for child in database and returns new routine's ID
     * @param {string} $name - Name of routine
     * @param {string} $icon - Icon for routine
     * @param {int} $xp_value - XP value of routine
     * @param {int} $child_id - ID of child for whom routine is created
     * @param {array} $steps - List of steps for routine
     * @return {int} ID of newly created routine
     */
    public static function createRoutine($name, $icon, $xp_value, $child_id, $steps) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("INSERT INTO routine (name, icon, xp_value, is_active, is_completed, created_at, child_id) VALUES (:name, :icon, :xp_value, true, false, NOW(), :child_id) RETURNING id");
      $stmt->execute([
        ':name' => $name,
        ':icon' => $icon,
        ':xp_value' => (int) $xp_value,
        ':child_id' => (int) $child_id,
      ]);
      $routineId = (int) $stmt->fetchColumn();
      foreach ($steps as $step) {
        $s = $db->prepare("INSERT INTO routine_step (name, routine_id) VALUES (:name, :routine_id)");
        $s->execute([':name' => trim($step), ':routine_id' => $routineId]);
      }
      return $routineId;
    }

    /**
     * Updates routine information in database
     * @param {int} $id - ID of routine to update
     * @param {string} $name - Updated name of routine
     * @param {int} $xp_value - Updated XP value of routine
     * @param {int} $child_id - ID of child for whom routine is created
     * @param {array} $steps - Updated list of steps for routine
     * @return {void}
     */
    public static function updateRoutine($id, $name, $icon, $xp_value, $child_id, $steps) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("UPDATE routine SET name = :name, icon = :icon, xp_value = :xp_value WHERE id = :id AND child_id = :child_id");
      $stmt->execute([
        ':name' => $name,
        ':icon' => $icon,
        ':xp_value' => (int) $xp_value,
        ':id' => (int) $id,
        ':child_id' => (int) $child_id,
      ]);
      $stmt = $db->prepare("DELETE FROM routine_step WHERE routine_id = :routine_id");
      $stmt->execute([':routine_id' => (int) $id]);
      foreach ($steps as $step) {
        $s = $db->prepare("INSERT INTO routine_step (name, routine_id) VALUES (:name, :routine_id)");
        $s->execute([':name' => trim($step), ':routine_id' => (int) $id]);
      }
    }

    /**
     * Deletes routine and all its stepsfrom database
     * @param {int} $id - ID of routine to delete
     * @param {int} $child_id - ID of child for whom routine is created (used to make sure routine belongs them)
     * @return {void}
     */
    public static function deleteRoutine($id, $child_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("DELETE FROM routine WHERE id = :id AND child_id = :child_id");
      $stmt->execute([':id' => (int) $id, ':child_id' => (int) $child_id]);
    }

    /**
     * Returns total number of routines and how many are completed for a child
     * @param {int} $child_id - ID of child
     * @return {array} associative array with total and completed counts
     */
    public static function getCompletedCount($child_id) {
      $db = Bdd::getInstance();
      $stmt = $db->prepare("
        SELECT COUNT(*) AS total,
               SUM(CASE
                 WHEN r.is_completed = true THEN 1
                 WHEN (SELECT COUNT(*) FROM routine_step rs WHERE rs.routine_id = r.id) > 0
                  AND (SELECT COUNT(*) FROM routine_step rs WHERE rs.routine_id = r.id AND rs.is_completed = true)
                    = (SELECT COUNT(*) FROM routine_step rs WHERE rs.routine_id = r.id)
                 THEN 1
                 ELSE 0
               END) AS completed
        FROM routine r
        WHERE r.child_id = :child_id AND r.is_active = true
      ");
      $stmt->execute([':child_id' => (int) $child_id]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
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