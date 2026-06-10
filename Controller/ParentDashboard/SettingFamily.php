<?php
  session_start();
  require_once("../../Model/ChildModel.php");
  require_once("../../Model/MedicalStaffModel.php");
  header('Content-Type: application/json');

  if (!isset($_SESSION['parent'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
  }

  $action = $_GET['action'] ?? $_POST['action'] ?? '';
  $parent_id = (int) $_SESSION['parent']['id'];

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getChildren') {
    try {
      $children = ChildModel::getChildrenByParentId($parent_id);
      echo json_encode(['success' => true, 'children' => $children]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'addChild') {
    if (!isset($_POST['fullname'], $_POST['age'], $_POST['avatar'], $_POST['pin'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    $fullname = trim($_POST['fullname']);
    $age = (int) $_POST['age'];
    $avatar = trim($_POST['avatar']);
    $pin = (int) $_POST['pin'];
    $disease = trim($_POST['disease'] ?? '');
    if (empty($fullname) || $age <= 0 || $age > 18) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Invalid data']);
      exit;
    }
    try {
      $id = ChildModel::createChild($fullname, $age, $avatar, $pin, $disease, $parent_id);
      echo json_encode(['success' => true, 'id' => $id]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'editChild') {
    if (!isset($_POST['child_id'], $_POST['fullname'], $_POST['age'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    $child_id = (int) $_POST['child_id'];
    $fullname = trim($_POST['fullname']);
    $age = (int) $_POST['age'];
    $disease = trim($_POST['disease'] ?? '');
    if (empty($fullname) || $age <= 0) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Invalid data']);
      exit;
    }
    try {
      ChildModel::updateChild($child_id, $fullname, $age, $disease, $parent_id);
      echo json_encode(['success' => true]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'deleteChild') {
    if (!isset($_POST['child_id'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    $child_id = (int) $_POST['child_id'];
    try {
      ChildModel::deleteChild($child_id, $parent_id);
      echo json_encode(['success' => true]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'searchStaff') {
    $query = trim($_GET['q'] ?? '');
    if (strlen($query) < 2) {
      echo json_encode(['success' => true, 'staff' => []]);
      exit;
    }
    try {
      $staff = MedicalStaffModel::searchStaff($query);
      echo json_encode(['success' => true, 'staff' => $staff]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'linkStaff') {
    if (!isset($_POST['staff_id'], $_POST['child_ids'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    $staff_id = (int) $_POST['staff_id'];
    $child_ids = array_map('intval', (array) $_POST['child_ids']);
    if ($staff_id <= 0 || empty($child_ids)) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Invalid data']);
      exit;
    }
    try {
      foreach ($child_ids as $child_id) {
        $child = ChildModel::getChildById($child_id);
        if ($child && (int) $child['parent_id'] === $parent_id) {
          MedicalStaffModel::linkToChild($staff_id, $child_id, $parent_id);
        }
      }
      echo json_encode(['success' => true]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getStaff') {
    try {
      $staff = MedicalStaffModel::getStaffByParentId($parent_id);
      echo json_encode(['success' => true, 'staff' => $staff]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'addStaff') {
    if (!isset($_POST['fullname'], $_POST['child_ids'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    $fullname = trim($_POST['fullname']);
    $speciality = trim($_POST['speciality'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $child_ids = array_map('intval', (array) $_POST['child_ids']);
    if (empty($fullname) || empty($child_ids)) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Invalid data']);
      exit;
    }
    try {
      $staff_id = MedicalStaffModel::createStaff($fullname, $speciality, $email);
      foreach ($child_ids as $child_id) {
        $child = ChildModel::getChildById($child_id);
        if ($child && (int) $child['parent_id'] === $parent_id) {
          MedicalStaffModel::linkToChild($staff_id, $child_id, $parent_id);
        }
      }
      echo json_encode(['success' => true]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'editStaff') {
    if (!isset($_POST['staff_id'], $_POST['fullname'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    try {
      MedicalStaffModel::updateStaff(
        (int) $_POST['staff_id'],
        trim($_POST['fullname']),
        trim($_POST['speciality'] ?? ''),
        trim($_POST['email'] ?? ''),
        $parent_id
      );
      echo json_encode(['success' => true]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'deleteStaff') {
    if (!isset($_POST['staff_id'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    try {
      MedicalStaffModel::deleteStaff((int) $_POST['staff_id'], $parent_id);
      echo json_encode(['success' => true]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'removeStaffLink') {
    if (!isset($_POST['link_id'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    try {
      MedicalStaffModel::unlinkFromChild((int) $_POST['link_id'], $parent_id);
      echo json_encode(['success' => true]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }
?>
