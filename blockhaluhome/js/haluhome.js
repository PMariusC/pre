if($("#index").length != 0) {
	 $(document).ready(function() {
        let currentIndex = 0;
        const sections = $('.blocurivideo');

        function scrollToSection(index) {
            if (index >= 0 && index < sections.length) {
                $('html, body').animate({
                    scrollTop: $(sections[index]).offset().top
                }, 1000);
                currentIndex = index;
            }
        }

        let isScrolling = false;
        function debounceScroll(event) {
            if (isScrolling) return;
            isScrolling = true;

            let delta = event.originalEvent.deltaY || -event.originalEvent.wheelDelta || event.originalEvent.detail;
            if (delta > 0) {
                // Scroll down
                if (currentIndex < sections.length - 1) {
                    scrollToSection(currentIndex + 1);
                }
            } else {
                // Scroll up
                if (currentIndex > 0) {
                    scrollToSection(currentIndex - 1);
                }
            }

            setTimeout(function() {
                isScrolling = false;
            }, 500); // Adjust the delay as needed
        }

        $(window).on('wheel mousewheel DOMMouseScroll', function(event) {
            //event.preventDefault();
            debounceScroll(event);
        });
    });
}