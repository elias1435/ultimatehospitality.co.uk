<?php
/* Template Name: Category Event Taxonomy */
get_header();

$term = get_queried_object(); // Current term
$taxonomy = $term->taxonomy;
$term_id = $term->term_id;

// ACF: Get banner image
$banner_image_id = get_field('event_category_image', "{$taxonomy}_{$term_id}");
$banner_image_url = wp_get_attachment_image_url($banner_image_id, 'full');
?>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

<?php if ($banner_image_url): ?>
    <div class="category-banner" style="position: relative; width: 100%; height: 355px; overflow: hidden;">
        <img src="<?php echo esc_url($banner_image_url); ?>" alt="<?php echo esc_attr($term->name); ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;">

        <div class="banner-title" style="
            position: absolute;
            bottom: 0px;
            left: 50%;
            transform: translateX(-50%);
            background: #0000005E;
			padding: 8px 20px 5px 20px;
			font-size: 30px;
			font-weight: 600;
			text-transform: uppercase;
			line-height: 35px;
			color: #fff;
        ">
            <?php echo esc_html($term->name); ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($term->description): ?>
    <div class="max-w-screen-xl mx-auto px-4">
        <div class="category-description text-center">
            <?php echo wp_kses_post(wpautop($term->description)); ?>
        </div>
    </div>
<?php endif; ?>

<?php
// Get direct child terms
$child_terms = get_terms([
    'taxonomy'   => $taxonomy,
    'parent'     => $term_id,
    'hide_empty' => false,
]);

// Cusom ACF term field sorting
if (!empty($child_terms)):
	usort($child_terms, function($a, $b) use ($taxonomy) {
		$date_a = get_field('event_category_date', "{$taxonomy}_{$a->term_id}");
		$date_b = get_field('event_category_date', "{$taxonomy}_{$b->term_id}");
		return strtotime($date_a) <=> strtotime($date_b); // ASC order ✅
	});
?>
    <div class="max-w-screen-xl mx-auto px-4 pt-6 pb-6">
        <h2 class="text-2xl font-bold mb-6 brand-color-title uppercase text-center" style="font-size: 28px;"><?php echo esc_html($term->name); ?></h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach ($child_terms as $child):
                $child_image_id = get_field('event_category_image', "{$taxonomy}_{$child->term_id}");
                $image_url = $child_image_id ? wp_get_attachment_image_url($child_image_id, 'full') : $banner_image_url;
                $event_date = get_field('event_category_date', "{$taxonomy}_{$child->term_id}");
				
				// Fetch First Event Price Query
                $child_event = new WP_Query([
                    'post_type'      => 'event',
                    'posts_per_page' => 1,
                    'tax_query'      => [[
                        'taxonomy'         => $taxonomy,
                        'field'            => 'term_id',
                        'terms'            => $child->term_id,
                        'include_children' => true, // ✅ important
                    ]],
                ]);

                $price = '';
                if ($child_event->have_posts()) {
                    $child_event->the_post();
                    $price = get_field('price_start_from');
                    wp_reset_postdata();
                }
            ?>
            <div class="flex flex-col justify-between rounded-lg overflow-hidden bg-white shadow-lg">
                <div class="img-title-description-wrapper">
                    <?php if ($image_url): ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($child->name); ?>" style="width: 100%; height: 250px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="p-5">
                        <h3 class="brand-color-title mb-2"><?php echo esc_html($child->name); ?></h3>
                        <?php if ($event_date): ?>
                        <div style="font-size: 14px; font-weight: 700; line-height: 20px; color: #696969;" class="mt-1"><?php echo esc_html($event_date); ?></div>
                        <?php endif; ?>
                        <?php if ($child->description): ?>
							<div class="event-des mt-2 mb-4 text-gray-400 text-sm event-description">
								<?php echo wp_kses_post(wpautop(wp_trim_words($child->description, 53, '...'))); ?>
							</div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex justify-between flex-col p-5">
                    <?php if ($price): ?>
                        <span class="price-from-text translate-y-5 transition-all duration-700" data-animate-on-scroll>From:</span>
                    <?php endif; ?>
                    <div class="flex justify-between items-center">
                        <div class="opacity-0 translate-y-5 transition-all duration-700 ease-out will-change-transform delay-150" data-animate-on-scroll>
                            <?php if ($price): ?>
                                <span class="subcategory-prices font-bold brand-color-title flex items-center">£<?php echo esc_html($price); ?>pp</span>
                            <?php endif; ?>
                        </div>
                        <div class="button-wrapper">
                            <a href="<?php echo esc_url(get_term_link($child)); ?>" class="cta-button flex flex-row gap-2 transition">
                                Search
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <?php
    $paged = max(1, get_query_var('paged') ?: get_query_var('page'));
    $events = new WP_Query([
        'post_type'      => 'event',
        'posts_per_page' => 12,
        'paged'          => $paged,
        'tax_query'      => [[
            'taxonomy'         => $taxonomy,
            'field'            => 'term_id',
            'terms'            => $term_id,
            'include_children' => false,
        ]],
    ]);

    if ($events->have_posts()): ?>
    <div class="max-w-screen-xl mx-auto px-4 pt-6 mb-10 single-events-main-category">
		<div id="event-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
			<?php while ($events->have_posts()): $events->the_post(); ?>
				<?php get_template_part('template-parts/event-card'); ?>
			<?php endwhile; ?>
		</div>
	
