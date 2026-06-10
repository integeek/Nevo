<?php
  session_start();
  require_once("../../Model/ParentModel.php");
  header('Content-Type: application/json');

  if (!isset($_SESSION['parent'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
  }

  $action = $_GET['action'] ?? $_POST['action'] ?? '';
  $parent_id = (int) $_SESSION['parent']['id'];

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getParent') {
    try {
      $parent = ParentModel::getParentById($parent_id);
      echo json_encode(['success' => true, 'parent' => ['fullname' => $parent['fullname'], 'email' => $parent['email']]]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'updateParent') {
    if (!isset($_POST['fullname'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    $fullname = trim($_POST['fullname']);
    if (empty($fullname)) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Name cannot be empty']);
      exit;
    }
    try {
      ParentModel::updateParent($parent_id, $fullname);
      $_SESSION['parent']['fullname'] = $fullname;
      echo json_encode(['success' => true]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'deleteAccount') {
    try {
      ParentModel::deleteParent($parent_id);
      session_destroy();
      echo json_encode(['success' => true]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }
?>
