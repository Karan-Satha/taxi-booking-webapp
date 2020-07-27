<!DOCTYPE html>
<?php

// Start session
session_start();
$_SESSION["currentpage"] = $_SERVER['REQUEST_URI'];

// Set session
if (isset($_POST["1"])) {
    $_SESSION["package"] = "saloon";
} else if (isset($_POST["2"])) {
    $_SESSION["package"] = "6seater";
} else if (isset($_POST["3"])) {
    $_SESSION["package"] = "9seater";
}

?>
<html>

<head>
    <?php include "templates/head.php";?>
    <!-- Home css-->
    <link rel="stylesheet" href="stylesheets/taxihome.css" />
    <!-- Header css -->
    <link rel="stylesheet" href="stylesheets/header.css" />
</head>

<body>
    <div id="loader"></div>
    <main class="homeMain">
        <!-- Navigation section starts here -->
        <section>
            <article>
                <?php include "templates/header.php";?>
            </article>
        </section>
        <!--Home section starts here-->
        <section class="homeSection">
            <div class="homeMainContainer">
                <article>
                    <div class="getQuoteMainContainer">
                        <div class="getQuoteTopBanner">
                            <h3>Get your quote today</h3>
                        </div>
                        <!-- Form starts here -->
                        <form>
                            <!-- Pick up location input-->
                            <div class="mainInputContainer">
                                <p>From (Pickup Location)</p>
                                <div class="inputContainer" id="inputPickContainer">
                                    <div class="inputIconContainer">
                                        <i class="fas fa-search-location"></i>
                                    </div>
                                    <div id="clearButtonPick" class="removeIconContainer">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <input type="text" class="userInputAddress" id="pickUpLocation"
                                        placeholder="e.g: TW6 1EW or Heathrow Terminals 2" />
                                </div>
                                <div class="errorMsgHome">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <p></p>
                                </div>
                            </div>
                            <!-- Drop off location input -->
                            <div class="mainInputContainer">
                                <p>To (Dropoff Location)</p>
                                <div class="inputContainer" id="inputDropContainer">
                                    <div class="inputIconContainer">
                                        <i class="fas fa-search-location"></i>
                                    </div>
                                    <div id="clearButtonDrop" class="removeIconContainer">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <input type="text" class="userInputAddress" id="dropOffLocation"
                                        placeholder="e.g: HA2 8PW or Harrow" />
                                </div>
                                <div class="errorMsgHome">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <p></p>
                                </div>
                            </div>
                            <!-- Pickup date input -->
                            <div class="mainInputContainer">
                                <p>Pickup Date</p>
                                <div class="inputContainer" id="inputDateContainer">
                                    <div class="inputIconContainer">
                                        <i class="far fa-calendar-alt"></i>
                                    </div>
                                    <div id="clearButtonDate" class="removeIconContainer">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <input type="text" onfocus="(this.type='date')" class="userInput" id="date" />
                                </div>
                                <div class="errorMsgHome">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <p id="dateValid"></p>
                                </div>
                            </div>
                            <!-- Pickup time input -->
                            <div class="mainInputContainer">
                                <p>Pickup Time</p>
                                <div class="inputContainer" id="inputTimeContainer">
                                    <div class="inputIconContainer">
                                        <i class="far fa-clock"></i>
                                    </div>
                                    <div id="clearButtonTime" class="removeIconContainer">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <select class="userInput" id="time" required>
                                        <option value="" disabled selected hidden>e.g: 12:00</option>
                                    </select>
                                </div>
                                <div class="errorMsgHome">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <p id="timeValid"></p>
                                </div>
                            </div>
                        </form>
                        <!-- Submit button -->
                        <div class="getQuoteBtnContainer" id="getQuoteBtnCId">
                            <button id="getQuote" class="getQuoteButton">
                                GET QUOTE
                            </button>
                        </div>
                    </div>
                    <div class="homeMessageContainer">
                        <p>Reach your destination with our reliable, fast and professional
                            service.</p>
                    </div>
                </article>
            </div>
        </section>
    </main>
    <main class="journeyMain" id="journeyMain">
        <!-- Journey details start here  -->
        <div class="editDetailContainer">Journey details<button class="editQuoteBtn">EDIT QUOTE</button>
        </div>
        <section class="journeyMainSection">
            <article class="journeyDetailsDisplay" id="journeyDetailsDisplayId">
                <div class="timeLine"></div>
                <div class="distanceMarkerContainer">
                    <div class="car-side"></div>
                </div>
                <div class="journeyDetailsInnerC">
                    <!-- Start location details -->
                    <section class="journeyDetailDisplay">
                        <!-- <div class="journeyDetailHeading">Pickup address</div> -->
                        <div class="iconContainer">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="journeyDetailHeading">
                            <div>
                                <p>Pickup address</p>
                                <div id="depatureFullAddress"></div>
                            </div>
                        </div>
                    </section>
                    <!-- End location details  -->
                    <section class="journeyDetailDisplay">
                        <!-- <div class="journeyDetailHeading">Dropoff address</div> -->
                        <div class="iconContainer">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="journeyDetailHeading">
                            <div>
                                <p>Dropoff address</p>
                                <div id="arrivalFullAddress"></div>
                            </div>
                        </div>
                    </section>
                    <!-- Distance details  -->
                    <section class="distanceDisplay">
                        <!-- <div class="journeyDetailHeading">Estimated distance</div> -->
                        <div class="iconContainer">
                            <i class="fa fa-road"></i>
                        </div>
                        <div class="journeyDetailHeading">
                            <div>
                                <p>Estimated distance</p>
                                <div id="distanceMile"></div>
                            </div>
                        </div>
                        <div id="distanceKm"></div>
                    </section>
                    <!-- Time duration details  -->
                    <section class="journeyDetailDisplay">
                        <!-- <div class="journeyDetailHeading">Estimated duration</div> -->
                        <div class="iconContainer">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="journeyDetailHeading">
                            <div>
                                <p>Estimated duration</p>
                                <div id="duration"></div>
                            </div>
                        </div>
                    </section>
                    <!-- Travel date details  -->
                    <section class="journeyDetailDisplay">
                        <!-- <div class="journeyDetailHeading">Pickup date</div> -->
                        <div class="iconContainer">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="journeyDetailHeading">
                            <div>
                                <p>Pickup date</p>
                                <div id="dateDisplay"></div>
                            </div>
                        </div>
                    </section>
                    <!-- Travel time details  -->
                    <section class="journeyDetailDisplay">
                        <!-- <div class="journeyDetailHeading">Pickup time</div> -->
                        <div class="iconContainer">
                            <i class="fas fa-business-time"></i>
                        </div>
                        <div class="journeyDetailHeading">
                            <div>
                                <p>Pickup time</p>
                                <div id="timeDisplay"></div>
                            </div>
                        </div>
                    </section>
                </div>
            </article>
            <article class="selectServiceContainer">
                <!-- <div id="journeyLoad"></div> -->
                <section id="packageInfo">
                    <article id="serviceX">
                        <div class="journeyLoad">
                    </article>
                    <article id="serviceXL">
                        <div class="journeyLoad">
                    </article>
                    <article id="serviceXXL">
                        <div class="journeyLoad">
                    </article>
                </section>
            </article>
        </section>
    </main>

    <!--Call jquery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <!--Internal JavaScript file-->
    <script src="scripts/index.js"></script>

    <!--Call google map API service-->
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAg80Yk_ZM_7vlLgbL6e1kZjOjh6NZgHiU&libraries=places&callback=initAutocomplete"
        async defer></script>

</body>

</html>