<?php 
  session_start();
  require_once("../../Model/ParentModel.php");
  if(!empty($_POST)){
    $email = $_POST["email"] ?? $_SESSION["parent"]["email"];
    $expiricy = date("Y-m-d H:i:s", time() + 60*30);
    $token = ParentModel::generateResetToken($email, $expiricy);
    if ($token) {
      $link = "http://localhost/View/Page/setNewPassword.php?token=$token";
      $destinataire = $email;
      $sujet = "Reset your Nevo password";
      $message = "<html><body style=\"margin:0; padding:0; font-family:'Nunito',Arial,sans-serif; background:linear-gradient(135deg,#c8e6c9 0%,#e8f5e9 15%,#fffde7 35%,#fff8e1 55%,#ffe0b2 75%,#ffccbc 100%); min-height:100vh;\">";
      $message .= '
        <div style="max-width:480px; margin:40px auto; padding:24px;">
          <div style="text-align:center; margin-bottom:24px;">
            <div style="display:inline-flex; align-items:center; gap:10px;">
              <span style="font-family:\'Baloo 2\',Arial,cursive; font-size:1.35rem; font-weight:800; color:#000;">Nevo</span>
            </div>
          </div>
          <div style="background:rgba(255,255,255,.88); border:1.5px solid rgba(255,255,255,.95); border-radius:22px; padding:44px 40px 40px; box-shadow:0 10px 40px rgba(0,0,0,.09),0 2px 8px rgba(0,0,0,.04);">

            <h1 style="font-family:\'Baloo 2\',Arial,cursive; font-size:1.7rem; font-weight:800; color:#000; text-align:center; margin:0 0 8px 0;">Reset your password</h1>
            <p style="font-size:.875rem; color:#888; text-align:center; font-weight:600; margin:0 0 32px 0;">A request was made to reset your password</p>

            <p style="font-size:.95rem; font-weight:700; color:#000; margin:0 0 12px 0;">Hello,</p>
            <p style="font-size:.95rem; font-weight:600; color:#444; margin:0 0 12px 0;">We received a request to reset your password. Click the button below, this link is valid for <strong>30 minutes</strong>.</p>
            <p style="font-size:.95rem; font-weight:600; color:#444; margin:0 0 32px 0;">If you did not make this request, simply ignore this email.</p>

            <div style="text-align:center; margin-bottom:32px;">
              <a href="' . $link . '" style="display:inline-block; background:linear-gradient(135deg,#3dbfa0,#2a9d8f); color:#fff; border-radius:50px; padding:13px 36px; font-family:\'Nunito\',Arial,sans-serif; font-size:1rem; font-weight:800; text-decoration:none; box-shadow:0 4px 18px rgba(61,191,160,.38);">
              Reset my password →
              </a>
            </div>

            <div style="border-top:1.5px solid #eee; margin:0 0 24px 0;"></div>

            <p style="font-size:.82rem; font-weight:600; color:#aaa; text-align:center; margin:0 0 16px 0;">Need help? Contact us at <a href="mailto:nevo@gmail.com" style="color:#3dbfa0; text-decoration:none; font-weight:700;">nevo@gmail.com</a></p>
          </div>
          <p style="font-size:.75rem; color:#aaa; text-align:center; margin-top:16px; font-weight:600;">© 2026 Nevo. All rights reserved.</p>
        </div>
      ';
      $message .= "</body></html>";
      $headers  = "From: integeek789@gmail.com\r\n";
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-type: text/html; charset=UTF-8\r\n";

      if (mail($destinataire, $sujet, $message, $headers)) {
        $_SESSION["success"] = "The email was sent successfully";
        header("Location: ../../View/Page/ForgotPassword.php");
        exit;
      } else {
        $_SESSION["error"] = "The email could not be sent";
        header("Location: ../../View/Page/ForgotPassword.php");
        exit;
      }
    }
  } else {
    $_SESSION["error"] = "The form is incomplete";
    header("Location: ../../view/Page/ForgotPassword.php");
    exit;
  }
?>