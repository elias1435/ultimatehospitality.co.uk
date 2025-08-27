<?php
/* Template for displaying single Event custom post type
 * Template Name: Single Event Page Template
 *  */

get_header();

if (have_posts()) :
    while (have_posts()) : the_post();
        $event_date = get_field('event_date');
		$event_end_date = get_field('event_end_date');
        $event_location = get_field('event_location');
        $packages = get_field('package');
        $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
        $seat_plan = get_field('seat_plan', 'package');
        $event_terms = get_the_terms(get_the_ID(), 'event-category');
        $event_type = '';
        if ($event_terms && !is_wp_error($event_terms)) {
            $event_type = $event_terms[0]->name;
        }

?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />


<div class="main-event-wrapper">
    <!-- Featured Image with Title Overlay -->
    <div class="event-hero bg-cover bg-center bg-no-repeat bg-cover" style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
        <div class="event-title-overlay text-4xl content-end h-96 font-normal text-white text-center">
<!--             <h2 class="font-normal text-white text-center uppercase"><?php // echo esc_html($event_type); ?></h2> -->
			<h1 class="font-normal text-white text-center text-lg post-title bg-black bg-opacity-50"><?php the_title(); ?></h1>
        </div>
    </div>

    <div class="single-event">
        <div class="date-location-wrapper">
            <div class="p-10">
                <div class="text-center">
					<?php
					function render_calendar_block($date) {
						if (!$date) return;
						$timestamp = strtotime($date);
						$year = date("Y", $timestamp);
						$month = strtoupper(date("F", $timestamp));
						$day = date("d", $timestamp);
						$weekday = date("l", $timestamp);
						?>
						<div class="calendar-card text-xl text-white uppercase post-meta">
							<div class="calendar-year"><?php echo esc_html($year); ?></div>
							<div class="calendar-month"><?php echo esc_html($month); ?></div>
							<div class="calendar-day"><?php echo esc_html($day); ?></div>
							<div class="calendar-weekday"><?php echo esc_html($weekday); ?></div>
						</div>
						<?php
					}
					if (!empty($event_date)) : ?>
						<div class="calendar-range-wrapper flex items-center gap-4">
							<?php render_calendar_block($event_date); ?>

							<?php if (!empty($event_end_date)) : ?>
								<span class="text-black font-bold text-xl">→</span>
								<?php render_calendar_block($event_end_date); ?>
							<?php endif; ?>
						</div>
					<?php endif; ?>
                    <p class="mt-4 text-xl text-black uppercase post-meta location-tag"><?php echo esc_html($event_location); ?></p>
                </div>
            </div>
        </div>

        <div class="event-description px-4 mt-4 main-event-description">
            <?php the_content(); ?>
        </div>

        <?php if ($packages) : ?>
            <div class="event-packages mt-8 px-4">
                <?php foreach ($packages as $index => $package) : ?>
                    <div class="accordion-item marginy-50">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4 text-center price-title custom-title"><?php echo esc_html($package['package_title']); ?></h3>

                        <!-- Displaying Tabs Only If Content Exists -->
                        <div class="flex flex-mbl space-x-4 mb-4">
                            
                            <?php if (!empty($package['gallery'])) : ?>
                                <button class="accordion-tab py-2 px-4 bg-gray-200 font-semibold text-gray-700 active" onclick="showAccordionContent(event, 'gallery-<?php echo $index; ?>')">
                                    Gallery
                                </button>
                            <?php endif; ?>
							
                            <?php if (!empty($package['details'])) : ?>
                                <button class="accordion-tab py-2 px-4 bg-gray-200 font-semibold text-gray-700" onclick="showAccordionContent(event, 'details-<?php echo $index; ?>')">
                                    Details
                                </button>
                            <?php endif; ?>							
                            
                            <?php if (!empty($package['enquire'])) : ?>
                                <button class="accordion-tab py-2 px-4 bg-gray-200 font-semibold text-gray-700" onclick="showAccordionContent(event, 'enquire-<?php echo $index; ?>')">
                                    Enquire
                                </button>
                            <?php endif; ?>
                            
                            <?php if (!empty($package['book_now'])) : ?>
                                <button class="accordion-tab py-2 px-4 bg-gray-200 font-semibold text-gray-700" onclick="showAccordionContent(event, 'book-now-<?php echo $index; ?>')">
                                    Book Now
                                </button>
                            <?php endif; ?>
							
							<?php if (!empty($package['seat_plan'])) : ?>
								<!-- New Seat Plan Tab -->
                                <button class="accordion-tab py-2 px-4 bg-gray-200 font-semibold text-gray-700" onclick="showAccordionContent(event, 'seat-plan-<?php echo $index; ?>')">
                                    Seat Plan
                                </button>							
							<?php endif; ?>
							
							<?php if (!empty($package['itinerary'])) : ?>
                                <button class="accordion-tab py-2 px-4 bg-gray-200 font-semibold text-gray-700" onclick="showAccordionContent(event, 'itinerary-<?php echo $index; ?>')">
                                    Itinerary
                                </button>
                            <?php endif; ?>	
							
                        </div>

                        <!-- Conditional Content Display -->
                        <div class="accordion-content-container bg-white p-2">
		
							<?php if (!empty($package['gallery'])) : ?>
								<div id="gallery-<?php echo $index; ?>" class="accordion-content">
									<div class="swiper-container gallery-swiper my-4">
										<div class="swiper-wrapper">
											<?php foreach ($package['gallery'] as $image) : ?>
												<div class="swiper-slide">
													<a class="gallery-link" href="<?php echo esc_url($image['url']); ?>" 
													   onclick="openLightbox(event, <?php echo htmlspecialchars(json_encode($package['gallery'])); ?>)" 
													   class="cursor-pointer">
														<img src="<?php echo esc_url($image['url']); ?>" 
															 alt="<?php echo esc_attr($image['alt']); ?>" 
															 class="rounded-lg shadow hover:opacity-80 transition-opacity">
													</a>
												</div>
											<?php endforeach; ?>
										</div>
										<div class="swiper-button-next"></div>
										<div class="swiper-button-prev"></div>
										<div class="swiper-pagination"></div>
									</div>
								</div>
							<?php endif; ?>
							
                            <?php if (!empty($package['details'])) : ?>
                                <div id="details-<?php echo $index; ?>" class="accordion-content hidden">
                                    <div class="details-container"><?php echo wp_kses_post(wpautop($package['details'])); ?></div>
                                </div>
                            <?php endif; ?>
							
                            <?php if (!empty($package['enquire'])) : ?>
                                <div id="enquire-<?php echo $index; ?>" class="accordion-content hidden">
                                    <?php echo do_shortcode($package['enquire']); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($package['book_now'])) : ?>
                                <div id="book-now-<?php echo $index; ?>" class="accordion-content hidden">
                                    <?php echo do_shortcode($package['book_now']); ?>
                                </div>
                            <?php endif; ?>
							
							<?php if (!empty($package['seat_plan'])) : ?>
								<!-- Seat Plan Tab Content -->
								<div id="seat-plan-<?php echo esc_attr($index); ?>" class="accordion-content hidden">
									<a href="#" onclick="openModal('<?php echo esc_url($package['seat_plan']['url']); ?>')" class="cursor-pointer">
										<img src="<?php echo esc_url($package['seat_plan']['url']); ?>" 
											 alt="<?php echo esc_attr($package['seat_plan']['alt'] ?? 'Seat Plan'); ?>" 
											 class="w-full h-auto rounded-md hover:opacity-80 transition-opacity" />
									</a>
								</div>
							<?php endif; ?>
                            <?php if (!empty($package['itinerary'])) : ?>
                                <div id="itinerary-<?php echo $index; ?>" class="accordion-content hidden">
                                    <div class="details-container"><?php echo wp_kses_post(wpautop($package['itinerary'])); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

