let group_form = document.querySelector(".groupForm");
let input = group_form.password;
let digit = document.querySelector(".digit");
let uppercase = document.querySelector(".uppercase");
let lowercase = document.querySelector(".lowercase");
let length = document.querySelector(".length");


let validator = document.querySelector(".validatorCriters");

input.addEventListener("input", function(){
  console.log(input)
    if (this.value && validator.style.display !== "block") {
        validator.style.display = "block";
    }
    validation(this);
    if(!this.value){
        remove();
        validator.style.display = "none";
    }
});

function validation(password) {
    const val = password.value;
    const allValid = 
        /[0-9]/.test(val) &&
        /[A-Z]/.test(val) &&
        /[a-z]/.test(val) &&
        val.length >= 8;

    if (/[0-9]/.test(val)) {
        digit.classList.add("success");
        digit.classList.remove("error");
    } else {
        digit.classList.add("error");
        digit.classList.remove("success");
    }

    if (/[A-Z]/.test(val)) {
        uppercase.classList.add("success");
        uppercase.classList.remove("error");
    } else {
        uppercase.classList.add("error");
        uppercase.classList.remove("success");
    }

    if (/[a-z]/.test(val)) {
        lowercase.classList.add("success");
        lowercase.classList.remove("error");
    } else {
        lowercase.classList.add("error");
        lowercase.classList.remove("success");
    }

    if (val.length >= 8) {
        length.classList.add("success");
        length.classList.remove("error");
    } else {
        length.classList.add("error");
        length.classList.remove("success");
    }

    if (allValid) {
        input.classList.add("success");
        input.classList.remove("invalide");
    } else {
        input.classList.add("invalide");
        input.classList.remove("success");
    }
}
function remove(){
    input.classList.remove("invalide");
    input.classList.remove("success");

    digit.classList.remove("error");
    digit.classList.remove("success");

    uppercase.classList.remove("success");
    uppercase.classList.remove("error");

    lowercase.classList.remove("success");
    lowercase.classList.remove("error")

    length.classList.remove("success")
    length.classList.remove("error");
}