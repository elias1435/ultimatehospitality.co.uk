<?php
$price = get_field('price_start_from');
$thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'large');
$excerpt = get_the_excerpt();
$event_types = wp_get_post_terms(get_the_ID(), 'event-type', ['fields' => 'slugs']);
$link_type = in_array('event-details', $event_types) ? 'details' : (in_array('enquiry-now', $event_types) ? 'enquiry' : null);
$link = ($link_type === 'details') ? get_permalink() : (($link_type === 'enquiry') ? '/contact-us/' : '#');
$button_text = ($link_type === 'details') ? 'Search' : 'Contact';

$event_date = get_field('event_date');
$event_end_date = get_field('event_end_date');

// Safe: define function only if not already defined
if (!function_exists('format_ordinal')) {
	function format_ordinal($date) {
		$timestamp = strtotime($date);
		return $timestamp ? date('jS', $timestamp) : '';
	}
}

$formatted_event_date = 'To Be Confirmed';

$start_ts = strtotime($event_date);
$end_ts = strtotime($event_end_date);

if ($start_ts) {
	$start_day = format_ordinal($event_date);
	$start_month = date('F', $start_ts);

	if ($end_ts) {
		if (date('Y-m-d', $start_ts) === date('Y-m-d', $end_ts)) {
			$formatted_event_date = "{$start_day} {$start_month}";
		} else {
			$end_day = format_ordinal($event_end_date);
			$end_month = date('F', $end_ts);

			if ($start_month === $end_month) {
				$formatted_event_date = "{$start_day} – {$end_day} {$start_month}";
			} else {
				$formatted_event_date = "{$start_day} {$start_month} – {$end_day} {$end_month}";
			}
		}
	} else {
		$formatted_event_date = "{$start_day} {$start_month}";
	}
}
?>

<div class="flex flex-col justify-between rounded-lg overflow-hidden bg-white shadow-lg">
	<div class="img-title-description-wrapper">
		<?php if ($thumbnail): ?>
			<img src="<?php echo esc_url($thumbnail); ?>" alt="<?php the_title_attribute(); ?>" style="width: 100%; height: 250px; object-fit: cover;">
		<?php endif; ?>
		<div class="p-4">
			<h3 class="brand-color-title mb-2">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h3>
			<p class="event-dates-tbc opacity-0 translate-y-5 transition-all duration-700" data-animate-on-scroll><?php echo esc_html($formatted_event_date); ?></p>
			<p class="event-des mt-2 mb-4 text-gray-400 text-sm event-description"><?php echo esc_html($excerpt); ?></p>
		</div>
	</div>
	<div class="flex justify-between flex-col p-5">
		<?php if ($price): ?>
			<span class="text-gray-800 price-from-text opacity-0 translate-y-5 transition-all duration-700" data-animate-on-scroll>From:</span>
		<?php endif; ?>
		<div class="flex justify-between items-center">
			<div class="text-gray-800 price-wrapper opacity-0 translate-y-5 transition-all duration-700" data-animate-on-scroll>
				<?php if ($price): ?>
					<span class="subcategory-prices font-bold brand-color-title flex items-center">£<?php echo esc_html($price); ?>pp</span>
				<?php endif; ?>
			</div>
			<div class="button-wrapper">
				<a href="<?php echo esc_url($link); ?>" class="cta-button transition transition">
					<?php echo esc_html($button_text); ?>
				</a>
			</div>
		</div>
	</div>
</div>
