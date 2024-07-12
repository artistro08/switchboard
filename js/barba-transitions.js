const minWidth = window.matchMedia("(max-width: 991px)");

// Prevent Barba from running on the admin bar (Does not update the admin menu)
document.querySelectorAll("#wpadminbar a").forEach(item => item.setAttribute('data-barba-prevent', 'self'));

// Google Maps
function initMaps() {
    (function ($) {
    
        /**
         * initMap
         *
         * Renders a Google Map onto the selected jQuery element
         *
         * @date    22/10/19
         * @since   5.8.6
         *
         * @param   jQuery $el The jQuery element.
         * @return  object The map instance.
         */
        function initMap($el) {
    
            // Find marker elements within map.
            var $markers = $el.find('.marker');
    
            // Create gerenic map.
            var mapArgs = {
                zoom: $el.data('zoom') || 16,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                disableDefaultUI: true
            };
            var map = new google.maps.Map($el[0], mapArgs);
    
            // Add markers.
            map.markers = [];
            $markers.each(function () {
                initMarker($(this), map);
            });
    
            // Center map based on markers.
            centerMap(map);
    
            // Return map instance.
            return map;
        }
    
        /**
         * initMarker
         *
         * Creates a marker for the given jQuery element and map.
         *
         * @date    22/10/19
         * @since   5.8.6
         *
         * @param   jQuery $el The jQuery element.
         * @param   object The map instance.
         * @return  object The marker instance.
         */
        function initMarker($marker, map) {
    
            // Get position from marker.
            var lat = $marker.data('lat');
            var lng = $marker.data('lng');
            var latLng = {
                lat: parseFloat(lat),
                lng: parseFloat(lng)
            };
    
            // Create marker instance.
            var marker = new google.maps.Marker({
                position: latLng,
                map: map
            });
    
            // Append to reference for later use.
            map.markers.push(marker);
    
            // If marker contains HTML, add it to an infoWindow.
            if ($marker.html()) {
    
                // Create info window.
                var infowindow = new google.maps.InfoWindow({
                    content: $marker.html()
                });
    
                // Show info window when marker is clicked.
                google.maps.event.addListener(marker, 'click', function () {
                    infowindow.open(map, marker);
                });
            }
        }
    
        /**
         * centerMap
         *
         * Centers the map showing all markers in view.
         *
         * @date    22/10/19
         * @since   5.8.6
         *
         * @param   object The map instance.
         * @return  void
         */
        function centerMap(map) {
    
            // Create map boundaries from all map markers.
            var bounds = new google.maps.LatLngBounds();
            map.markers.forEach(function (marker) {
                bounds.extend({
                    lat: marker.position.lat(),
                    lng: marker.position.lng()
                });
            });
    
            // Case: Single marker.
            if (map.markers.length == 1) {
                map.setCenter(bounds.getCenter());
    
                // Case: Multiple markers.
            } else {
                map.fitBounds(bounds);
            }
        }
    
        // Render maps on page load.
        $(document).ready(function () {
            $('.acf-map').each(function () {
                var map = initMap($(this));
            });
        });
    
    })(jQuery);
}

initMaps();

// Clear Form Filters function
function clearFilters(filter_form_id, accordion_id) {
    let cityFilter = document.getElementById(filter_form_id + '_filter-city');
    let stateFilter = document.getElementById(filter_form_id + '_filter-state');
    let countryFilter = document.getElementById(filter_form_id + '_filter-country');
    let accordion = document.getElementById(accordion_id + '_events');
    let filterableCities = accordion.querySelectorAll('[data-filter-city]');
    let filterableStates = accordion.querySelectorAll('[data-filter-state]');
    let filterableCountries = accordion.querySelectorAll('[data-filter-country]');

    cityFilter.value = '';
    stateFilter.value = '';
    countryFilter.value = '';

    filterableCities.forEach(city => {
        city.classList.remove('d-none');
    });

    filterableStates.forEach(state => {
        state.classList.remove('d-none');
    });

    filterableCountries.forEach(country => {
        country.classList.remove('d-none');
    });

}

function showHideFilters() {
    let hiddenFilterLinks = document.querySelectorAll('.hidden-filter-links');
    hiddenFilterLinks.forEach(link => {
        link.classList.toggle('show');
    });
}

// slugify function
String.prototype.slugify = function (separator = "-") {
    return this
        .toString()
        .normalize('NFD')                  // split an accented letter in the base letter and the accent
        .replace(/[\u0300-\u036f]/g, '')   // remove all previously split accents
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9 ]/g, '')       // remove all chars not letters, numbers and spaces (to be replaced)
        .replace(/\s+/g, separator);
};

