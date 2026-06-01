<?php 
  session_start();
  require_once("../../Model/FeelingModel.php");
  header('Content-Type: application/json');

  $action = $_GET['action'] ?? $_POST['action'] ?? '';

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getFeelings') {
    try {
      $child_id = (int) ($_SESSION["child"]["id"] ?? 1);
      $feelings = FeelingModel::getFeelingsByChildId($child_id);
      echo json_encode(['success' => true, 'feelings' => $feelings]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => 'Error with database : ' . $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!isset($_POST['emoji']) || !isset($_POST['feeling'])) {
      $_SESSION["error"] = "The form is incomplete";
      header("Location: ../../View/Page/WriteFeelings.php");
      exit;
    }

    $emoji = trim($_POST['emoji']);
    $note = trim($_POST['note'] ?? '');
    $child_id  = (int) ($_SESSION["child"]["id"] ?? 1);

    try {
      FeelingModel::publishFeeling($emoji, $note, $child_id);
      $_SESSION["success"] = "Feeling published";
      header("Location: ../../View/Page/WriteFeelings.php");
      exit;
    } catch (PDOException $e) {
      $_SESSION["error"] = "Error with database";
      header("Location: ../../View/Page/WriteFeelings.php");
      exit;
    }
  }
?>