<?php
$related_events = get_field('related_events');

if ($related_events): 
    // Step 1: Filter only future or today's events
    $filtered_events = array_filter($related_events, function($event) {
        $event_date = get_field('event_date', $event->ID);
        return $event_date && strtotime(trim($event_date)) >= strtotime('today');
    });

    // Step 2: Sort by event_date ascending
    usort($filtered_events, function($a, $b) {
        return strtotime(get_field('event_date', $a->ID)) <=> strtotime(get_field('event_date', $b->ID));
    });

    // Step 3: If we still have events, show them
    if (!empty($filtered_events)): ?>
    
    <div class="related-events">
        <h3 class="text-2xl font-semibold text-gray-800 mb-4 text-center price-title custom-title">Related Events</h3>
        <div class="events-grid">
            <?php foreach ($filtered_events as $event):
                $event_date      = get_field('event_date', $event->ID);
                $event_end_date  = get_field('event_end_date', $event->ID);
                $formatted_event_date = 'To Be Confirmed';

                if ($event_date) {
                    $start_ts       = strtotime(trim($event_date));
                    $start_day_name = date('l', $start_ts);
                    $start_day      = date('jS', $start_ts);
                    $start_month    = date('F', $start_ts);
                    $start_year     = date('Y', $start_ts);

                    if ($event_end_date) {
                        $end_ts    = strtotime(trim($event_end_date));
                        $end_day   = date('jS', $end_ts);
                        $end_month = date('F', $end_ts);
                        $end_year  = date('Y', $end_ts);

                        if ($start_month === $end_month && $start_year === $end_year) {
                            // Same month and year
                            $formatted_event_date = "{$start_day} {$start_month} – {$end_day} {$start_month} {$start_year}";
                        } else {
                            // Different month or year
                            $formatted_event_date = "{$start_day_name} {$start_day} {$start_month} {$start_year} – {$end_day} {$end_month} {$end_year}";
                        }
                    } else {
                        // No end date
                        $formatted_event_date = "{$start_day_name} {$start_day} {$start_month} {$start_year}";
                    }
                }
            ?>
                <div class="event-item">
                    <?php if (has_post_thumbnail($event->ID)): ?>
                        <div class="event-image">
                            <a href="<?php echo get_permalink($event->ID); ?>">
                                <?php echo get_the_post_thumbnail($event->ID, 'medium'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <h4 class="text-xl font-semibold m-0 text-gray-800 text-left">
                        <a href="<?php echo get_permalink($event->ID); ?>">
                            <?php echo get_the_title($event->ID); ?>
                        </a>
                    </h4>
                    <p class="event-date"><?php echo esc_html($formatted_event_date); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; endif; ?>



		
   </div> <!-- end single-event mx-auto -->	
</div> <!-- end main wrapper -->





<!-- liht box start -->
<div id="lightbox-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-75 hidden z-50">
    <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white text-2xl font-bold close-btn">&times;</button>
    <div class="swiper-container lightbox-swiper w-full max-w-3xl">
        <div class="swiper-wrapper">
            <!-- Images will be dynamically injected here -->
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>
</div>

<!-- seat plan lightbox -->
<div id="seatPlanModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
        <!-- Close Button -->
        <button onclick="closeModal()" class="close-button absolute top-3 right-3 text-gray-600 hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>	
    <div class="max-w-[1000px] w-full relative popup-image-container">
        <!-- Image -->
        <img id="modalImage" src="" alt="Seat Plan" class="w-full h-auto">
    </div>
</div>


<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.gallery-swiper').forEach((gallery) => {
        new Swiper(gallery, {
            slidesPerView: 3,
            spaceBetween: 10,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            breakpoints: {
                0: {
                    slidesPerView: 1
                },
                640: {
                    slidesPerView: 2
                },
                1024: {
                    slidesPerView: 3
                }
            }
        });
    });
});

	
function showAccordionContent(event, id) {
    const accordionItem = event.currentTarget.closest('.accordion-item');
    const allContent = accordionItem.querySelectorAll('.accordion-content');

    allContent.forEach(content => content.classList.add('hidden'));

    const contentToShow = document.getElementById(id);
    contentToShow.classList.remove('hidden');

    accordionItem.querySelectorAll('.accordion-tab').forEach(tab => tab.classList.remove('active', 'bg-gray-300'));
    event.currentTarget.classList.add('active', 'bg-gray-300');

    if (typeof window.gf_global !== 'undefined' && typeof window.gf_global.gf_ajax_init !== 'undefined') {
        window.gf_global.gf_ajax_init(contentToShow);
    }
}

