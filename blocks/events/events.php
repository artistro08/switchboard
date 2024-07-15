<?php
$show_filters         = get_field("show_filters");
$hide_previous_events = get_field("hide_previous_events");
$request_event_link   = get_field("hide_previous_events");
$accordion_id         = 'events_' . strtolower(generateRandomString(10));
$filter_form_id       = 'events_' . strtolower(generateRandomString(10));
$index                = 0;
$filters              = ['city', 'state', 'country'];
$cities               = [];
$states               = [];
$countries            = [];
$args                 = array(
    'post_type'      => 'event',
    'posts_per_page' => -1,
    'meta_key'       => 'start_date',
    'order'          => 'ASC',
    'orderby'        => 'meta_value',
);
$events               = new WP_Query($args); ?>

<?php if ($events->have_posts()) : ?>
    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <?php if($show_filters) : ?>
                <?php while ($events->have_posts()) : $events->the_post(); ?>
                    <?php // get all the cities, states and countries from the events 
                        $end_date = DateTime::createFromFormat("F j, Y g:i a", get_field('end_date', get_the_ID()));

                        $is_past_event = $end_date < new DateTime() ? true : false;
                        // show this event initially
                        $show_this_event = true;
                    
                    ?>
                    <?php if ($is_past_event) : // if the event is in the past and $hide_previous_events is set to true, hide this event ?>
                        <?php if ($hide_previous_events) : ?>
                            <?php $show_this_event = false; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($show_this_event) : ?>
                        <?php $filter_location = get_field('location', get_the_ID());
                            if ($filter_location) {
                                $data_address = '';
                                foreach (array('city', 'state', 'country') as $i => $k) {
                                    if (isset($filter_location[$k])) {
                                        if ($i === 0) {
                                            $cities[] = $filter_location[$k];
                                        }
                                        
                                        if ($i === 1) {
                                            $states[] = $filter_location[$k];
                                        }
                                        
                                        if ($i === 2) {
                                            $countries[] = $filter_location[$k];
                                        }
                                        
                                    }
                                }
                            }
                        ?>
                    <?php endif; ?>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
                <?php 
                    $cities = array_unique($cities);
                    $states = array_unique($states);
                    $countries = array_unique($countries);
                ?>
            <?php endif; ?> 
            <div class="events-header d-flex justify-content-between align-items-center">
                <?php if ($show_filters) : ?>
                    <div class="filter-links d-flex">
                        <button class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#<?php echo "{$filter_form_id}_filter-form" ?>" aria-expanded="false" aria-controls="<?php echo "{$filter_form_id}_filter-form" ?>" onclick="showHideFilters()">Filters</button>
                        <div class="hidden-filter-links">
                            <button class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#<?php echo "{$filter_form_id}_filter-form" ?>" aria-expanded="false" aria-controls="<?php echo "{$filter_form_id}_filter-form" ?>" onclick="clearFilters('<?php echo $filter_form_id ?>', '<?php echo $accordion_id ?>');showHideFilters();">Clear All</button>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="request-links ms-auto">
                    <?php
                        $request_event_link = get_field('request_event_link');
                        if ($request_event_link) :
                            $link_url    = $request_event_link['url'];
                            $link_title  = $request_event_link['title'];
                            $link_target = $request_event_link['target'] ? $request_event_link['target'] : '_self'; ?>
                        <a 
                            class="btn btn-link" 
                            href="<?php echo esc_url($link_url); ?>"
                            target="<?php echo esc_attr($link_target); ?>"
                        >
                            <?php echo esc_html($link_title); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($show_filters) : ?>
                <div class="filter-bar">
                    <form id="<?php echo "{$filter_form_id}_filter-form" ?>" class="collapse filter-form">
                        <div class="d-flex justify-lg-content-center align-lg-items-center gap-3 flex-wrap filter-form-wrapper">
                            <?php foreach ($filters as $i => $filter) : ?>
                                <?php if ($filter === 'city') : ?>
                                    <div class="filter-div filter-city">
                                        <label for="<?php echo "{$filter_form_id}_filter-city" ?>">City</label>
                                        <select class="form-select" name="filter-city" id="<?php echo "{$filter_form_id}_filter-city" ?>">
                                            <option value="">All</option>
                                            <?php foreach ($cities as $city) : ?>
                                                <option value="<?php echo $city; ?>"><?php echo $city; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                                <?php if ($filter === 'state') : ?>
                                    <div class="filter-div filter-state">
                                        <label for="<?php echo "{$filter_form_id}_filter-state" ?>">State</label>
                                        <select class="form-select" name="filter-state" id="<?php echo "{$filter_form_id}_filter-state" ?>">
                                            <option value="">All</option>
                                            <?php foreach ($states as $state) : ?>
                                                <option value="<?php echo $state; ?>"><?php echo $state; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                                <?php if ($filter === 'country') : ?>
                                    <div class="filter-div filter-country">
                                        <label for="<?php echo "{$filter_form_id}_filter-country" ?>">Country</label>
                                        <select class="form-select" name="filter-country" id="<?php echo "{$filter_form_id}_filter-country" ?>">
                                            <option value="">All</option>
                                            <?php foreach ($countries as $country) : ?>
                                                <option value="<?php echo $country; ?>"><?php echo $country; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach ;  ?>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
            <hr class="events-header-divider" style="border-top-width: 2px;" class="opacity-100 w-100 mw-100">
            <div class="accordion events-accordion" id="<?php echo "{$accordion_id}_events"; ?>">
                <?php while ($events->have_posts()) : $events->the_post(); ?>
                    <?php
                        // Get the start and end dates
                        $start_date      = DateTime::createFromFormat("F j, Y g:i a", get_field('start_date', get_the_ID()))->format('F j, Y');
                        $start_date_time = DateTime::createFromFormat("F j, Y g:i a", get_field('start_date', get_the_ID()))->format('g:i a');
                        $end_date        = DateTime::createFromFormat("F j, Y g:i a", get_field('end_date', get_the_ID()))->format('F j, Y');
                        $end_date_time   = DateTime::createFromFormat("F j, Y g:i a", get_field('end_date', get_the_ID()))->format('g:i a');
                        $display_date    = '';

                        // Check if event is in the past
                        // Also check if event is in the same day
                        $is_past_event = DateTime::createFromFormat("F j, Y g:i a", get_field('end_date', get_the_ID())) < new DateTime() ? true : false;
                        $is_same_day   = $start_date == $end_date ? true : false;
                        

                        // Show a different format for the same day events
                        if ($is_same_day) {
                            $display_date = "<span class='start_date'>$start_date</span> <br> <span class='start_date_time'>$start_date_time</span><span class='end_date_time'> - $end_date_time</span>";
                        } else {
                            $display_date = "<span class='start_date'>$start_date</span> <br> <span class='start_date_time'>$start_date_time</span> <br> <span class='end_date'>$end_date</span> <br> <span class='end_date_time'>$end_date_time</span>";
                        }
                        
                        // show this event initially
                        $show_this_event = true;
                        
                        $is_editing = str_contains(admin_url(sprintf('admin.php?%s', http_build_query($_GET))), 'edit') ? true : false;
                        
                    ?>
                    
                    <?php if ($is_past_event) : // if the event is in the past and $hide_previous_events is set to true, hide this event ?>
                        <?php if ($hide_previous_events) : ?>
                            <?php $show_this_event = false; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($show_this_event) : ?>
                        <?php $data_location = get_field('location', get_the_ID());
                            if ($data_location) {

                                // Loop over segments and construct HTML.
                                $data_address = '';
                                foreach (array('street_number', 'street_name', 'city', 'state', 'post_code', 'country') as $i => $k) {
                                    if (isset($data_location[$k])) {
                                        $data_address .= sprintf('data-filter-%s="%s", ', $k, $data_location[$k]);
                                    }
                                }
                            }
                        ?>
                        <div class="accordion-item" <?php echo $data_address; ?> >
                            <?php $title_location = get_field('location', get_the_ID());
                                if ($title_location) {

                                    // Loop over segments and construct HTML.
                                    $location_title = '';
                                    $ical_location_title = '';
                                    foreach (array('city', 'state') as $i => $k) {
                                        if (isset($title_location[$k])) {
                                            $location_title .= sprintf('<span class="segment-%s">%s</span>&nbsp;', $k, $title_location[$k]);
                                        }
                                    }
                                    
                                    foreach (array('street_number', 'street_name', 'city', 'state', 'post_code', 'country') as $i => $k) {
                                        if (isset($title_location[$k])) {
                                            $ical_location_title .= sprintf('%2$s, ', $k, $title_location[$k]);
                                        }
                                    }

                                    // Trim trailing comma.
                                    $location_title = trim($location_title, ', ');
                                    $ical_location_title = trim($ical_location_title, ', ');
                                }
                            ?>
                            <h2 class="accordion-header">
                                <button class="accordion-button fs-3 align-items-center justify-content-center collapsed flex-wrap" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#<?php echo "{$accordion_id}_events_collapse_{$index}"; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                    aria-controls="<?php echo "{$accordion_id}_events_collapse_{$index}"; ?>">
                                    <div class="text-center">
                                        <?php echo get_the_title(); ?> <br> <span class="location-title"><?php echo $location_title ?></span> <br> <?php echo $display_date ?>
                                    </div>
                                </button>
                            </h2>
                            <div id="<?php echo "{$accordion_id}_events_collapse_{$index}"; ?>"
                                class="accordion-collapse collapse"
                                data-bs-parent="#<?php echo "{$accordion_id}_events"; ?>">
                                <div class="accordion-body">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-lg-6">
                                            <div class="row">
                                                <div class="col-12 col-lg-8 mx-auto">
                                                    <?php $location = get_field('location', get_the_ID());
                                                        if( $location ) {
                    
                                                            // Loop over segments and construct HTML.
                                                            $address = '';
                                                            foreach( array('street_number', 'street_name', 'city', 'state', 'post_code', 'country') as $i => $k ) {
                                                                if( isset( $location[ $k ] ) ) {
                                                                    $address .= sprintf( '<span class="segment-%s">%s</span>, ', $k, $location[ $k ] );
                                                                }
                                                            }
                    
                                                            // Trim trailing comma.
                                                            $address = trim( $address, ', ' );
                                                            
                                                            // Split into array
                                                            $address = explode(',', $address );
                                                        }
                                                    ?>
                                                    <div class="address mb-2">
                                                        <?php foreach( $address as $key => $item ) : ?>
                                                            <?php echo $item; ?>
                                                        <?php endforeach; ?>
                                                        <div class="add-to-calendar">
                                                            <?php if (!$is_editing) : ?>
                                                                <add-to-calendar-button
                                                                    name="<?php echo get_the_title(); ?>"
                                                                    startDate="<?php echo DateTime::createFromFormat("F j, Y g:i a", get_field('start_date', get_the_ID()))->format('Y-m-d'); ?>"
                                                                    endDate="<?php echo DateTime::createFromFormat("F j, Y g:i a", get_field('end_date', get_the_ID()))->format('Y-m-d'); ?>"
                                                                    startTime="<?php echo DateTime::createFromFormat("F j, Y g:i a", get_field('start_date', get_the_ID()))->format('g:i a'); ?>"
                                                                    endTime="<?php echo DateTime::createFromFormat("F j, Y g:i a", get_field('start_date', get_the_ID()))->format('g:i a'); ?>"
                                                                    location="<?php echo htmlspecialchars ($ical_location_title) ?>"
                                                                    options="['Apple','Google','iCal','Microsoft365','Outlook.com','Yahoo']"
                                                                    timeZone="America/Chicago"
                                                                    trigger="click"
                                                                    inline
                                                                    listStyle="modal"
                                                                    iCalFileName="Reminder-Event"
                                                                />
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <div class="row">
                                                <div class="col-12 col-lg-9 mx-auto">
                                                    <?php $location = get_field('location', get_the_ID()); if( $location ): ?>
                                                        <?php if (!$is_editing) : ?>
                                                            <div class="acf-map" data-zoom="16">
                                                                <div class="marker" data-lat="<?php echo esc_attr($location['lat']); ?>" data-lng="<?php echo esc_attr($location['lng']); ?>"></div>
                                                            </div>
                                                        <?php else : ?>
                                                            <div class="p-5 bg-secondary d-flex justify-content-center align-items-center">
                                                                Map will be rendered here
                                                            </div>
                                                        <?php endif; ?>
                                                        <a class="map-link" href="https://www.google.com/maps/search/?api=1&query=<?php echo str_replace(',', '', str_replace(' ', '+', $location['address'])); ?>" target="_blank">View on Map</a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="text-center">
                                                <?php the_field('description', get_the_ID()); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php $index++; ?>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<script data-filter-form-id="<?php echo "{$filter_form_id}_filter-form" ?>" type="text/javascript">
    function filterForm() {
        let form = document.getElementById('<?php echo "{$filter_form_id}_filter-form" ?>');
        if (form.length) {
            let cityFilter = document.getElementById('<?php echo "{$filter_form_id}_filter-city" ?>');
            let stateFilter = document.getElementById('<?php echo "{$filter_form_id}_filter-state" ?>');
            let countryFilter = document.getElementById('<?php echo "{$filter_form_id}_filter-country" ?>');
            let filterableCities = document.querySelectorAll('[data-filter-city]');
            let filterableStates = document.querySelectorAll('[data-filter-state]');
            let filterableCountries = document.querySelectorAll('[data-filter-country]');
            
            let filterableItems = [];
            let currentValues = [cityFilter.value, stateFilter.value, countryFilter.value];
            
            cityFilter.addEventListener('change', function() {
                filterableCities.forEach(city => {
                    filterableItems.push(city.dataset.filterCity);
                });
            });
            
            stateFilter.addEventListener('change', function() {
                filterableStates.forEach(state => {
                    filterableItems.push(state.dataset.filterState);
                });    
            });
            
            countryFilter.addEventListener('change', function() {
                filterableCountries.forEach(country => {
                    filterableItems.push(country.dataset.filterCountry);
                });
            });
            
            
            form.addEventListener('change', function (event) {
                currentValues = [cityFilter.value, stateFilter.value, countryFilter.value];
                

                
                filterableItems.forEach(item => {
                    
                    if (currentValues.includes(item)) {
                        document.querySelectorAll('[data-filter-city="' + item + '"]').forEach(city => {
                            city.classList.remove('d-none');
                        });
                        document.querySelectorAll('[data-filter-state="' + item + '"]').forEach(state => {
                            state.classList.remove('filter-active');
                        });
                        document.querySelectorAll('[data-filter-country="' + item + '"]').forEach(country => {
                            country.classList.remove('filter-active');
                        });
                    } else {
                        document.querySelectorAll('[data-filter-city="' + item + '"]').forEach(city => {
                            city.classList.add('d-none');
                        });
                        document.querySelectorAll('[data-filter-state="' + item + '"]').forEach(state => {
                            state.classList.add('d-none');
                        });
                        document.querySelectorAll('[data-filter-country="' + item + '"]').forEach(country => {
                            country.classList.add('d-none');
                        });
                    }
                    
                });
                
                filterableItems.forEach(item => {
                    
                    if (currentValues.includes(item)) {
                        document.querySelectorAll('[data-filter-city="' + item + '"]').forEach(city => {
                            city.classList.remove('d-none');
                        });
                        document.querySelectorAll('[data-filter-state="' + item + '"]').forEach(state => {
                            state.classList.remove('d-none');
                        });
                        document.querySelectorAll('[data-filter-country="' + item + '"]').forEach(country => {
                            country.classList.remove('d-none');
                        });
                    } 
                    
                });
                
                let noCurrentValues = currentValues.every(element => !element);
                
                if (noCurrentValues) {
                    document.querySelectorAll('[data-filter-city]').forEach(city => {
                        city.classList.remove('d-none');
                    });
                    document.querySelectorAll('[data-filter-state]').forEach(state => {
                        state.classList.remove('d-none');
                    });
                    document.querySelectorAll('[data-filter-country]').forEach(country => {
                        country.classList.remove('d-none');
                    });
                }
            });
        }
    }
    filterForm();
</script>