if (!minWidth.matches) {
    barba.hooks.before((data) => {
        main_container = document.getElementById('main-container');
        animation_target = data.trigger;
        
        if (typeof animation_target == 'string') { 
            // if the trigger is a string, it's not animate-able
            animation_target = null;
        } else {
            // Animations if you click the panel, or nav link. (nav link is extrapolated from a data attribute)
            if (animation_target.classList.contains('panel-image-and-title-container')) { 
                animation_target = document.getElementById(data.trigger.id).parentElement;
            } else if (animation_target.classList.contains('nav-link')) {
                if (document.getElementById('section-' + data.trigger.dataset.animationTarget)) {
                    animation_target = document.getElementById('section-' + data.trigger.dataset.animationTarget).parentElement;
                } else {
                    animation_target = null;
                }
            } else if (animation_target.classList.contains('dropdown-item')) {
                if (document.getElementById('section-' + data.trigger.dataset.animationTarget)) {
                    animation_target = document.getElementById('section-' + data.trigger.dataset.animationTarget).parentElement;
                } else {
                    animation_target = null;
                }
            } else {
                animation_target = null;
            }
            
        }
        
        // Hide the floating button content before moving to the next page
        jQuery("#floatingButtonContent").collapse('hide');
        
        Fancybox.destroy();
        destroyCarousels();

    });
    
    barba.hooks.after((data) => {
        initMaps();
        
        
        let filterFormElements = document.querySelectorAll('[data-filter-form-id]');
        if (filterFormElements.length) {
            filterFormElements.forEach(element => {
                eval(element.innerHTML);
            });
        }
        
        if (document.querySelectorAll('[data-fancybox]').length) {
            Fancybox.bind("[data-fancybox]", {});
        }
        if (document.querySelectorAll('.splide').length) {
            advancedCarousel(); 
        }
        if (data.next.url) {
            if (data.next.url.href.includes('events')) {

            }
        }
        
    });
    
    
    barba.init({
        debug: true,
        logLevel: 'debug',
        preventRunning: true,
        transitions: [
            { // Default Transition (Home)
                name: 'default',
                async leave() {
                    if (animation_target) {
                        main_container.classList.add('panel-open');
                        animation_target.classList.add('active');
                    }
                    await new Promise(r => setTimeout(r, 700));

                },
                enter() {
                    let menu_target = document.querySelectorAll(".panel.active");
                    let content_target = document.querySelectorAll(".panel-content-container");
                    let standalone_content_target = document.getElementById("standalone-panel-content-container");
                    
                    if (menu_target) {
                        menu_target.forEach((panel) => {
                            setTimeout(() => {
                                panel.classList.add("add-menu");
                            }, 5)
                        })
                    }
                    
                    if (content_target) {
                        content_target.forEach((content) => {
                            setTimeout(() => {
                                content.classList.add("add-content");
                            }, 5)
                        })
                    }
                    
                    if (standalone_content_target) {
                        setTimeout(() => {
                            standalone_content_target.classList.add("add-content");
                        }, 5)
                    }
                },
            },
            { // Panel -> Panel transition
                name: 'opened-panel-to-opened-panel',
                namespace: 'open-panel',
                from: {namespace: 'open-panel'},
                to: { namespace: 'open-panel' },
                
                async leave(data) {
                    let panels = document.querySelectorAll('.panel');
                    let content_target = document.querySelectorAll(".panel-content-container");
                    let menu_container = document.querySelectorAll(".panel.active");
                    let menu_target = document.querySelectorAll(".panel-menu");
                    let standalone_content_target = document.getElementById("standalone-panel-content-container");
                    
                    if (menu_container) {
                        menu_container.forEach((panel) => {
                            setTimeout(() => {
                                panel.classList.add("add-menu");
                            }, 5)
                        })
                    }
                    
                    if (content_target) {
                        content_target.forEach((content) => {
                            setTimeout(() => {
                                content.classList.add("add-content");
                            }, 5)
                        })
                    }
                    
                    if (menu_target) {
                        menu_target.forEach((menu) => {
                            menu.classList.add("no-animation");
                        })
                    }
                    
                    if (standalone_content_target) {
                        setTimeout(() => {
                            standalone_content_target.classList.add("add-content");
                        }, 5)
                    }

                    panels.forEach(panel => {
                        let panel_content = panel.querySelector('.panel-content-container.add-content');

                        if (panel_content !== null) {
                            panel_content.classList.remove('add-content');
                        }


                    });
                    
                    await new Promise(r => setTimeout(r, 1));
                    if (data.trigger !== 'back') {
                        if (data.trigger.dataset.animationTarget !== undefined) {
                            let the_animation_target = data.trigger.dataset.animationTarget;
                            let active_panel = document.getElementById('section-' + the_animation_target);
                            let active_panel_content_isEmpty = active_panel.parentElement.querySelector('.panel-content').innerHTML === "";
                        
                        
                            if (!active_panel_content_isEmpty) {
                            
                                await new Promise(r => setTimeout(r, 1));

                            } else {
                            
                                await new Promise(r => setTimeout(r, 700));
                            
                            }
                        
                        }
                    }
                },
                
                enter(data) {
                    let content_target = document.querySelectorAll(".panel-content-container");
                    let panels = document.querySelectorAll('.panel');
                    let menu_container = document.querySelectorAll(".panel.active");
                    let menu_target = document.querySelectorAll(".panel-menu");
                    let standalone_content_target = document.getElementById("standalone-panel-content-container");
                    
                    
                    if (menu_container) {
                        menu_container.forEach((panel) => {
                            setTimeout(() => {
                                panel.classList.add("add-menu");
                            }, 5);
                        })
                    }
                    
                    if (content_target) {
                        content_target.forEach((content) => {
                            setTimeout(() => {
                                content.classList.add("add-content");
                            }, 5);
                        })
                    }
                    
                    if (menu_target) {
                        if (data.trigger !== 'back') {
                            let the_animation_target = data.trigger.dataset.animationTarget;
                            if (!the_animation_target) {
                                menu_target.forEach((menu) => {
                                    menu.classList.add("no-animation");
                                });
                            } else {
                                let the_animation_target = data.trigger.dataset.animationTarget;
                                let active_panel = document.getElementById('section-' + the_animation_target).parentElement;
                                let active_panel_content_isEmpty = active_panel.querySelector('.panel-content').innerHTML === "";
                                if (active_panel.classList.contains('active')) {
                                    menu_target.forEach((menu) => {
                                        if (!active_panel_content_isEmpty) {
                                            menu.classList.add("no-animation");
                                        } else {
                                            menu.classList.remove("no-animation");
                                        }
                                    });
                                }
                            }
                        }
                        
                    }
                    
                    if (standalone_content_target) {
                        setTimeout(() => {
                            standalone_content_target.classList.add("add-content");
                        }, 5);
                        
                    }
                    
                    panels.forEach(panel => {
                        let panel_content = panel.querySelector('.panel-content-container.add-content');

                        if (panel_content !== null) {
                            panel_content.classList.add('add-content');
                        }
                    });
                }
            },
            { // Panel -> Standalone Panel transition
                name: 'panel-to-standalone-panel',
                namespace: 'open-panel',
                from: { namespace: 'open-panel' },
                to: { namespace: 'standalone-open-panel' },

                async leave(data) {
                    let panels = document.querySelectorAll('.panel');
                    let content_target = document.querySelectorAll(".panel-content-container");
                    let menu_container = document.querySelectorAll(".panel.active");
                    let standalone_content_target = document.getElementById("standalone-panel-content-container");
                    let standalone_target = document.getElementById("standalone-container");
                    let main_container = document.getElementById('main-container');
                    
                    if (main_container) {
                        setTimeout(() => {
                            main_container.classList.add("panel-open");
                        }, 5);
                    }
                    
                    if (menu_container) {
                        menu_container.forEach((panel) => {
                            setTimeout(() => {
                                panel.classList.remove("active");
                            }, 5)
                        })
                    }

                    if (content_target) {
                        content_target.forEach((content) => {
                            setTimeout(() => {
                                content.classList.remove("add-content");
                            }, 5)
                        })
                    }

                    if (standalone_content_target) {
                        
                        setTimeout(() => {
                            standalone_content_target.classList.add("add-content");
                        }, 5)
                    }
                    
                    if (standalone_target) {
                        
                        setTimeout(() => {
                            standalone_target.classList.add("active");
                        }, 5)
                    }

                    panels.forEach(panel => {
                        let panel_content = panel.querySelector('.panel-content-container.add-content');

                        if (panel_content !== null) {
                            panel_content.classList.remove('add-content');
                        }
                        
                        if (panel !== null) {
                            panel.classList.remove('active');
                            panel.classList.add('no-horizontal-padding');
                        }


                    });

                    await new Promise(r => setTimeout(r, 700));
                },

                enter(data) {
                    let standalone_content_target = document.getElementById("standalone-panel-content-container");
                    let standalone_target = document.getElementById("standalone-container");

                    if (standalone_target) {
                        setTimeout(() => {
                            standalone_content_target.classList.add("active");
                        }, 5);
                    }

                    if (standalone_content_target) {
                        setTimeout(() => {
                            standalone_content_target.classList.add("add-content");
                        }, 5);
                    }

                }
            },
            { // Standalone Panel -> Panel transition
                name: 'standalone-panel-to-panel',
                namespace: 'open-panel',
                from: { namespace: 'standalone-open-panel' },
                to: { namespace: 'open-panel' },

                async leave(data) {
                    let standalone_content_target = document.getElementById("standalone-panel-content-container");
                    let standalone_target = document.getElementById("standalone-container");
                    
                    if (animation_target) {
                        main_container.classList.add('panel-open');
                        animation_target.classList.add('active');
                    }

                    if (standalone_target) {
                        setTimeout(() => {
                            standalone_target.classList.remove("active");
                        }, 5);
                    }

                    if (standalone_content_target) {
                        setTimeout(() => {
                            standalone_content_target.classList.remove("add-content");
                        }, 5);
                    }
                    

                    await new Promise(r => setTimeout(r, 700));
                },

                enter(data) {
                    let panels = document.querySelectorAll('.panel');
                    let content_target = document.querySelectorAll(".panel-content-container");
                    let menu_container = document.querySelectorAll(".panel.active");
                    let standalone_content_target = document.getElementById("standalone-panel-content-container");
                    let standalone_target = document.getElementById("standalone-container");

                    if (menu_container) {
                        menu_container.forEach((panel) => {
                            setTimeout(() => {
                                panel.classList.add("add-menu");
                            }, 5)
                        })
                    }

                    if (content_target) {
                        content_target.forEach((content) => {
                            setTimeout(() => {
                                content.classList.add("add-content");
                            }, 5)
                        })
                    }

                    if (standalone_content_target) {
                        setTimeout(() => {
                            standalone_content_target.classList.remove("add-content");
                        }, 5)
                    }

                    if (standalone_target) {
                        setTimeout(() => {
                            standalone_target.classList.remove("active");
                        }, 5)
                    }

                    panels.forEach(panel => {
                        let panel_content = panel.querySelector('.panel-content-container.add-content');

                        if (panel_content !== null) {
                            panel_content.classList.remove('add-content');
                        }


                    });

                }
            },
            { // Panel -> Default (Home) Transition
                name: 'closed-panel',
                namespace: 'open-panel',
                from: {namespace: 'open-panel'},
                to: {namespace: 'default'},

                async leave(data) {
                    let panels = document.querySelectorAll('.panel');
                    let menu_target = document.querySelectorAll(".panel-menu");

                    if (animation_target) {
                        main_container.classList.remove('panel-open');
                        animation_target.classList.remove('active');
                    }

                    if (menu_target) {
                        menu_target.forEach((menu) => {
                            menu.classList.remove("no-animation");
                        })
                    }

                    await new Promise(r => setTimeout(r, 700));

                },
                enter() {
                    let menu_container = document.querySelectorAll(".panel.active");
                    let content_target = document.querySelectorAll(".panel-content-container");
                    
                    if (animation_target) {
                        main_container.classList.remove('panel-open');
                        animation_target.classList.remove('active');
                    }

                    if (menu_container) {
                        menu_container.forEach((panel) => {
                            setTimeout(() => {
                                panel.classList.add("add-menu");
                            }, 5)
                        })
                    }
                    if (content_target) {
                        content_target.forEach((content) => {
                            setTimeout(() => {
                                content.classList.add("add-content");
                            }, 5)
                        })
                    }
                }
            },
            { // Standalone Panel -> Default (Home) Transition
                name: 'closed-standalone-panel',
                namespace: 'standalone-open-panel',
                from: { namespace: 'standalone-open-panel' },
                to: { namespace: 'default' },

                async leave(data) {
                    let standalone_content_target = document.getElementById("standalone-panel-content-container");
                    let standalone_target = document.getElementById("standalone-container");

                    if (animation_target) {
                        main_container.classList.remove('panel-open');
                        animation_target.classList.remove('active');
                    }
                    
                    if (main_container) {
                        setTimeout(() => {
                            main_container.classList.remove("panel-open");
                        }, 5);
                    }

                    if (standalone_target) {
                        setTimeout(() => {
                            standalone_target.classList.remove("active");
                        }, 5);
                    }

                    if (standalone_content_target) {
                        setTimeout(() => {
                            standalone_content_target.classList.remove("add-content");
                        }, 5);
                    }


                    await new Promise(r => setTimeout(r, 700));
                },
                enter() {
                    let menu_container = document.querySelectorAll(".panel.active");
                    let content_target = document.querySelectorAll(".panel-content-container");

                    if (animation_target) {
                        main_container.classList.remove('panel-open');
                        animation_target.classList.remove('active');
                    }

                    if (menu_container) {
                        menu_container.forEach((panel) => {
                            setTimeout(() => {
                                panel.classList.add("add-menu");
                            }, 5)
                        })
                    }
                    if (content_target) {
                        content_target.forEach((content) => {
                            setTimeout(() => {
                                content.classList.add("add-content");
                            }, 5)
                        })
                    }
                }
            },
            { // Default -> Standalone Panel (Home) Transition
                name: 'opened-standalone-panel',
                namespace: 'default',
                from: { namespace: 'default' },
                to: { namespace: 'standalone-open-panel' },

                async leave(data) {
                    let standalone_content_target = document.getElementById("standalone-panel-content-container");
                    let standalone_target = document.getElementById("standalone-container");

                    if (animation_target) {
                        main_container.classList.add('panel-open');
                        animation_target.classList.add('active');
                    }

                    if (main_container) {
                        setTimeout(() => {
                            main_container.classList.add("panel-open");
                        }, 5);
                    }

                    if (standalone_target) {
                        setTimeout(() => {
                            standalone_target.classList.add("active");
                        }, 5);
                    }

                    if (standalone_content_target) {
                        setTimeout(() => {
                            standalone_content_target.classList.add("add-content");
                        }, 5);
                    }


                    await new Promise(r => setTimeout(r, 700));
                },
                enter() {
                    let standalone_content_target = document.getElementById("standalone-panel-content-container");

                    if (standalone_content_target) {
                        setTimeout(() => {
                            standalone_content_target.classList.add("add-content");
                        }, 5);
                    }
                }
            }
        ],
        
        views: [
            {
                namespace: 'open-panel',
                beforeLeave(data) {
                    let panels = document.querySelectorAll('.panel');
                    
                    panels.forEach(panel => {
                        panel.classList.remove('active');
                        panel.classList.add('no-hover');
                        
                        if (data.trigger !== 'back') {
                            if (!data.trigger.dataset.animationTarget) {
                                if (panel.parentElement.classList.contains('panel-open')) {
                                    panel.parentElement.classList.remove('panel-open');
                                    panel.parentElement.classList.remove('no-animation');
                                }
                            } else {
                                panel.classList.add('no-horizontal-padding');
                            }
                        }
                        
                        
                        
                    });
                    if (data.trigger !== 'back') {
                        if (data.trigger.dataset.animationTarget) {
                            let next_panel = document.getElementById('section-' + data.trigger.dataset.animationTarget);
                            if (next_panel) {
                                next_panel.parentElement.classList.add('active', 'add-menu');
                                next_panel.parentElement.classList.remove('no-hover', 'no-horizontal-padding');
                            }
                        }
                    }
                },
                beforeEnter(data) {
                    let content_target = document.querySelectorAll(".panel-content-container");
                    let menu_container = document.querySelectorAll(".panel.active");
                    let standalone_content_target = document.getElementById("standalone-panel-content-container");
                    if (standalone_content_target) {
                        setTimeout(() => {
                            standalone_content_target.classList.add("add-content");
                        }, 5)
                    }
                    if (menu_container) {
                        menu_container.forEach((panel) => {
                            setTimeout(() => {
                                panel.classList.add("add-menu");
                            }, 5)
                        })
                    }
                    if (content_target) {
                        content_target.forEach((content) => {
                            setTimeout(() => {
                                content.classList.add("add-content");
                            }, 5)
                        })
                    }
                }
            },
            {
                namespace: 'standalone-open-panel',
                beforeEnter(data) {
                    let standalone_content_target = document.getElementById("standalone-panel-content-container");
                    let standalone_target = document.getElementById("standalone-container");
                    if (standalone_content_target) {
                        setTimeout(() => {
                            standalone_content_target.classList.add("add-content");
                        }, 5)
                    }

                    if (standalone_target) {
                        setTimeout(() => {
                            standalone_target.classList.add("active");
                        }, 5)
                    }
                }
            },
        ]
    });
}