/* lightbox */
let lightboxSwiper;

function openLightbox(event, galleryImages) {
    event.preventDefault();
    const lightbox = document.getElementById('lightbox-modal');
    const swiperWrapper = lightbox.querySelector('.swiper-wrapper');

    swiperWrapper.innerHTML = '';

    galleryImages.forEach(image => {
        const slide = document.createElement('div');
        slide.classList.add('swiper-slide');
        slide.innerHTML = `<img src="${image.url}" alt="${image.alt}" class="w-full h-auto rounded-md lightbox-img">`;
        swiperWrapper.appendChild(slide);
    });

    lightbox.classList.remove('hidden');

if (lightboxSwiper) {
    lightboxSwiper.update();
} else {
    lightboxSwiper = new Swiper('.lightbox-swiper', {
        slidesPerView: 1,
        loop: false,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });
}

}

function closeLightbox() {
    const lightbox = document.getElementById('lightbox-modal');
    lightbox.classList.add('hidden');
}


/* seal plan lightbox */
function openModal(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('seatPlanModal').classList.remove('hidden');
}

// Close modal function
function closeModal() {
    document.getElementById('seatPlanModal').classList.add('hidden');
}

// Close when clicking outside the modal content
document.getElementById('seatPlanModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeModal();
    }
});
	
	
// Description gallary animation
document.addEventListener("DOMContentLoaded", () => {
  const items = document.querySelectorAll(".gallery-item");

  // Add animation class after DOM loads
  items.forEach(item => item.classList.add("animate-on-scroll"));

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
      if (entry.isIntersecting) {
        const item = entry.target;
        if (!item.classList.contains("visible")) {
          setTimeout(() => {
            item.classList.add("visible");
          }, index * 150);
        }
        observer.unobserve(item);
      }
    });
  }, {
    threshold: 0.1
  });

  items.forEach(item => observer.observe(item));
});

	
// gallery images on tab description
document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('.gallery').forEach(function (g) {
		const items = g.querySelectorAll(':scope > figure').length;
		let cols = items >= 5 ? 5 : items; // cap at 5
		if (items === 4) cols = 3;         // special case
		// apply inline style or a CSS custom property
		g.style.display = 'grid';
		g.style.gap = g.style.gap || '16px';
		g.style.gridTemplateColumns = `repeat(${Math.max(cols, 1)}, minmax(0, 1fr))`;
	});
});

