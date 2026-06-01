let selectedAvatar = {
  avatar: "icon-superhero",
  img: "../Assets/img/icon-superhero.svg",
  bg: "linear-gradient(135deg,#f4845f,#e8623a)",
};
let pinCode = "";
let selectedChildId = null;

function openModal() {
  document.getElementById("modalOverlay").classList.add("active");
  document.getElementById("profileNameInput").value = "";
  setTimeout(() => document.getElementById("profileNameInput").focus(), 100);
}

function closeModal(id) {
  document.getElementById(id).classList.remove("active");
}

function selectProfile(card, childId, name, img, bg) {
  if (card.id === "addCard") {
    return;
  }
  card.style.transform = "scale(0.92)";
  setTimeout(() => {
    card.style.transform = "";
  }, 180);
  setTimeout(() => openPinScreen(childId, name, img, bg), 200);
}

function openPinScreen(childId, name, img, bg) {
  selectedChildId = childId;
  pinCode = "";
  updateDots();
  document.getElementById("pinName").textContent = "Hi, " + name + "!";
  const av = document.getElementById("pinAvatar");
  av.style.background = bg;
  av.innerHTML = `<img src="${img}" alt="" style="width:60%;height:60%;object-fit:contain;">`;
  document.getElementById("forgotPinLink").href = "ForgotPin.php?child_id=" + childId;
  document.getElementById("pinScreen").classList.add("active");
  const card = document.getElementById("pinCard");
  card.style.animation = "none";
  requestAnimationFrame(() => {
    card.style.animation = "";
  });
}

function pressKey(digit) {
  if (pinCode.length >= 4) {
    return;
  }
  pinCode += digit;
  updateDots();
  if (pinCode.length === 4) {
    setTimeout(checkPin, 150);
  }
}

function deleteKey() {
  pinCode = pinCode.slice(0, -1);
  updateDots();
}

function updateDots() {
  for (let i = 0; i < 4; i++) {
    const dot = document.getElementById("dot" + i);
    dot.classList.toggle("filled", i < pinCode.length);
    dot.classList.remove("error");
  }
}

async function checkPin() {
  const body = new URLSearchParams();
  body.append("child_id", selectedChildId);
  body.append("pin", pinCode);

  try {
    const res = await fetch("../../Controller/Authentication/ChildAuth.php?action=login", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body,
    });
    const data = await res.json();

    if (data.success) {
      for (let i = 0; i < 4; i++) {
        const dot = document.getElementById("dot" + i);
        dot.style.background = "#2cbfb1";
      }
      setTimeout(() => {
        window.location.href = data.redirect;
      }, 300);
      return;
    }
  } catch (err) {
    console.error("Pin login error:", err);
  }

  showPinError();
}

function showPinError() {
  for (let i = 0; i < 4; i++) {
    document.getElementById("dot" + i).classList.add("error");
  }
  document.getElementById("pinCard").classList.add("shake");
  setTimeout(() => {
    document.getElementById("pinCard").classList.remove("shake");
    pinCode = "";
    updateDots();
  }, 500);
}

document.addEventListener("keydown", (e) => {
  if (!document.getElementById("pinScreen").classList.contains("active")) {
    return;
  }
  if (e.key >= "0" && e.key <= "9") {
    pressKey(e.key);
  }
  if (e.key === "Backspace") {
    deleteKey();
  }
  if (e.key === "Escape") {
    closeModal("pinScreen");
  }
});

function pickAvatar(el) {
  document
    .querySelectorAll(".avatar-option")
    .forEach((a) => a.classList.remove("selected-avatar"));
  el.classList.add("selected-avatar");
  selectedAvatar = {
    avatar: el.dataset.avatar,
    img: el.querySelector("img").src,
    bg: el.style.background,
  };
  document.getElementById("selectedAvatarInput").value = selectedAvatar.avatar;
}

function addProfile() {
  const name = document.getElementById("profileNameInput").value.trim();
  if (!name) {
    document.getElementById("profileNameInput").style.borderColor = "#e57373";
    setTimeout(
      () =>
        (document.getElementById("profileNameInput").style.borderColor = ""),
      800,
    );
    return;
  }
  const age = document.getElementById("age");
  const secretPin = document.getElementById("secretPin");
  const confirmSecretPin = document.getElementById("confirmSecretPin");

  if (isNaN(secretPin.value) || secretPin.value === "") {
    secretPin.style.borderColor = "#e57373";
    return;
  }

  if (isNaN(age.value.trim()) || age.value === "") {
    age.style.borderColor = "#e57373";
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

  document.querySelector(".modal-box").requestSubmit();
}
document.getElementById("profileNameInput").addEventListener("keydown", (e) => {
  if (e.key === "Enter") {
    addProfile();
  }
  if (e.key === "Escape") {
    closeModal("modalOverlay");
  }
});
