<?php
  session_start();
  require_once("../../Model/ChildModel.php");
  require_once("../../Model/RoutineModel.php");
  require_once("../../Model/QuestModel.php");
  header('Content-Type: application/json');

  $action = $_GET['action'] ?? $_POST['action'] ?? '';
  $child_id = (int) ($_SESSION["child"]["id"] ?? 1);

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'init') {
    try {
      $child = ChildModel::getChildById($child_id);
      $routines = RoutineModel::getRoutinesWithStepsByChildId($child_id);
      $quests = QuestModel::getQuestsByChildId($child_id);
      echo json_encode(['success' => true, 'child' => $child, 'routines' => $routines, 'quests' => $quests]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'toggleStep') {
    $step_id = (int) $_POST['step_id'];
    $is_completed = $_POST['is_completed'] === 'true';
    try {
      $is_completed ? RoutineModel::completeStep($step_id) : RoutineModel::uncompleteStep($step_id);
      echo json_encode(['success' => true]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'toggleRoutine') {
    $routine_id = (int) $_POST['routine_id'];
    $xp_value = (int) $_POST['xp_value'];
    $is_completed = $_POST['is_completed'] === 'true';
    try {
      $is_completed ? RoutineModel::completeRoutine($routine_id, $child_id, $xp_value) : RoutineModel::uncompleteRoutine($routine_id, $child_id, $xp_value);
      echo json_encode(['success' => true]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }
?>