</script>

<style>

::root  {
    --e-global-color-primary: #5BC9DE;
}
.main-event-wrapper h1,
.main-event-wrapper h2,
.main-event-wrapper p {
    font-family: Raleway !important;
}
.event-title-overlay h2 {
    font-size: 38px;
    font-weight: 600;
    line-height: 20px;
    letter-spacing: 0.2em;
}
.marginy-50 {
	margin: 50px 0;
}	 
.post-title {
	font-size: 30px;
	font-weight: 600;
	line-height: 1.2em;
}	 
.post-meta {
    font-size: 20px;
    font-weight: 600;
    line-height: 1.2em;
    letter-spacing: 0.2em;
}
.event-description p {
	text-align: center;
    color: #6e6e6e;
    font-size: 16px;
    font-weight: 500;
    line-height: 22px;
	margin-block-end: .9rem;
}
.price-title {
/* 	color: var(--e-global-color-primary); */
    font-size: 30px;
    font-weight: 700;
    text-transform: uppercase;
    line-height: 1.2em;
    letter-spacing: 0.2em;
	margin-block-end: .9rem;
} 
.bg-brand {
    background-color: var(--e-global-color-primary);
}
.accordion-tab:hover,
.accordion-tab.active {
    background-color: var(--e-global-color-primary);
	color: #fff;
}
.details-container h2 {
	font-size: 20px;
	font-weight: bold;
	margin-bottom: 10px;
}
.accordion-tab {
    flex-grow: 1;
    flex-basis: content;
	margin-left: 0 !important;
	border: 1px solid #000;
	padding: 15px 35px;
	font-weight: 700;
	font-size: 16px;
	color: #000;
	border-radius: 0;
	background: #fff;
}
.gform_button {
    background-color: var(--e-global-color-primary);
    border: none;
    color: var(--e-global-color-text);
}
.main-event-wrapper .single-event {
    max-width: 1280px !important;
    margin: 0 auto !important;
}
.accordion-content .gallery-swiper {
    position: relative;
    overflow: hidden;
}
.accordion-content p {
    color: #6e6e6e;
    font-size: 16px;
    font-weight: 500;
    line-height: 22px;
}
.swiper-button-prev:after,
.swiper-button-next:after {
    color: #000;
}	 
.swiper-pagination-bullet-active {
    background: #000 !important;
}	 
.gform_button:hover {
   background-color: #000 !important;
}
div:not(.partners-logo) .swiper-slide img:not(.lightbox-img) {
    aspect-ratio: 16 / 9;
    width: 100%;
    height: auto;
    object-fit: cover;
}
.partners-logo .swiper-slide img {
    aspect-ratio: inherit !important;
    width: inherit !important;
    height: inherit !important;
    object-fit: inherit !important;
}
/* lightbox */
#lightbox-modal.hidden {
    display: none;
}
#lightbox-modal img {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}
#lightbox-images img {
    transition: opacity 0.3s ease-in-out;
}
#lightbox-slider {
    max-height: 80%;
    overflow-y: auto;
}
#lightbox-images img:hover {
    opacity: 1;
}
#lightbox-modal {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: rgba(0, 0, 0, 0.9);
	z-index: 100;
}
.swiper-horizontal {
    overflow: hidden;
}
#lightbox-modal img {
    max-width: 100%;
    max-height: 100%;
    margin: 0 auto;
    display: block;
    border-radius: 0.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
}
#lightbox-modal .close-btn {
    margin: 28px 10px 0;
    border: none;
}
.swiper-button-prev:after,
.swiper-button-next:after {
	color: var(--e-global-color-text);
}
#lightbox-modal .close-btn:visited,
#lightbox-modal .close-btn:hover {
	background-color: transparent !important;
	border: none;
}
#seatPlanModal {
    z-index: 100;
}	
.close-button {
    background-color: #5CC9DE;
}
	 
