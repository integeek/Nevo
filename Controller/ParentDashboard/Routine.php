<?php
  session_start();
  require_once("../../Model/RoutineModel.php");
  require_once("../../Model/ChildModel.php");
  header('Content-Type: application/json');

  if (!isset($_SESSION['parent'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
  }

  $action    = $_GET['action'] ?? $_POST['action'] ?? '';
  $parent_id = (int) $_SESSION['parent']['id'];

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getRoutines') {
    $child_id = (int) ($_GET['child_id'] ?? 0);
    try {
      $child = ChildModel::getChildById($child_id);
      if (!$child || (int) $child['parent_id'] !== $parent_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
      }
      $routines = RoutineModel::getRoutinesWithStepsByChildId($child_id);
      echo json_encode(['success' => true, 'routines' => $routines]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'addRoutine') {
    if (!isset($_POST['name'], $_POST['xp_value'], $_POST['child_id'], $_POST['steps'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    $child_id = (int) $_POST['child_id'];
    try {
      $child = ChildModel::getChildById($child_id);
      if (!$child || (int) $child['parent_id'] !== $parent_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
      }
      $name  = trim($_POST['name']);
      $xp    = (int) $_POST['xp_value'];
      $steps = array_values(array_filter(array_map('trim', explode("\n", $_POST['steps']))));
      $id    = RoutineModel::createRoutine($name, 'icon-alarm', $xp, $child_id, $steps);
      echo json_encode(['success' => true, 'id' => $id]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'editRoutine') {
    if (!isset($_POST['routine_id'], $_POST['name'], $_POST['xp_value'], $_POST['child_id'], $_POST['steps'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    $child_id   = (int) $_POST['child_id'];
    $routine_id = (int) $_POST['routine_id'];
    try {
      $child = ChildModel::getChildById($child_id);
      if (!$child || (int) $child['parent_id'] !== $parent_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
      }
      $name  = trim($_POST['name']);
      $xp    = (int) $_POST['xp_value'];
      $steps = array_values(array_filter(array_map('trim', explode("\n", $_POST['steps']))));
      RoutineModel::updateRoutine($routine_id, $name, $xp, $child_id, $steps);
      echo json_encode(['success' => true]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'deleteRoutine') {
    if (!isset($_POST['routine_id'], $_POST['child_id'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    $child_id   = (int) $_POST['child_id'];
    $routine_id = (int) $_POST['routine_id'];
    try {
      $child = ChildModel::getChildById($child_id);
      if (!$child || (int) $child['parent_id'] !== $parent_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
      }
      RoutineModel::deleteRoutine($routine_id, $child_id);
      echo json_encode(['success' => true]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }
?>
