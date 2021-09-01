$(window).on("load", function () {
    let myMap = setMap();
    $("#stats-content").hide();

    navigator.geolocation.getCurrentPosition(success, error);

    function success(pos) {

        let lat = pos.coords.latitude;
        let long = pos.coords.longitude;
        saveLocation(lat, long, myMap);
    }

    function error() {
        let locationLottie = $("#location-lottie");
        locationLottie.get(0).pause();
        locationLottie.hide();
        let locationErrorLottie = $("#location-error-lottie");
        locationErrorLottie.show();
        $("#error-message").show();
        locationErrorLottie.get(0).play();
    }
});

function setMap() {

    let mymap = L.map('mapid').setView([49.4079, 19.4803], 13);

    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoidG9tYXMtcG9waWsiLCJhIjoiY2tudndqcW4xMHF5bTJ4bWtsYWwxdnBzNCJ9.jyEGKCGXsTZW4B9zWiDJUw', {
        maxZoom: 18,
        id: 'mapbox/streets-v11',
        tileSize: 512,
        zoomOffset: -1,
        accessToken: 'pk.eyJ1IjoidG9tYXMtcG9waWsiLCJhIjoiY2tudndqcW4xMHF5bTJ4bWtsYWwxdnBzNCJ9.jyEGKCGXsTZW4B9zWiDJUw'
    }).addTo(mymap);

    return mymap;
}

function saveLocation(lat, long, myMap) {
    let date = new Date();
    $.getJSON(`api/location/?lat=${lat}&long=${long}&timestamp=${Date.now() / 1000 - date.getTimezoneOffset() * 60}&portal=stats`, function () {
        getGPS(myMap).then(r => getUserAttendance().then(r=> getWebsiteAttendance().then(r=> getHourAttendance().then(r=>showContent()))));
    });
}

function showContent() {
    let lottieLocation = $("#location-lottie");
    (lottieLocation.get(0)).pause();
    lottieLocation.hide();
    $("#stats-content").show()

}

async function getGPS(myMap) {
    $.getJSON(`api/stats/gps/`, function (data) {
        printToMap(data, myMap);
    });
}

async function getUserAttendance() {
    $.getJSON(`api/stats/country/`, function (data) {
        printUserAttendance(data);
    });
}

async function getWebsiteAttendance() {
    $.getJSON(`api/stats/website/`, function (data) {
        printWebsiteAttendance(data);
    });
}

function printToMap(gps, myMap) {
    let markers = L.featureGroup();
    $.each(gps, function () {
        let coordinates = this;
        let marker = L.marker(coordinates).addTo(myMap);
        marker.addTo(markers);
    });
    myMap.fitBounds(markers.getBounds());
}

function printWebsiteAttendance(websites) {
    $.each(websites, function () {
        let tableRow = createTr($("#website-attendance"));
        let website = this;
        let portal = "";
        if (website.portal === "weather")
            portal = "Počasie"
        else if (website.portal === "location")
            portal = "Poloha"
        else if (website.portal === "stats")
            portal = "Štatistika"
        tableRow.append(createTh(portal), createTd(website.attendance));
    })
}

async function getHourAttendance() {
    $.getJSON(`api/stats/hour/`, function (data) {
        printHourAttendance(data);
    });
}

function printHourAttendance(hours) {
    let tbody = $("#hours-attendance");
    let tableRow = createTr(tbody);
    tableRow.append(createTh("00:00 - 05:59"), createTd(hours.hour00));
    tableRow = createTr(tbody);
    tableRow.append(createTh("06:00 - 14:59"), createTd(hours.hour06));
    tableRow = createTr(tbody);
    tableRow.append(createTh("15:00 - 20:59"), createTd(hours.hour15));
    tableRow = createTr(tbody);
    tableRow.append(createTh("21:00 - 23:59"), createTd(hours.hour21));
}


function printUserAttendance(countries) {
    $.each(countries, function () {
        let tableRow = createTr($("#country-attendance"));
        let country = this;
        let countryTh = createTh(country.name + " " + createCountryFlag(country.code.toLowerCase(), 20));
        $(countryTh).on("click", () => showPlaceAttendanceInModal(country.name, country.code, country.places));
        $(countryTh).addClass("clickable-th");
        tableRow.append(countryTh, createTd(country.userCount));
    })
}

function createCountryFlag(countryCode, height) {
    return `<img src='https://www.geonames.org/flags/x/${countryCode}.gif' height=${height} alt='flag'>`
}

function createTh(text) {
    let th = document.createElement("th");
    $(th).html(text);
    return th;
}

function createTd(text) {
    let td = document.createElement("td");
    $(td).text(text);
    return td;
}

function createTr(tbody) {
    let tr = document.createElement("tr");
    tbody.append(tr);
    return tr;
}

function showPlaceAttendanceInModal(country, countryCode, places) {
    let tbody = $("#user-info-tbody");
    tbody.empty();
    $("#attendance-detail-title").html(createCountryFlag(countryCode.toLowerCase(), 30) + " " + country);
    $.each(places, function () {
        let tableRow = createTr(tbody);
        let place = this;
        tableRow.append(createTh(place.name), createTd(place.userCount));
    })
    showModal($('#attendance-detail'));
}

function showModal(modalToShow) {
    modalToShow.modal({
        keyboard: false
    });
}