/* related events */
	 
.related-events {
    margin: 2rem 0;
}
.events-grid {
    display: grid;
    gap: 1.5rem;
    margin-top: 1rem;
    justify-content: start;
	grid-template-columns: repeat(4, 1fr);
}
.event-item {
    border: 1px solid #eee;
    padding: 1rem;
    border-radius: 4px;
}
	 
.event-item h4 {
    margin-bottom: 0.3rem;
}
.event-image {
    margin-bottom: 1rem;
}
.related-events .event-image img {
    width: 100%;
    aspect-ratio: 5 / 3;
    object-fit: cover;
    border-radius: 4px;
}
.event-excerpt {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 1rem;
}
.event-title-overlay h1 {
    display: inline-block;
    padding: 8px 20px 5px;
}
#seatPlanModal button {
    border: 1px solid;
}
#seatPlanModal button:hover {
    background-color: var(--e-global-color-primary);
    color: var(--e-global-color-text);
}
.max-w-\[1000px\],
#modalImage {
    max-width: 800px;
	margin: 0 auto;
}
.popup-image-container {
	padding: 50px 0;
}
.location-tag {
    display: inline-block;
    border-top: 1px dashed #000;
    padding-top: 16px;
}
.main-event-description h3 {
    font-size: 18px;
	color: #343434;
}
a.gallery-link {
    pointer-events: none;
}
/* calendar date start */
	 
.calendar-range-wrapper {
	width: 100%;
    max-width: 400px;
	margin: 0 auto;
}
	 
