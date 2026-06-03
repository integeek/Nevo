<?php
  session_start();
  require_once("../../Model/ChildModel.php");
  require_once("../../Model/ParentModel.php");

  $action = $_GET["action"] ?? $_POST["action"] ?? "";

  /**
   * Redirects user to child login page
   * @return void
   */
  function redirectToChildLogin() {
    header("Location: ../../View/Page/LoginChild.php");
    exit;
  }

  /**
   * Checks if pin is valid (exactly 4 digits)
   * @param string $pin - PIN to validate
   * @return bool - true if valid, false otherwise
   */
  function isValidPin($pin) {
    return preg_match("/^[0-9]{4}$/", $pin);
  }

  if ($_SERVER["REQUEST_METHOD"] === "POST" && $action === "createProfile") {
    $name = trim($_POST["profileNameInput"] ?? "");
    $age = trim($_POST["age"] ?? "");
    $disease = trim($_POST["superpower"] ?? "");
    $avatar = trim($_POST["avatar"] ?? "icon-superhero");
    $pin = trim($_POST["secretPin"] ?? "");
    $confirmPin = trim($_POST["confirmSecretPin"] ?? "");

    if ($name === "" || $age === "" || $disease === "" || $pin === "" || $confirmPin === "") {
      $_SESSION["error"] = "The form is incomplete";
      redirectToChildLogin();
    }

    if (!ctype_digit($age) || (int) $age <= 0) {
      $_SESSION["error"] = "The age must be a valid number";
      redirectToChildLogin();
    }

    if (!isValidPin($pin) || !isValidPin($confirmPin)) {
      $_SESSION["error"] = "The pin must contain exactly 4 digits";
      redirectToChildLogin();
    }

    if ($pin !== $confirmPin) {
      $_SESSION["error"] = "The pins do not match";
      redirectToChildLogin();
    }

    if (!isset($_SESSION["parent"]["id"])) {
      $_SESSION["error"] = "Please log in as a parent before creating a child profile";
      redirectToChildLogin();
    }

    try {
      ChildModel::createChild($name, (int) $age, $disease, $avatar, (int) $pin, (int) $_SESSION["parent"]["id"]);
      $_SESSION["success"] = "Profile created";
      redirectToChildLogin();
    } catch (PDOException $e) {
      $_SESSION["error"] = "Error with database";
      redirectToChildLogin();
    }
  }

  if ($_SERVER["REQUEST_METHOD"] === "POST" && $action === "login") {
    header("Content-Type: application/json");

    $childId = (int) ($_POST["child_id"] ?? 0);
    $pin = trim($_POST["pin"] ?? "");

    if ($childId <= 0 || !isValidPin($pin)) {
      http_response_code(400);
      echo json_encode(["success" => false, "message" => "Invalid pin"]);
      exit;
    }

    try {
      $child = ChildModel::login($childId, (int) $pin);
      if (!$child) {
        echo json_encode(["success" => false, "message" => "Invalid pin"]);
        exit;
      }

      if (isset($_SESSION["parent"]["id"]) && (int) $child["parent_id"] !== (int) $_SESSION["parent"]["id"]) {
        echo json_encode(["success" => false, "message" => "Invalid profile"]);
        exit;
      }

      $_SESSION["child"] = [
        "id" => $child["id"],
        "fullname" => $child["fullname"],
        "avatar" => $child["avatar"]
      ];
      echo json_encode(["success" => true, "redirect" => "../../View/Page/HomePageChild.php"]);
      exit;
    } catch (PDOException $e) {
      http_response_code(500);
      echo json_encode(["success" => false, "message" => "Error with database"]);
      exit;
    }
  }

  if ($_SERVER["REQUEST_METHOD"] === "POST" && $action === "resetPin") {
    $childId = (int) ($_POST["child_id"] ?? 0);
    $parentPassword = $_POST["password"] ?? "";
    $pin = trim($_POST["secretPin"] ?? "");
    $confirmPin = trim($_POST["confirmSecretPin"] ?? "");

    if ($childId <= 0 || $parentPassword === "" || $pin === "" || $confirmPin === "") {
      $_SESSION["error"] = "The form is incomplete";
      header("Location: ../../View/Page/ForgotPin.php?child_id=" . $childId);
      exit;
    }

    if (!isValidPin($pin) || !isValidPin($confirmPin)) {
      $_SESSION["error"] = "The pin must contain exactly 4 digits";
      header("Location: ../../View/Page/ForgotPin.php?child_id=" . $childId);
      exit;
    }

    if ($pin !== $confirmPin) {
      $_SESSION["error"] = "The pins do not match";
      header("Location: ../../View/Page/ForgotPin.php?child_id=" . $childId);
      exit;
    }

    if (!isset($_SESSION["parent"]["id"])) {
      $_SESSION["error"] = "Please log in as a parent before changing a pin";
      header("Location: ../../View/Page/LoginParent.php");
      exit;
    }

    try {
      $parent = ParentModel::getUserById((int) $_SESSION["parent"]["id"]);
      if (!$parent || !password_verify($parentPassword, $parent["password"])) {
        $_SESSION["error"] = "The parent password is incorrect";
        header("Location: ../../View/Page/ForgotPin.php?child_id=" . $childId);
        exit;
      }

      $child = ChildModel::getChildById($childId);
      if (!$child || (int) $child["parent_id"] !== (int) $_SESSION["parent"]["id"]) {
        $_SESSION["error"] = "This child profile is not linked to your account";
        header("Location: ../../View/Page/LoginChild.php");
        exit;
      }

      ChildModel::updatePin($childId, (int) $pin);
      $_SESSION["success"] = "The pin was updated";
      header("Location: ../../View/Page/LoginChild.php");
      exit;
    } catch (PDOException $e) {
      $_SESSION["error"] = "Error with database";
      header("Location: ../../View/Page/ForgotPin.php?child_id=" . $childId);
      exit;
    }
  }

  redirectToChildLogin();
?>
