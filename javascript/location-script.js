$(window).on('load', function(){
    navigator.geolocation.getCurrentPosition(success, error);
    setSectionHeight();


    function success(pos) {
        let lat = pos.coords.latitude;
        let long = pos.coords.longitude;
        loadLocation(lat, long);
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
$(window).on('resize', function(){
    setSectionHeight();
});
function loadLocation(lat, long){
    let date = new Date();
    $.getJSON(`api/location/?lat=${lat}&long=${long}&timestamp=${Date.now()/1000 - date.getTimezoneOffset()*60}&portal=location`, function(data) {
        console.log(data);
        display(data);
    });
}
function display(data){
    let lottieLocation = $("#location-lottie");
    (lottieLocation.get(0)).pause();
    lottieLocation.hide();
    $("#main-section").append(
        createInfoDiv("GPS súradnice(lat, long): ", data.lat+", "+data.long),
        createInfoDiv("Miesto: ", data.city),
        createInfoDiv("Krajina: ", data.state),
        createInfoDiv("Hlavné mesto: ", data.capital),
        createInfoDiv("Tvoja IP adresa je:", data.ipAddress),
        )
}

function createInfoDiv(info, data){
    let div  = document.createElement("div");
    $(div).html(`<strong>${info}</strong> <span>${data}</span>`);
    $(div).addClass("info-div");
    return div;
}
function setSectionHeight(){
    if($("body").width() > 768)
        $("#main-section").height((parseInt($("html").height())-parseInt($("footer").height())-parseInt($("#main-header").height()))+"px");
    else
        $("#main-section").height("auto");

}