<?php if ($events->max_num_pages > 1): ?>
    <div class="text-center mt-6">
        <button id="load-more">Load More</button>
    </div>

    <script>
		document.addEventListener('DOMContentLoaded', function () {
			let page = <?php echo max(1, get_query_var('paged') ?: get_query_var('page')); ?>;
			const maxPage = <?php echo $events->max_num_pages; ?>;
			const button = document.getElementById('load-more');
			const container = document.getElementById('event-container');

			button?.addEventListener('click', function () {
				button.innerText = 'Loading...';

				fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
					method: 'POST',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: new URLSearchParams({
						action: 'load_more_events',
						page: page + 1,
						term_id: '<?php echo get_queried_object_id(); ?>',
						taxonomy: '<?php echo esc_attr($taxonomy); ?>'
					})
				})
				.then(response => response.text())
				.then(data => {
					if (data.trim()) {
						container.insertAdjacentHTML('beforeend', data);

						// 👇 Re-observe newly added animated elements
						document.querySelectorAll('[data-animate-on-scroll]').forEach(el => {
							observer.observe(el);
						});

						page++;
						if (page >= maxPage) {
							button.remove();
						} else {
							button.innerText = 'Load More';
						}
					} else {
						button.remove();
					}
				});
			});
		});

    </script>
<?php endif; ?>
	
		
</div>


    <?php else: ?>
    	<p class="max-w-screen-xl mx-auto px-4 pt-6 pb-6 text-black text-center">⚠️<br/>No events found in this category.</p>	
    <?php endif; ?>

<?php endif; ?>

<div class="max-w-screen-xl mx-auto px-4 pb-10">
	<h3 class="solid-dark mb-2 title-text font-semibold">CAN'T FIND WHAT YOU'RE LOOKING FOR?</h3>
	<p class="solid-dark">If you can't find the ideal VIP hospitality package or the event you were looking for, it may be because it's not on our website just yet. However, we have a massive catalogue and it is highly likely that we can help you. <a href="/contact-us/" style="text-decoration: underline;">Send us an enquiry</a> now and one of our experienced corporate hospitality specialists will get back to you as soon as possible.</p>
</div>




