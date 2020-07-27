("use strict");

// Shorthand for dom element
const element = (id) => {
  return document.querySelector(id);
};

const elementAll = (id) => {
  return document.querySelectorAll(id);
};

// Load animation on page load
const loadJourney = () => {
  setTimeout(function () {
    elementAll(".journeyLoad").forEach((loader) => {
      loader.style.visibility = "hidden";
    });
    elementAll(".serviceType").forEach((content) => {
      content.style.visibility = "visible";
    });
  }, 500);
};
loadJourney();

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

const [
  pickUpLocationInput,
  dropOffLocationInput,
  dateInput,
  timeInput,
  dateValidationMsg,
  timeValidationMsg,
  journeyDetail,
] = [
  element("#pickUpLocation"),
  element("#dropOffLocation"),
  element("#date"),
  element("#time"),
  element("#dateValid"),
  element("#timeValid"),
  element(".journeyMain"),
];

const errorMsg = {
  pickEmpty: "Please enter pickup address",
  dropEmpty: "Please enter dropoff address",
  tripDate: {
    empty: "Please select the date",
    yearError: "Please check the year",
    monthError: "Please check the month",
    dateError: "Please check the date",
  },
  timeEmpty: "Please select the time",
};

//Smooth scroll
const scrollTo = (elem) => {
  window.scroll({
    behavior: "smooth",
    left: 0,
    top: elem.offsetTop,
  });
};

//Change navigation icon style
const navToggle = (navIcon) => navIcon.classList.toggle("crossIcon");

const navButton = element("#navToggle");
navButton.onclick = function () {
  navToggle(this);
};

const zoomOut = element("#zoomOutDiv");
navButton.addEventListener("mouseenter", () =>
  zoomOut.classList.add("navInnerContainer-hover")
);

navButton.addEventListener("mouseleave", () =>
  zoomOut.classList.remove("navInnerContainer-hover")
);

//Autocomplete the address fields using google Autocomplete places API

let selected = false;
function initAutocomplete() {
  // Find pickup lacation
  pickUpLocation = new google.maps.places.Autocomplete(pickUpLocationInput, {
    //Return only geocoding results and restrict to UK address
    types: ["geocode"],
    componentRestrictions: {
      country: ["gb"],
    },
  });

  // Find dropoff lacation
  dropOffLocation = new google.maps.places.Autocomplete(dropOffLocationInput, {
    //Return only geocoding results and restrict to UK address
    types: ["geocode"],
    componentRestrictions: {
      country: ["gb"],
    },
  });
}

// Handle input data
const handler = (event) => {
  let input = event.target;
  let errorDisplay = input.parentElement.nextElementSibling;
  let inputMargin = input.parentElement.parentElement;

  // Add CSS to input fields on focus
  let addOnFocus = () => {
    input.classList.remove("error");
    input.classList.add("focus");
    errorDisplay.style.display = "none";
    inputMargin.style.marginBottom = "15px";
  };

  // Add CSS to input fields on focusout
  let addFocusOut = (msg) => {
    input.classList.add("error");
    errorDisplay.style.display = "block";
    inputMargin.style.marginBottom = "3px";
    return (input.parentElement.nextElementSibling.lastElementChild.innerHTML = msg);
  };

  // Add focus class on focus
  if (event.type == "focus") {
    addOnFocus();

    // Check name on focusout
  } else if (event.type == "blur") {
    if (input.value === "" && input.id === "pickUpLocation") {
      addFocusOut(errorMsg.pickEmpty);
    } else if (input.value === "" && input.id === "dropOffLocation") {
      addFocusOut(errorMsg.dropEmpty);
    } else if (input.value === "" && input.id === "time") {
      addFocusOut(errorMsg.timeEmpty);
    } else {
      input.classList.remove("focus");
    }
  }
};

// Add event handler to input fields
document.querySelectorAll(".userInputAddress, .userInput").forEach((input) => {
  input.addEventListener("focus", handler);
  input.addEventListener("blur", handler);
});

// Clear input field if not selected from dropdown
$(".userInputAddress")
  .on("focus", function () {
    selected = false;
  })
  .on("blur", function () {
    if (!selected) {
      $(this).val("");
    }
  });

