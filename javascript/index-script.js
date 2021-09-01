$(window).on("load", function() {
    setSectionHeight();
    setBlur();
});
$(window).on('resize', function(){
    setSectionHeight();
});
function setSectionHeight(){
    if($("body").width() > 768)
        $("#main-section").height((parseInt($("html").height())-parseInt($("footer").height())-parseInt($("#main-header").height()))+"px");
    else
        $("#main-section").height("auto");

}

function setBlur(){
    $(".tile").on({
        "mouseenter": function (){
            $(this).find(".blur").css({
                "filter": "blur(0px)",
                "-webkit-filter": "blur(0px)"
            })
            $(this).find(".title").css("top","-51%");
        },
        "mouseleave": function (){
            $(this).find(".blur").css({
                "filter": "blur(8px)",
                "-webkit-filter": "blur(8px)"
            });
            $(this).find(".title").css("top","0");
        }
    })
}
