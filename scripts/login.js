const element = (id) => {
  return document.querySelector(id);
};

const elementAll = (id) => {
  return document.querySelectorAll(id);
};

// Create an object to store error messages
const errorMsg = {
  emptyEmail: "Please enter your email address",
  invalidEmail: "Invalid email address",
  emptyPassword: "Please enter your password",
  invalidPassword: "Password must include atleast 1 letter and 1 number",
};

const regEx = {
  email: /\S+@\S+\.\S+/,
  password: /^(?=.*\d)(?=.*[a-zA-Z]).{8,}$/,
};

const handler = (event) => {
  let input = event.target;
  let error = input.nextElementSibling.lastElementChild;
  let passValue = element("#password");

  let addOnFocus = () => {
    input.classList.remove("error");
    input.classList.add("focus");
    input.nextElementSibling.style.display = "none";
    input.style.marginBottom = "15px";
  };

  let addFocusOut = () => {
    input.classList.add("error");
    input.nextElementSibling.style.display = "block";
    input.style.marginBottom = "0px";
  };

  if (event.type === "focus") {
    addOnFocus();
  } else if (event.type === "blur") {
    if (input.value === "" && input.name === "email") {
      addFocusOut();
      error.innerHTML = errorMsg.emptyEmail;
    } else if (input.name === "email" && !input.value.match(regEx.email)) {
      addFocusOut();
      error.innerHTML = errorMsg.invalidEmail;
    } else if (input.value === "" && input.name === "password") {
      addFocusOut();
      error.innerHTML = errorMsg.emptyPassword;
    } else if (
      input.name === "password" &&
      !input.value.match(regEx.password)
    ) {
      addFocusOut();
      error.innerHTML = errorMsg.invalidPassword;
    } else {
      input.classList.remove("focus");
    }
  }
};

elementAll(".userInputLog").forEach((input) => {
  input.addEventListener("blur", handler);
  input.addEventListener("focus", handler);
});

// Validate user input on submit
element("#loginForm").addEventListener("submit", (event) => {
  let email = element("#email");
  let password = element("#password");

  // Add CSS to input fields
  let displayError = (input) => {
    return [
      input.classList.add("error"),
      (input.nextElementSibling.style.display = "block"),
      (input.style.marginBottom = "0px"),
    ];
  };

  // Check email
  if (email.value === "") {
    displayError(email);
    email.nextElementSibling.lastElementChild.innerHTML = errorMsg.emptyEmail;
    event.preventDefault();
  } else {
    if (!email.value.match(regEx.email)) {
      displayError(email);
      email.nextElementSibling.lastElementChild.innerHTML =
        errorMsg.invalidEmail;
      event.preventDefault();
    }
  }

  // Check password
  if (password.value === "") {
    displayError(password);
    password.nextElementSibling.lastElementChild.innerHTML =
      errorMsg.emptyPassword;
    event.preventDefault();
  } else {
    if (!password.value.match(regEx.password)) {
      displayError(password);
      password.nextElementSibling.lastElementChild.innerHTML =
        errorMsg.invalidPassword;
      event.preventDefault();
    }
  }

  return true;
});

document.onreadystatechange = function () {
  let state = document.readyState;
  if (state == "interactive") {
    element("body").style.visibility = "hidden";
    element("#loader").style.visibility = "visible";
  } else if (state == "complete") {
    element("#loader").style.visibility = "hidden";
    element("body").style.visibility = "visible";
  }
};