<style>
.category-description {
	margin: 30px 0;
	font-weight: 400;
	color: #000000;
	font-size: 16px;
	line-height: 24px;
	font-family: var(--e-global-typography-text-font-family), Sans-serif;
}
.category-description h3 {
	font-weight: 700 !important;
	font-size: 22px !important;
	margin: 8px 16px;
}
.category-description strong {
	font-weight: 700 !important;
	font-size: 22px !important;
}
.event-des {
    font-family: "Raleway", Sans-serif;
    font-size: 18px;
    font-weight: 400;
    line-height: 28px;
    color: var(--e-global-color-accent);
}
.brand-color-title {
    font-family: "Raleway", Sans-serif;
    font-size: 22px;
    font-weight: 600;
    text-transform: uppercase;
    line-height: 37px;
    color: var(--e-global-color-secondary);
}
.brand-color {
   color: var(--e-global-color-primary);
}
.price-wrapper {
   display: flex;
   flex-direction: column;
}
.category-description p {
    margin-block-start: 0;
    margin-block-end: .9rem;
}
#load-more {
    background-color: var(--e-global-color-primary);
    font-family: "Raleway", Sans-serif;
    font-size: 15px;
    font-weight: 700;
    text-transform: uppercase;
    fill: var(--e-global-color-text);
    color: var(--e-global-color-text);
    border-radius: 30px 30px 30px 30px;
    padding: 18px 38px 18px 38px;
	border-color: var(--e-global-color-primary);
}
.cta-button:hover,
#load-more:hover {
    background-color: var(--e-global-color-text);
    color: var(--e-global-color-primary) !important;
	border: 1px solid var(--e-global-color-primary) !important;
}
.cta-button {
    background-color: var(--e-global-color-primary);
    font-family: "Raleway", Sans-serif;
    font-size: 15px;
    font-weight: 700;
    text-transform: uppercase;
    fill: var(--e-global-color-text);
    color: var(--e-global-color-text);
    border-radius: 30px 30px 30px 30px;
    padding: 18px 38px 18px 38px;
	float: right;
	border: 1px solid var(--e-global-color-primary) !important;
}
.cta-button:hover {
    color: var(--e-global-color-text);
}
.event-description {
    font-family: "Raleway", Sans-serif;
    font-size: 18px;
    font-weight: 400;
    line-height: 28px;
    color: var(--e-global-color-secondary);
}
.subcategory-prices {
	font-weight: 800 !important;
	color: var(--e-global-color-primary) !important;
	font-family: 'Roboto Mono', monospace !important;
    font-size: 24px !important;
}
.tdp-wrapper {
	display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.price-from-text {
    font-family: var(--e-global-typography-text-font-family), Sans-serif;
    font-weight: var(--e-global-typography-text-font-weight);
    color: var(--e-global-color-secondary);
}
.cta-button svg {
	width: 23px;
}
.single-event-pagination ul {
    display: flex;
    column-gap: 15px;
    align-content: center;
}
.event-dates-tbc {
    font-size: 15px;
    font-weight: 700;
    line-height: 25px;
	text-transform: uppercase;
    color: var(--e-global-color-secondary) !important;
    font-family: var(--e-global-typography-primary-font-family), Sans-serif;
}

/* animation start */
@keyframes fadeInUpSlow {
0% {
opacity: 0;
	transform: translateY(20px);
}
100% {
opacity: 1;
	transform: translateY(0);
}
}

.animate-fade-in-up-slow {
	animation: fadeInUpSlow 1.5s ease-out forwards;
}

/* animation end */

.title-text {
	font-size: 22px;
}
.solid-dark {
	color: #000000;
}

@media (min-width: 1280px) {
  .max-w-screen-xl {
    max-width: 1280px;
  }
}

@media (max-width: 480px) {

.banner-title {
    width: 100%;
    text-align: center;
}


}

</style>

<script>
let observer;

function initIntersectionObserver() {
    observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.remove('opacity-0', 'translate-y-5');
                entry.target.classList.add('opacity-100', 'translate-y-0');
                observer.unobserve(entry.target); // Only animate once
            }
        });
    }, {
        threshold: 0.1
    });

    document.querySelectorAll('[data-animate-on-scroll]').forEach(el => {
        observer.observe(el);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initIntersectionObserver();
});


	
</script>


<?php get_footer(); ?>