// Validate date function
const validateDate = () => {
  // Add CSS to input fields on focusout
  let addFocusOut = (msg) => {
    dateInput.classList.add("error");
    dateInput.parentElement.nextElementSibling.style.display = "block";
    return (dateInput.parentElement.nextElementSibling.lastElementChild.innerHTML = msg);
  };

  // Remove CSS to input fields on success
  let removeError = () => {
    dateInput.classList.remove("error");
    dateInput.parentElement.nextElementSibling.style.display = "none";
    dateInput.parentElement.nextElementSibling.lastElementChild.innerHTML = "";
  };

  let [datePicker, today, dateValue] = [
    new Date(dateInput.value),
    new Date(),
    dateInput.value,
  ];

  // Assign variables
  let [datePickerYear, datePickerMonth, datePickerDate] = [
    datePicker.getFullYear(),
    datePicker.getMonth(),
    datePicker.getDate(),
  ];

  let [currentYear, currentMonth, currentDate] = [
    today.getFullYear(),
    today.getMonth(),
    today.getDate(),
  ];

  // Check the field if it is empty
  if (dateValue === "") {
    addFocusOut(errorMsg.tripDate.empty);
  }

  // Check the year valid
  else if (datePickerYear < currentYear) {
    addFocusOut(errorMsg.tripDate.yearError);
  }

  // Check the year and month valid
  else if (datePickerYear <= currentYear && datePickerMonth < currentMonth) {
    addFocusOut(errorMsg.tripDate.monthError);
  }

  // Check the year, month, and date valid
  else if (
    datePickerYear <= currentYear &&
    datePickerMonth <= currentMonth &&
    datePickerDate < currentDate
  ) {
    addFocusOut(errorMsg.tripDate.dateError);
  } else {
    removeError();
  }
};

// Bind two events to date input element
["focusout", "change"].forEach(function (event) {
  dateInput.addEventListener(event, validateDate, false);
});

// Set date placeholder
dateInput.placeholder = `e.g: ${new Date().toLocaleDateString()}`;

// Add time option to time field
let halfHour = ["00", "30"];
for (let i = 0; i < 24; i++) {
  if (i < 10) {
    i = "0" + i;
  }
  for (let j = 0; j < 2; j++) {
    timeInput.add(new Option(i + ":" + halfHour[j]));
  }
}
// Add error css
const addError = (input, msg) => {
  return [
    input.classList.add("error"),
    (input.parentElement.nextElementSibling.style.display = "block"),
    (input.parentElement.parentElement.style.marginBottom = "3px"),
    (input.parentElement.nextElementSibling.lastElementChild.innerHTML = msg),
  ];
};
// Remove error css
const removeError = (input) => {
  return [
    input.classList.remove("error"),
    (input.parentElement.nextElementSibling.style.display = "none"),
    (input.parentElement.parentElement.style.marginBottom = "15px"),
    (input.parentElement.nextElementSibling.lastElementChild.innerHTML = ""),
  ];
};

// Validate input fields on submit button click
element("#getQuote").addEventListener("click", (event) => {
  if (
    pickUpLocationInput.value &&
    dropOffLocationInput.value &&
    dateInput.value &&
    timeInput.value !== "" &&
    dateValidationMsg.innerHTML === "" &&
    timeValidationMsg.innerHTML === ""
  ) {
    getDistance();
    journeyDetail.style.display = "block";
    scrollTo(element(".journeyMain"));
  } else {
    if (pickUpLocationInput.value === "") {
      addError(pickUpLocationInput, errorMsg.pickEmpty);
    }

    if (dropOffLocationInput.value === "") {
      addError(dropOffLocationInput, errorMsg.dropEmpty);
    }

    if (timeInput.value === "") {
      addError(timeInput, errorMsg.timeEmpty);
    } else {
      removeError(timeInput);
    }

    if (journey) {
      journeyDetail.style.display = "block";
    } else {
      journeyDetail.style.display = "none";
    }

    validateDate();
  }
});

//Find distance between 2 addresses using google API distance matrix service
const getDistance = () => {
  let service = new google.maps.DistanceMatrixService();

  service.getDistanceMatrix(
    {
      origins: [pickUpLocationInput.value],
      destinations: [dropOffLocationInput.value],
      travelMode: google.maps.TravelMode.DRIVING,
      unitSystem: google.maps.UnitSystem.METRIC,
      durationInTraffic: true,
      avoidHighways: false,
      avoidTolls: false,
    },

    (response, status) => {
      if (status !== google.maps.DistanceMatrixStatus.OK) {
        alert`(Error: ${status})`;
      } else {
        // Get distance value in metres
        let distanceInMetre = response.rows[0].elements[0].distance.value;

        // Change metres to miles and round miles to 2nd decimel point
        let distanceInMile = ((distanceInMetre / 1000) * 0.621371).toFixed(1);

        // Find the time duration
        let travelDuration = response.rows[0].elements[0].duration.text;

        // Find departure address
        let depatureFullAddress = response.originAddresses[0];

        // Find arrival address
        let arrivalFullAddress = response.destinationAddresses[0];

        // Change travel duration in minutes
        let durationInMinute = 0;
        durationInMinute = response.rows[0].elements[0].duration.value / 60;

        // Create object to store data in localstorage
        let travelDetail = {
          originAddress: depatureFullAddress,
          destinationAddress: arrivalFullAddress,
          distanceMetre: distanceInMetre,
          distanceMile: distanceInMile,
          duration: travelDuration,
          durationMinute: durationInMinute,
          date: dateInput.value,
          time: timeInput.value,
          storageTime: new Date().getTime(),
        };

        // Check browser support for Web Storage
        if (typeof Storage !== "undefined") {
          let isStorage = localStorage.getItem("travelInfo");
          if (isStorage === null) {
            localStorage.setItem("travelInfo", JSON.stringify(travelDetail));
            window.location.reload();
          } else {
            localStorage.clear();
            localStorage.setItem("travelInfo", JSON.stringify(travelDetail));
            window.location.reload();
          }
        } else {
          alert("Sorry, your browser does not support Web Storage");
        }
      }
    }
  );
};

