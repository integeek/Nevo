<?php
  session_start();
  require_once("../../Model/QuestModel.php");
  require_once("../../Model/ChildModel.php");
  header('Content-Type: application/json');

  if (!isset($_SESSION['parent'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
  }

  $action = $_GET['action'] ?? $_POST['action'] ?? '';
  $parent_id = (int) $_SESSION['parent']['id'];

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getQuests') {
    $child_id = (int) ($_GET['child_id'] ?? 0);
    try {
      $child = ChildModel::getChildById($child_id);
      if (!$child || (int) $child['parent_id'] !== $parent_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
      }
      $quests = QuestModel::getQuestsByChildId($child_id);
      echo json_encode(['success' => true, 'quests' => $quests]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'addQuest') {
    if (!isset($_POST['name'], $_POST['icon'], $_POST['xp_value'], $_POST['child_id'])) {
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
      $xp = (int) $_POST['xp_value'];
      $id = QuestModel::createQuest($name, $icon, $xp, $child_id, $parent_id);
      echo json_encode(['success' => true, 'id' => $id]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'editQuest') {
    if (!isset($_POST['quest_id'], $_POST['name'], $_POST['icon'], $_POST['xp_value'], $_POST['child_id'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    $child_id = (int) $_POST['child_id'];
    $quest_id = (int) $_POST['quest_id'];
    try {
      $child = ChildModel::getChildById($child_id);
      if (!$child || (int) $child['parent_id'] !== $parent_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
      }
      $name = trim($_POST['name']);
      $icon = trim($_POST['icon']) ?: 'icon-star';
      $xp = (int) $_POST['xp_value'];
      QuestModel::updateQuest($quest_id, $name, $icon, $xp, $child_id);
      echo json_encode(['success' => true]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'deleteQuest') {
    if (!isset($_POST['quest_id'], $_POST['child_id'])) {
      http_response_code(400);
      echo json_encode(['success' => false, 'message' => 'Missing data']);
      exit;
    }
    $child_id = (int) $_POST['child_id'];
    $quest_id = (int) $_POST['quest_id'];
    try {
      $child = ChildModel::getChildById($child_id);
      if (!$child || (int) $child['parent_id'] !== $parent_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
      }
      QuestModel::deleteQuest($quest_id, $child_id);
      echo json_encode(['success' => true]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }
?>
