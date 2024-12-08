// to add later something if needed
$('#cat4test').on('click', function () {
     $(this).find("span").toggleClass('open4')
    $(this).find(".sub-sub-cathidden").toggleClass('opencat4')
});
//fix empty top for sub menu 54321 is manually declared...modify if go back to rand
var top_sub_menu = document.getElementById("top_sub_menu_54321");
var offsetHeight = document.getElementById("headtopright").offsetHeight;
var offsetHeightHeadertop = document.getElementById("forfixprodpage").offsetHeight;
if(top_sub_menu.getAttribute("style").indexOf("top:") == -1){
    top_sub_menu.style.top = offsetHeight + "px";
    //offsetHeight + 17 + "px";
}
//fix fixed position on product page
function sticky_relocate() {
    var window_top = $(window).scrollTop()+60;
    var footer_top = $("#footer").offset().top;
    var div_top = $('#stick-anchor').offset().top;
    var div_height = $("#stick").height();
    
    var padding = 80;  // tweak here or get from margins etc
    
    if (window_top + div_height > footer_top - padding)
        $('#stick').css({top: (window_top + div_height - footer_top + padding) * -1})
    else if (window_top > div_top) {
        $('#stick').addClass('fixit');
        $('#stick').css({top: offsetHeightHeadertop})
    } else {
        $('#stick').removeClass('fixit');
    }
}

$(function () {
    $(window).scroll(sticky_relocate);
    sticky_relocate();
});

$(".nav-link").click(function () {
    if(!$(this).hasClass('openplus')) {
        $(this).addClass('openplus');
    } else {
        $(this).removeClass('openplus');
    }
    $(".tab-pane:not(" + $(this).attr('href') + ")").css("display","none");
    if($($(this).attr('href')).css('display') == 'none') {
        $($(this).attr('href')).css("display","block");
    } else {
        $($(this).attr('href')).css("display","none");
    }
});