.calendar-card {
  width: 180px;
  border-radius: 8px;
  overflow: hidden;
  font-family: Raleway !important;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  margin: 20px auto;
  background: url(/wp-content/uploads/2025/04/calendar-top.webp);
  background-size: contain;
  background-repeat: no-repeat;
  padding-top: 18px;
  background-position: top -12px center;
}

.calendar-year {
  background: #c60000;
  color: white;
  padding: 8px;
  font-weight: bold;
  text-align: center;
  font-size: 1rem;
}

.calendar-month {
  background: #eee;
  color: #333;
  padding: 12px 0;
  font-size: 1rem;
  text-align: center;
  font-weight: 600;
}

.calendar-day {
  font-size: 3rem;
  font-weight: bold;
  color: #000;
  text-align: center;
  padding: 12px 0 15px;
}

.calendar-weekday {
  background: #111;
  color: white;
  text-align: center;
  padding: 8px 0;
  font-size: 0.9rem;
  font-weight: bold;
}
	 
/* calendar date end */
	 
/* end related events */

	
/* Event Description gallery start */
	
.event-description .gallery {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
	margin-top: 50px;
    margin-bottom: 80px;
}
.event-description .gallery-item {
  opacity: 1;
  transform: translateY(0);
  transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

/* Add an animation-only class, not default state */
.event-description .gallery-item.animate-on-scroll {
  opacity: 0;
  transform: translateY(20px);
}

.event-description .gallery-item.animate-on-scroll.visible {
  opacity: 1;
  transform: translateY(0);
}
	
/* Event Description gallery end */
	
/* Event gallery on descrition tab */

.details-container .gallery {
  display: grid;
  gap: 16px; /* adjust spacing */
  grid-template-columns: repeat(5, minmax(0, 1fr));
}

/* --- Handle exact child counts on desktop --- */
.details-container .gallery:has(> figure:nth-child(1):last-child) {
  grid-template-columns: 1fr;
}
.details-container .gallery:has(> figure:nth-child(2):last-child) {
  grid-template-columns: repeat(2, minmax(0, 1fr));
}
.details-container .gallery:has(> figure:nth-child(3):last-child) {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}
/* special case for 4 */
.details-container .gallery:has(> figure:nth-child(4):last-child) {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}
/* 5 stays 5 (already default, explicit for clarity) */
.details-container .gallery:has(> figure:nth-child(5):last-child) {
  grid-template-columns: repeat(5, minmax(0, 1fr));
}

/* --- Tablet: force 2 columns always --- */
@media (max-width: 992px) {
  .details-container .gallery {
    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
  }
}

/* Make figures fill grid cell */
.details-container .gallery > .gallery-item {
  width: 100%;
}
.details-container .gallery .gallery-icon a,
.details-container .gallery .gallery-icon img {
  display: block;
  width: 100%;
  height: auto;
}



/* responsive start */
	
@media (max-width: 480px) {

.flex-mbl {
	flex-direction: column;
}
.events-grid {
	grid-template-columns: repeat(1, 1fr) !important;
}	
.related-events {
    padding: 0 10px;
}
.calendar-card {
    padding-top: 13px !important;
}
	
.post-title {
    font-size: 20px !important;
}
div:not(.partners-logo) .swiper-slide img:not(.lightbox-img) {
    aspect-ratio: 16 / 16;
}
.custom-title {
	font-size: 18px !important;
	letter-spacing: 0em !important;
}
.related-events h4.text-xl {
	font-size: 16px !important;
}
.event-description .gallery {
	grid-template-columns: repeat(2, 1fr) !important;
}
		 
}

/* --- Mobile: force 1 column always --- */
@media (max-width: 576px) {
  .details-container .gallery {
    grid-template-columns: 1fr !important;
  }
}

	 
@media (max-width: 768px) {

.related-events .event-image img {
    height: 200px !important;
}
.events-grid {
	grid-template-columns: repeat(2, 1fr);
}	

}
	
@media (max-width: 1024px) {
	
.event-description .gallery {
	grid-template-columns: repeat(3, 1fr);
}
	
}
	 
/* responsive end */
	 
	 
 </style>

<?php
    endwhile;
endif;

get_footer();
?>
