function togglePassword() {
  const input = document.getElementById("passwordInput");
  const btn = document.getElementById("toggleBtn");
  if (input.type === "password") {
    input.type = "text";
    btn.src = "../Assets/img/icon-eye-closed.png";
    btn.alt = "Hide password";
  } else {
    input.type = "password";
    btn.src = "../Assets/img/icon-eye-open.png";
    btn.alt = "Show password";
  }
}
function setNewPin() {
  const secretPin = document.getElementById("secretPin");
  const confirmSecretPin = document.getElementById("confirmSecretPin");
  if (isNaN(secretPin.value) || secretPin.value === "") {
    secretPin.style.borderColor = "#e57373";
    return;
  }
  if (isNaN(confirmSecretPin.value) || confirmSecretPin.value === "") {
    confirmSecretPin.style.borderColor = "#e57373";
    return;
  }

  if (secretPin.value !== confirmSecretPin.value) {
    secretPin.style.borderColor = "#e57373";
    confirmSecretPin.style.borderColor = "#e57373";
    return;
  }
  window.location.href = "LoginChild.html";
}
