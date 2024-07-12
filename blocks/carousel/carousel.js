function advancedCarousel() {
    let thumbnailCarousel = '';
    let imageCarousel = '';
    let contentCarousel = '';
    let arrowPath = 'M 22.271,20 -4.3069e-5,40.000003 V -2.869e-6 Z M 8.4639491,14.764379 5.2827458,19.435734 c -0.1179194,0.172948 -0.1179194,0.400052 0,0.573001 l 3.1812033,4.672228 c 0.1572258,0.231472 0.474298,0.291742 0.7057694,0.133642 0.2323449,-0.158099 0.2917413,-0.474298 0.1345154,-0.706643 L 6.3178161,19.722234 9.3042339,15.33738 C 9.4614598,15.105035 9.4020634,14.788836 9.1697185,14.630737 8.9382471,14.472638 8.6211749,14.532907 8.4639491,14.764379 Z'
    let carousels = document.querySelectorAll('.splide');
    let targetedCarousels = [];
    let mobile = window.matchMedia('(max-width: 991px)');
    
    
    // Get appropriate carousel items and stop at 3
    carousels.forEach(carousel => {
        if (carousel.classList.contains('image-carousel')) {
            imageCarousel = carousel;
            targetedCarousels.push(carousel);
        }
        if (carousel.classList.contains('thumbnail-carousel')) {
            thumbnailCarousel = carousel;
            targetedCarousels.push(carousel);
        }
        if (carousel.classList.contains('content-carousel')) {
            contentCarousel = carousel;
            targetedCarousels.push(carousel);
        }
        
        if (targetedCarousels.length === 3) {
            return;
        }
    });
    
    // Setup Carousels from foreach
    let main = new Splide(imageCarousel, {
        type: 'fade',
        rewind: false,
        pagination: false,
        arrowPath: arrowPath,
        omitEnd: true,
    });
    
    let thumbnails = new Splide(thumbnailCarousel, {
        fixedWidth: 150,
        fixedHeight: '100%',
        gap: 10,
        rewind: false,
        pagination: false,
        isNavigation: true,
        arrowPath: arrowPath,
        breakpoints: {
            991: {
                height: 'auto',
                direction: 'ttb',
                fixedWidth: '65vw',
                fixedHeight: 'auto',
                gap: '1.5rem',
            }
        }
    });
    
    let content = new Splide(contentCarousel, {
        type: 'fade',
        rewind: false,
        pagination: false,
        arrowPath: arrowPath,
        omitEnd: true,
        isNavigation: false,
        arrows: false,
        breakpoints: {
            991: {
                isNavigation: false,
                arrows: true,
            }
        }
        
    });
    
    if (mobile.matches) { // run some extra code on mobile for carousel navigational purposes
        let closeBtn = document.getElementById('advanced-carousel-close-button');
        let overlayContent = document.querySelector('.advanced-carousel-content-container');
        let scrollableContentDivs = document.querySelectorAll('.advanced-carousel-content-container .content');
        
        content.sync(thumbnails);
        content.mount();
        thumbnails.mount();
        
        thumbnails.on('click', function () {
            overlayContent.classList.add('show');
        });
        
        closeBtn.addEventListener('click', function () {
            overlayContent.classList.remove('show');
            scrollableContentDivs.forEach(content => {
                setTimeout(() => {
                    content.scrollTop = 0;
                }, 250)
            });
        });
        
    } else {
        main.sync(content);
        main.sync(thumbnails);
        main.mount();
        thumbnails.mount();
        content.mount();
    }
    

}

function destroyCarousels() { // destroy carousels and reset classes
    let carousels = document.querySelectorAll('.splide');
    
    carousels.forEach(carousel => {
        new Splide(carousel).destroy();
    });
}

document.addEventListener('DOMContentLoaded', function () {
    let carousels = document.querySelectorAll('.splide');
    if (carousels.length) {
        advancedCarousel();
    }
});