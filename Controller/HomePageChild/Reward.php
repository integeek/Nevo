<?php 
  session_start();
  require_once("../../Model/RewardModel.php");
  require_once("../../Model/ChildModel.php");
  header('Content-Type: application/json');

  $action = $_GET['action'] ?? $_POST['action'] ?? '';

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getRewards') {
    try {
      $child_id = (int) ($_SESSION["child"]["id"] ?? 1);
      $rewards = RewardModel::getRewardsByChildId($child_id);
      echo json_encode(['success' => true, 'rewards' => $rewards]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => 'Error with database : ' . $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getXp') {
    try {
      $child_id = (int) ($_SESSION["child"]["id"] ?? 1);
      $xp = ChildModel::getXpByChildId($child_id);
      echo json_encode(['success' => true, 'xp' => $xp]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => 'Error with database : ' . $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'buyReward') {
    if (!isset($_POST['reward_id'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }

    $reward_id = (int) $_POST['reward_id'];
    $child_id  = (int) ($_SESSION["child"]["id"] ?? 1);

    try {
      $reward = RewardModel::getRewardById($reward_id);
      if (!$reward) {
        echo json_encode(['success' => false, 'message' => 'Reward not found']);
        exit;
      }
      if ($reward['is_completed']) {
        echo json_encode(['success' => false, 'message' => 'Reward already purchased']);
        exit;
      }

      $xp = ChildModel::getXpByChildId($child_id);
      if ($xp < $reward['xp_cost']) {
        echo json_encode(['success' => false, 'message' => 'Not enough XP']);
        exit;
      }
      RewardModel::buyReward($reward_id, $child_id, $reward['xp_cost']);
      echo json_encode(['success' => true, 'new_xp' => $xp - $reward['xp_cost']]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => 'Error with database : ' . $e->getMessage()]);
    }
    exit;
  }
?>
