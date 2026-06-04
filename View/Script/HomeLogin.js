/**
 * Redirects user to appropriate login page based on selected role 
 * @param {string} type 
 * @returns {void}
 */
function handleCard(type) {
  if (type === "parent") {
    window.location.href = "../../Controller/HomeLogin/HomeLogin.php?role=parent";
  } else if (type === "hero" || type === "child") {
    window.location.href = "../../Controller/HomeLogin/HomeLogin.php?role=child";
  }
}
