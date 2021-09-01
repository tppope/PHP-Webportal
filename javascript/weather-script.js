$(window).on('resize', function () {
    setSectionHeight();
});
$(window).on("load", function () {
    navigator.geolocation.getCurrentPosition(success, error);
    setSectionHeight();

    function success(pos) {
        let lat = pos.coords.latitude;
        let long = pos.coords.longitude;
        weather(lat, long);
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

    function weather(lat, long) {
        let date = new Date();
        $.getJSON(`api/location/?lat=${lat}&long=${long}&timestamp=${Date.now() / 1000 - date.getTimezoneOffset() * 60}&portal=weather`, function (data) {
            let URL = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${long}&appid=93e4625bc9fe7c718dfe8a36117b9565&units=metric`;
            $.getJSON(URL, function (dataWeather) {
                display(dataWeather);
            });
        });

    }

    function display(data) {

        let city = data.name.toUpperCase();
        let temp =
            Math.round(data.main.temp) +
            "&deg; C";
        let desc = data.weather[0].description;
        let date = new Date();

        let months = [
            "Január",
            "Február",
            "Marec",
            "Apríl",
            "Máj",
            "Jún",
            "Júl",
            "August",
            "September",
            "Október",
            "November",
            "December"
        ];

        let weekday = new Array(7);
        weekday[0] = "Nedeľa";
        weekday[1] = "Pondelok";
        weekday[2] = "Utorok";
        weekday[3] = "Streda";
        weekday[4] = "Štvrtok";
        weekday[5] = "Piatok";
        weekday[6] = "Sobota";

        let font_color;
        let bg_color;
        if (Math.round(data.main.temp) > 25) {
            font_color = "#d36326";
            bg_color = "#e8eab5";
        } else {
            font_color = "#44c3de";
            bg_color = "#a5bbdd";
        }

        $(".weathercon").html(
            "<img width='80px' src=\"https://openweathermap.org/img/wn/" + data.weather[0].icon + ".png\">"
        );


        let minutes =
            date.getMinutes() < 11 ? "0" + date.getMinutes() : date.getMinutes();
        date =
            weekday[date.getDay()].toUpperCase() +
            " | " +
            months[date.getMonth()].toUpperCase().substring(0, 3) +
            " " +
            date.getDate() +
            " | " +
            date.getHours() +
            ":" +
            minutes;
        $(".location").html(city);
        $(".temp").html(temp);
        $(".date").html(date);
        $(".box").css("background", bg_color);
        $(".location").css("color", font_color);
        $(".temp").css("color", font_color);
    }
});

function setSectionHeight() {
    if ($("body").width() > 768)
        $("#main-section").height((parseInt($("html").height()) - parseInt($("footer").height()) - parseInt($("#main-header").height())) + "px");
    else
        $("#main-section").height("auto");
}
