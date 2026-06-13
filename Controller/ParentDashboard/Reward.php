<?php
  session_start();
  require_once("../../Model/RewardModel.php");
  require_once("../../Model/ChildModel.php");
  header('Content-Type: application/json');

  if (!isset($_SESSION['parent'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
  }

  $action = $_GET['action'] ?? $_POST['action'] ?? '';
  $parent_id = (int) $_SESSION['parent']['id'];

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getRewards') {
    $child_id = (int) ($_GET['child_id'] ?? 0);
    try {
      $child = ChildModel::getChildById($child_id);
      if (!$child || (int) $child['parent_id'] !== $parent_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
      }
      $rewards = RewardModel::getRewardsByChildId($child_id);
      echo json_encode(['success' => true, 'rewards' => $rewards]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'addReward') {
    if (!isset($_POST['name'], $_POST['icon'], $_POST['xp_cost'], $_POST['child_id'])) {
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
      $name = trim($_POST['name']);
      $icon = trim($_POST['icon']) ?: 'icon-star';
      $xp = (int) $_POST['xp_cost'];
      $type = in_array($_POST['type'] ?? '', ['in_app', 'out_app']) ? $_POST['type'] : 'out_app';
      $id = RewardModel::createReward($name, $icon, $xp, $type, $child_id, $parent_id);
      echo json_encode(['success' => true, 'id' => $id]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'editReward') {
    if (!isset($_POST['reward_id'], $_POST['name'], $_POST['icon'], $_POST['xp_cost'], $_POST['child_id'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    $child_id = (int) $_POST['child_id'];
    $reward_id = (int) $_POST['reward_id'];
    try {
      $child = ChildModel::getChildById($child_id);
      if (!$child || (int) $child['parent_id'] !== $parent_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
      }
      $name = trim($_POST['name']);
      $icon = trim($_POST['icon']) ?: 'icon-star';
      $xp = (int) $_POST['xp_cost'];
      $type = in_array($_POST['type'] ?? '', ['in_app', 'out_app']) ? $_POST['type'] : 'out_app';
      RewardModel::updateReward($reward_id, $name, $icon, $xp, $type, $child_id);
      echo json_encode(['success' => true]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'deleteReward') {
    if (!isset($_POST['reward_id'], $_POST['child_id'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    $child_id = (int) $_POST['child_id'];
    $reward_id = (int) $_POST['reward_id'];
    try {
      $child = ChildModel::getChildById($child_id);
      if (!$child || (int) $child['parent_id'] !== $parent_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
      }
      RewardModel::deleteReward($reward_id, $child_id);
      echo json_encode(['success' => true]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }
?>
