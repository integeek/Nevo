<?php
  session_start();
  require_once("../../Model/MedicalStaffModel.php");
  require_once("../../Model/ChildModel.php");
  require_once("../../Model/RoutineModel.php");
  require_once("../../Model/FeelingModel.php");
  header('Content-Type: application/json');

  if (!isset($_SESSION['staff'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
  }

  $action   = $_GET['action'] ?? $_POST['action'] ?? '';
  $staff_id = (int) $_SESSION['staff']['id'];

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'init') {
    try {
      $staff    = MedicalStaffModel::getById($staff_id);
      $patients = MedicalStaffModel::getPatientsByStaffId($staff_id);
      echo json_encode(['success' => true, 'staff' => $staff, 'patients' => $patients]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getPatientStats') {
    $child_id = (int) ($_GET['child_id'] ?? 0);
    try {
      if (!MedicalStaffModel::isPatientOfStaff($child_id, $staff_id)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
      }
      $child    = ChildModel::getChildById($child_id);
      $counts   = RoutineModel::getCompletedCount($child_id);
      $total    = (int) ($counts['total'] ?? 0);
      $done     = (int) ($counts['completed'] ?? 0);
      $feelings = FeelingModel::getFeelingsByChildId($child_id);
      echo json_encode([
        'success'      => true,
        'completion'   => $total > 0 ? round(($done / $total) * 100) . '%' : '0%',
        'routines'     => $total,
        'streak'       => ($child['streak'] ?? 0) . ' days',
        'xp'           => $child['xp'] ?? 0,
        'last_feeling' => count($feelings) > 0 ? $feelings[0]['emoji'] : null,
      ]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }
?>
