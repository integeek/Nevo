<?php 
  session_start();
  require_once("../../Model/ParentModel.php");

  if(!empty($_POST)){
    if(isset($_POST["email"],$_POST["password"]) && !empty(($_POST["email"]) && !empty($_POST["password"]))) {
      if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $_SESSION["error"] = "The email address is incorrect";
        header("Location: ../../View/Page/LoginParent.php");
        exit;
      }

      $parent = ParentModel::login($_POST["email"]);
      if(!$parent){
        $_SESSION["error"] = "The username and/or password does not exist";
        header("Location: ../../View/Page/LoginParent.php");
        exit;
      }

      if(!password_verify($_POST["password"], $parent["password"])){
        $_SESSION["error"] = "The username and/or password does not exist.";
        header("Location: ../../View/Page/LoginParent.php");
        exit;
      }

      $_SESSION["parent"] = [
        "id" => $parent["id"],
        "email" => $parent["email"],
        "fullname" => $parent["fullname"]
      ];

      if (($_SESSION["selectedRole"] ?? "") === "child") {
        unset($_SESSION["selectedRole"]);
        header("Location: ../../View/Page/LoginChild.php");
        exit;
      }

      header("Location: ../../View/Page/ParentDashboard.php");
      exit;

    } else {
      $_SESSION["error"] = "The form is incomplete";
      header("Location: ../../View/Page/LoginParent.php");
      exit;
    }
  }
?>