// Get local storage data
const journey = JSON.parse(localStorage.getItem("travelInfo"));

// Set expiry time for local storage
window.addEventListener("load", () => {
  let currentTime = new Date().getTime();
  if (journey) {
    let storageStartTime = journey.storageTime;
    let storageDuration = Math.floor((currentTime - storageStartTime) / 60000);
    if (storageDuration >= 5) {
      localStorage.clear();
      window.location.reload();
      journeyDetail.style.display = "none";
      //alert("Time out");
    }
  }
});

// Find final fare and display type of services
const url =
  "https://raw.githubusercontent.com/Karan-Satha/fare/master/taxi.json";

const displayServiceInfo = () => {
  fetch(url)
    .then((response) => response.json())
    .then((fare) => {
      let totalFare = fare.map((fare) => {
        let [farePerMinute, farePerMile, baseFare, minimumFare] = [
          fare.farePerMinute,
          fare.farePerMile,
          fare.baseFare,
          fare.minimumFare,
        ];

        if (journey) {
          let finalFare = (
            farePerMinute * journey.durationMinute +
            farePerMile * journey.distanceMile +
            baseFare +
            minimumFare
          ).toFixed(0);
          return `<div class="serviceType">
    <h1 class="fare" id="${fare.id}">£${finalFare}</h1>
    <img class="vehicleImage" src=${fare.imgUrl} />

    <div class="serviceInfo">
    <h1>${fare.carType}</h1>
    <p>${fare.carDescription}</p>
    <div class="buttonContainer">
    <div>
    <i class="fas fa-user-friends"><span> ${fare.person}</span></i>
    <i class="fas fa-suitcase"><span> ${fare.luggage}</span></i>
    </div>
    <form method="post">
    <button class="selectFareBtn" id=${fare.id} onClick="selectService(this)" name=${fare.id} type="submit">SELECT FARE</button>
    </form>
    </div>
    </div>
    </div>`;
        }
      });

      element("#serviceX").innerHTML += totalFare[0];
      element("#serviceXL").innerHTML += totalFare[1];
      element("#serviceXXL").innerHTML += totalFare[2];
    })
    .catch((error) => {
      alert("Request failed", error);
    });
};

// Display user entered data

const displayTravelInfo = () => {
  // Check session data is available
  if (journey) {
    // Display data
    element("#depatureFullAddress").innerHTML = journey.originAddress;
    element("#arrivalFullAddress").innerHTML = journey.destinationAddress;
    element("#distanceMile").innerHTML = `${journey.distanceMile} miles`;
    element("#duration").innerHTML = journey.duration;
    element("#dateDisplay").innerHTML = new Date(journey.date).toDateString();
    element("#timeDisplay").innerHTML = journey.time;

    displayServiceInfo();
  } else {
    //alert("There is no storage available");
    journeyDetail.style.display = "none";
  }
};

displayTravelInfo();

// Select service type and pass details to next page
const selectService = (button) => {
  // Get JSON file from GitHub using fetch API
  fetch(url)
    .then((response) => response.json())
    .then((fare) => {
      fare.map((service) => {
        if (parseInt(service.id) === parseInt(button.id)) {
          let serviceFare = document.getElementsByClassName("fare")[
            service.id - 1
          ];
          // Get the existing data and add more object properties
          if (journey) {
            journey["fare"] = serviceFare.innerHTML;
            journey["people"] = service.person;
            journey["carType"] = service.carType;
            journey["luggage"] = service.luggage;
            journey["imgUrl"] = service.imgUrl;
            // Update local storage
            localStorage.setItem("travelInfo", JSON.stringify(journey));
            window.location.href = "booking.php";
          }
        }
      });
    })
    .catch((error) => {
      alert("Request failed", error);
    });
};

//Clear textbox on times button click
element("#clearButtonPick").addEventListener(
  "click",
  () => (pickUpLocationInput.value = "")
);

element("#clearButtonDrop").addEventListener(
  "click",
  () => (dropOffLocationInput.value = "")
);

element("#clearButtonDate").addEventListener(
  "click",
  () => (dateInput.value = "")
);

element("#clearButtonTime").addEventListener(
  "click",
  () => (timeInput.value = "")
);

element(".editQuoteBtn").addEventListener("click", () => {
  scrollTo(element(".homeMain"));
  pickUpLocationInput.value = element("#depatureFullAddress").innerHTML;
  dropOffLocationInput.value = element("#arrivalFullAddress").innerHTML;
});
