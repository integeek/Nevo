<?php
  session_start();
  require_once("../../Model/ChildModel.php");
  require_once("../../Model/RoutineModel.php");
  require_once("../../Model/FeelingModel.php");
  header('Content-Type: application/json');

  if (!isset($_SESSION['parent'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
  }

  $action    = $_GET['action'] ?? $_POST['action'] ?? '';
  $parent_id = (int) $_SESSION['parent']['id'];

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'init') {
    try {
      $children = ChildModel::getChildrenByParentId($parent_id);
      echo json_encode(['success' => true, 'children' => $children]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getStats') {
    $child_id = (int) ($_GET['child_id'] ?? 0);
    try {
      $child = ChildModel::getChildById($child_id);
      if (!$child || (int) $child['parent_id'] !== $parent_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
      }
      $counts    = RoutineModel::getCompletedCount($child_id);
      $total     = (int) ($counts['total'] ?? 0);
      $completed = (int) ($counts['completed'] ?? 0);
      $completion = $total > 0 ? round(($completed / $total) * 100) . '%' : '0%';
      echo json_encode([
        'success'      => true,
        'completion'   => $completion,
        'streak'       => ($child['streak'] ?? 0) . ' days',
        'routines'     => $total,
        'week'         => $completed . '<sub>/' . $total . '</sub>',
        'child_name'   => $child['fullname'],
        'child_avatar' => $child['avatar'] ?? 'icon-superhero',
      ]);
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getFeelings') {
    $child_id = (int) ($_GET['child_id'] ?? 0);
    try {
      $child = ChildModel::getChildById($child_id);
      if (!$child || (int) $child['parent_id'] !== $parent_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
      }
      $feelings = FeelingModel::getFeelingsByChildId($child_id);
      echo json_encode(['success' => true, 'feelings' => $feelings]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'getAnalytics') {
    $child_id = (int) ($_GET['child_id'] ?? 0);
    try {
      $child = ChildModel::getChildById($child_id);
      if (!$child || (int) $child['parent_id'] !== $parent_id) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
      }

      $counts    = RoutineModel::getCompletedCount($child_id);
      $total     = (int) ($counts['total'] ?? 0);
      $completed = (int) ($counts['completed'] ?? 0);

      $feelings = FeelingModel::getFeelingsByChildId($child_id);
      $feelingCounts = [];
      foreach ($feelings as $f) {
        $key = $f['emoji'];
        $feelingCounts[$key] = ($feelingCounts[$key] ?? 0) + 1;
      }
      arsort($feelingCounts);

      echo json_encode([
        'success'          => true,
        'routines_total'   => $total,
        'routines_done'    => $completed,
        'routines_pending' => $total - $completed,
        'feelings_counts'  => $feelingCounts,
        'feelings_total'   => count($feelings),
      ]);
    } catch (\Throwable $e) {
      http_response_code(500);
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }
?>
