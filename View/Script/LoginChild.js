let selectedAvatar = {
  avatar: "icon-superhero",
  img: "../Assets/img/icon-superhero.svg",
  bg: "linear-gradient(135deg,#f4845f,#e8623a)",
};
let pinCode = "";
let selectedChildId = null;

/**
 * Opens add profile modal and focuses on name input field
 * Resets name input value to empty string
 * @returns {void}
 */
function openModal() {
  document.getElementById("modalOverlay").classList.add("active");
  document.getElementById("profileNameInput").value = "";
  setTimeout(() => document.getElementById("profileNameInput").focus(), 100);
}

/**
 * Closes specified modal by removing "active" class from its element
 * @param {string} id 
 * @returns {void}
 */
function closeModal(id) {
  document.getElementById(id).classList.remove("active");
}

/**
 * Handles clicking on child profile card by applying click animation and then opening PIN entry screen for that child
 * Does nothing if clicked card is "add profile" card
 * @param {HTMLElement} card 
 * @param {string} childId 
 * @param {string} name 
 * @param {string} img 
 * @param {string} bg 
 * @returns {void}
 */
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

/**
 * Opens PIN entry screen for selected child profile
 * @param {string} childId 
 * @param {string} name 
 * @param {string} img 
 * @param {string} bg 
 * @returns {void}
 */
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

/**
 * Adds a digit to PIN and triggers check when 4 digits are entered
 * @param {string} digit 
 * @returns {void}
 */
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

/**
 * Deletes last entered digit from PIN
 * @returns {void}
 */
function deleteKey() {
  pinCode = pinCode.slice(0, -1);
  updateDots();
}

/**
 * Updates visual representation of PIN entry dots based on how many digits have been entered
 * @returns {void}
 */
function updateDots() {
  for (let i = 0; i < 4; i++) {
    const dot = document.getElementById("dot" + i);
    dot.classList.toggle("filled", i < pinCode.length);
    dot.classList.remove("error");
  }
}

/**
 * Sends entered PIN to server for validation and redirects to appropriate page if PIN is correct, otherwise shows error animation
 * @returns {Promise<void>}
 */
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

/**
 * Shows error animation on PIN entry screen by shaking card and highlighting dots in red, then resets PIN code
 * @returns {void}
 */
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

/**
 * Handles avatar selection in add profile modal
 * @param {HTMLElement} el 
 * @returns {void}
 */
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

/**
 * Validates add profile form and submits it if everything is valid, otherwise highlights invalid fields with red border
 * Adds new child profile with provided details
 * @returns {void}
 */
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
