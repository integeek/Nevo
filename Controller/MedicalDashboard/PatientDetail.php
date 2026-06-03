<?php
  session_start();
  require_once("../../Model/MedicalStaffModel.php");
  require_once("../../Model/ChildModel.php");
  require_once("../../Model/RoutineModel.php");
  require_once("../../Model/FeelingModel.php");
  require_once("../../Model/RewardModel.php");
  header('Content-Type: application/json');

  if (!isset($_SESSION['staff'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
  }

  $action   = $_GET['action'] ?? $_POST['action'] ?? '';
  $staff_id = (int) $_SESSION['staff']['id'];
  $child_id = (int) ($_GET['child_id'] ?? 0);

  if ($child_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing child_id']);
    exit;
  }

  if (!MedicalStaffModel::isPatientOfStaff($child_id, $staff_id)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getPatient') {
    try {
      $child  = ChildModel::getChildById($child_id);
      $counts = RoutineModel::getCompletedCount($child_id);
      $total  = (int) ($counts['total'] ?? 0);
      $done   = (int) ($counts['completed'] ?? 0);
      echo json_encode([
        'success'    => true,
        'child'      => $child,
        'completion' => $total > 0 ? round(($done / $total) * 100) . '%' : '0%',
        'routines'   => $total,
      ]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getRoutines') {
    try {
      $routines = RoutineModel::getRoutinesWithStepsByChildId($child_id);
      echo json_encode(['success' => true, 'routines' => $routines]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getFeelings') {
    try {
      $feelings = FeelingModel::getFeelingsByChildId($child_id);
      echo json_encode(['success' => true, 'feelings' => $feelings]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getRewards') {
    try {
      $rewards = RewardModel::getRewardsByChildId($child_id);
      echo json_encode(['success' => true, 'rewards' => $rewards]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }
?>
