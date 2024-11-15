<?php
/* Template for displaying single Event custom post type */

get_header();

if (have_posts()) :
    while (have_posts()) : the_post();
        $event_date = get_field('event_date');
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
    <div class="event-hero bg-cover bg-center" style="background-image: url('<?php echo esc_url($featured_image_url); ?>');">
        <div class="event-title-overlay text-4xl content-center h-96 font-normal text-white text-center bg-black bg-opacity-50 py-4">
<!--             <h2 class="font-normal text-white text-center uppercase"><?php // echo esc_html($event_type); ?></h2> -->
			<h1 class="font-normal text-white text-center text-4xl uppercase post-title"><?php the_title(); ?></h1>
        </div>
    </div>

    <div class="single-event">
        <div class="bg-brand marginy-50">
            <div class="p-10">
                <div class="text-center">
                    <p class="text-xl text-white uppercase post-meta"><?php echo esc_html($event_date); ?></p>
                    <p class="mt-4 text-xl text-white uppercase post-meta"><?php echo esc_html($event_location); ?></p>
                </div>
            </div>
        </div>

        <div class="event-description px-4 mt-4">
            <?php the_content(); ?>
        </div>

        <?php if ($packages) : ?>
            <div class="event-packages mt-8 px-4">
                <?php foreach ($packages as $index => $package) : ?>
                    <div class="accordion-item marginy-50">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-4 text-center price-title"><?php echo esc_html($package['package_title']); ?></h3>

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
                        </div>

                        <!-- Conditional Content Display -->
                        <div class="accordion-content-container bg-white p-2">
		
							<?php if (!empty($package['gallery'])) : ?>
								<div id="gallery-<?php echo $index; ?>" class="accordion-content">
									<div class="swiper-container gallery-swiper my-4">
										<div class="swiper-wrapper">
											<?php foreach ($package['gallery'] as $image) : ?>
												<div class="swiper-slide">
													<a href="<?php echo esc_url($image['url']); ?>" 
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
									<a href="<?php echo esc_url($package['seat_plan']['url']); ?>" 
									   onclick="openLightbox(event, '<?php echo esc_url($package['seat_plan']['url']); ?>')" 
									   class="cursor-pointer">
										<img src="<?php echo esc_url($package['seat_plan']['url']); ?>" 
											 alt="<?php echo esc_attr($package['seat_plan']['alt'] ?? 'Seat Plan'); ?>" 
											 class="w-full h-auto rounded-md hover:opacity-80 transition-opacity" />
									</a>
								</div>
							<?php endif; ?>
							
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

	
	
	
	
<?php
$related_events = get_field('related_events');

if ($related_events): ?>
    <div class="related-events">
        <h3 class="text-2xl font-semibold text-gray-800 mb-4 text-center price-title">Related Events</h3>
        <div class="events-grid">
            <?php foreach ($related_events as $event): ?>
                <div class="event-item">
                    <?php 
                    // Optional: Display featured image if available
                    if (has_post_thumbnail($event->ID)): ?>
                        <div class="event-image">
                            <a href="<?php echo get_permalink($event->ID); ?>"><?php echo get_the_post_thumbnail($event->ID, 'medium'); ?></a>
                        </div>
                    <?php endif; ?>
					<h4 class="text-xl font-semibold m-0 text-gray-800 text-left">
                        <a href="<?php echo get_permalink($event->ID); ?>">
                            <?php echo get_the_title($event->ID); ?>
                        </a>					
					</h4>
                    <?php 
                    $event_date = get_field('event_date', $event->ID);
                    if ($event_date): ?>
                        <p class="event-date">
                            <?php echo esc_html($event_date); ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

	
	
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
	color: var(--e-global-color-primary);
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
.swiper-slide img:not(.lightbox-img) {
    aspect-ratio: 16 / 9;
    width: 100%;
    height: auto;
    object-fit: cover;
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
	
	 
/* related events */
	 
.related-events {
    margin: 2rem 0;
}

.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
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
.event-image img {
    width: 100%;
    height: auto;
    border-radius: 4px;
}
.event-excerpt {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 1rem;
}
.event-date {

}

	 
	 
/* end related events */
	 
	 
/* responsive start */
	
@media (max-width: 480px) {

.flex-mbl {
	flex-direction: column;
}
		 
}
	 
	 
/* responsive end */
	 
	 
 </style>

<?php
    endwhile;
endif;

get_footer();
?>
