function handleCard(type) {
  if (type === "parent") {
    window.location.href = "LoginParent.html";
  } else if (type === "hero") {
    window.location.href = "LoginChild.html";
  }
}
