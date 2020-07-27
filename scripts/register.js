const element = (id) => {
  return document.querySelector(id);
};

const elementAll = (id) => {
  return document.querySelectorAll(id);
};

// Create an object to store error messages
const errorMsg = {
  emptyName: "Please enter your name",
  invalidName: "Name must contain letters and space only",
  emptyEmail: "Please enter your email address",
  invalidEmail: "Invalid email address",
  emptyPhone: "Please enter your phone number",
  invalidPhone: "Invalid phone number",
  emptyPassword: "Please enter your password",
  invalidPassword: "Password must include atleast 1 letter and 1 number",
  emptyRePassword: "Please confirm your password",
  notMatchedPassword: "Password does not match",
};

const regEx = {
  name: /^[a-zA-Z-,]+\s[a-zA-Z-,]+(\s?)([a-zA-Z-,]?)+$/,
  email: /\S+@\S+\.\S+/,
  phone: /^[7]\d{8,9}$/,
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
    if (input.value === "" && input.name === "name") {
      addFocusOut();
      error.innerHTML = errorMsg.emptyName;
    } else if (input.name === "name" && !input.value.match(regEx.name)) {
      addFocusOut();
      error.innerHTML = errorMsg.invalidName;
    } else if (input.value === "" && input.name === "phone") {
      addFocusOut();
      error.innerHTML = errorMsg.emptyPhone;
    } else if (input.name === "phone" && !input.value.match(regEx.phone)) {
      addFocusOut();
      error.innerHTML = errorMsg.invalidPhone;
    } else if (input.value === "" && input.name === "email") {
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
    } else if (
      input.name === "confirmPassword" &&
      input.value !== passValue.value
    ) {
      addFocusOut();
      error.innerHTML = errorMsg.notMatchedPassword;
    } else {
      input.classList.remove("focus");
    }
  }
};

elementAll(".userInputReg").forEach((input) => {
  input.addEventListener("blur", handler);
  input.addEventListener("focus", handler);
});

// Get country code from gitHub using fetch API
const codeUrl =
  "https://raw.githubusercontent.com/Karan-Satha/country-code/master/code.json";

fetch(codeUrl)
  .then((response) => response.json())
  .then((code) => {
    code.forEach((code) => {
      let dataList = element("#codes");
      let option = document.createElement("option");
      option.value = `${code.code} - ${code.name}`;
      dataList.appendChild(option);
    });
  })
  .catch((error) => {
    alert("Request failed", error);
  });

// Get only country code number from dropdown on selection
element("#code").addEventListener("change", (event) => {
  let code = event.target.value.split(" ");
  event.target.value = code[0];
});

// Clear country code value on focus
element("#code").addEventListener("focus", (event) => {
  event.target.value = "";
});

// Make UK as default code
element("#code").addEventListener("focusout", (event) => {
  if (event.target.value == "") {
    event.target.value = "+44";
  }
});

// Validate user input on submit
element("#registerForm").addEventListener("submit", (event) => {
  let name = element("#name");
  let email = element("#email");
  let phone = element("#phone");
  let password = element("#password");
  let rePassword = element("#rePassword");

  // Add CSS to input fields
  let displayError = (input) => {
    return [
      input.classList.add("error"),
      (input.nextElementSibling.style.display = "block"),
      (input.style.marginBottom = "0px"),
    ];
  };

  // Check name
  if (name.value === "") {
    displayError(name);
    name.nextElementSibling.lastElementChild.innerHTML = errorMsg.emptyName;
    event.preventDefault();
  } else {
    if (!name.value.match(regEx.name)) {
      displayError(name);
      name.nextElementSibling.lastElementChild.innerHTML = errorMsg.invalidName;
      event.preventDefault();
    }
  }

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

  // Check phone number
  if (phone.value === "") {
    displayError(phone);
    phone.nextElementSibling.lastElementChild.innerHTML = errorMsg.emptyPhone;
    event.preventDefault();
  } else {
    if (!phone.value.match(regEx.phone)) {
      displayError(phone);
      phone.nextElementSibling.lastElementChild.innerHTML =
        errorMsg.invalidPhone;
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

  // Check repeat password
  if (rePassword.value === "") {
    displayError(rePassword);
    rePassword.nextElementSibling.lastElementChild.innerHTML =
      errorMsg.emptyRePassword;
    event.preventDefault();
  } else {
    if (rePassword.value !== password.value) {
      displayError(rePassword);
      rePassword.nextElementSibling.lastElementChild.innerHTML =
        errorMsg.notMatchedPassword;
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
