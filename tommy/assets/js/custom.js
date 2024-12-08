var pageproduct = document.getElementById("product");
if(pageproduct) {
let slideIndex = 1;
showSlides(slideIndex);

// Next/previous controls
function plusSlides(n) {
  showSlides(slideIndex += n);
}

// Thumbnail image controls
function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dotx");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" activex", "");
  }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " activex";
}

$(".nav-link").click(function () {
  $(".tab-pane:not(" + $(this).attr('href') + ")").css("display","none");
  if($($(this).attr('href')).css('display') == 'none') {
    $($(this).attr('href')).css("display","block");
  } else {
    $($(this).attr('href')).css("display","none");
  }
});
}