<?php

// ACF Admin CSS
function my_acf_admin_head() {
    ?>
    <style type="text/css">
        /* Add a top border to the first cell in each row */
        .acf-field-repeater .acf-repeater table.acf-table tbody tr.acf-row > td {
            border-top: 2px solid #5bc9de !important;
        }
    </style>
    <?php
}
add_action('acf/input/admin_head', 'my_acf_admin_head');


// Event fitler AJAX request
// Reusable helper to format ordinal day (e.g. 1st, 2nd)
function format_ordinal($date) {
    $timestamp = strtotime($date);
    return date('jS', $timestamp); // e.g., 1st, 2nd
}
function filter_events_ajax() {
    $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;

    $args = array(
        'post_type' => 'event',
        'posts_per_page' => 150,
        'paged' => $paged,
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'meta_key' => 'event_date',
        'meta_type' => 'DATE',
    );

    if (!empty($_GET['event_name'])) {
        $args['p'] = $_GET['event_name'];
    }

    if (!empty($_GET['event_location'])) {
        $args['meta_query'] = array(
            array(
                'key' => 'event_location',
                'value' => sanitize_text_field($_GET['event_location']),
                'compare' => 'LIKE',
            ),
        );
    }

    if (!empty($_GET['event_category'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'event-category',
                'field' => 'id',
                'terms' => sanitize_text_field($_GET['event_category']),
                'operator' => 'IN',
            ),
        );
    }

    if (!empty($_GET['search'])) {
        $args['s'] = sanitize_text_field($_GET['search']);
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) :
        $table = '';

        while ($query->have_posts()) : $query->the_post();
            $event_date = get_post_meta(get_the_ID(), 'event_date', true);
            $event_end_date = get_post_meta(get_the_ID(), 'event_end_date', true);
            $event_location = get_post_meta(get_the_ID(), 'event_location', true);
            $event_categories = wp_get_post_terms(get_the_ID(), 'event-category');

            // Build date display
			$formatted_date = '';

			if (!empty($event_date) && strtotime($event_date)) {
				$start_timestamp = strtotime($event_date);
				$start_day = date('jS', $start_timestamp); // 1st, 2nd, etc.
				$start_month = date('F', $start_timestamp);

				if (!empty($event_end_date) && strtotime($event_end_date)) {
					$end_timestamp = strtotime($event_end_date);

					if (date('Y-m-d', $start_timestamp) === date('Y-m-d', $end_timestamp)) {
						$formatted_date = "{$start_day} {$start_month}";
					} else {
						$end_day = date('jS', $end_timestamp);
						$end_month = date('F', $end_timestamp);

						if ($start_month === $end_month) {
							$formatted_date = "{$start_day} – {$end_day} {$start_month}";
						} else {
							$formatted_date = "{$start_day} {$start_month} – {$end_day} {$end_month}";
						}
					}
				} else {
					$formatted_date = "{$start_day} {$start_month}";
				}
			} else {
				$formatted_date = 'To Be Confirmed';
			}


            $categories = array_map(function($cat) {
                return $cat->name;
            }, $event_categories);

            $table .= '<tr>';
            $table .= '<td class="px-4 py-2 border-b border-gray-200">' . esc_html($formatted_date) . '</td>';
            $table .= '<td class="px-4 py-2 border-b border-gray-200"><a href="' . get_permalink() . '" class="text-blue-500 hover:underline">' . get_the_title() . '</a></td>';
            $table .= '<td class="px-4 py-2 border-b border-gray-200">' . esc_html($event_location) . '</td>';
            $table .= '<td class="px-4 py-2 border-b border-gray-200 category-column">' . implode(', ', $categories) . '</td>';
            $table .= '</tr>';
        endwhile;

        $pagination = paginate_links(array(
            'total' => $query->max_num_pages,
            'current' => max(1, $paged),
            'format' => '?paged=%#%',
            'type' => 'list',
        ));

        wp_reset_postdata();
    else :
        $table = '<tr><td colspan="4">No events found.</td></tr>';
        $pagination = '';
    endif;

    echo json_encode(array(
        'table' => $table,
        'pagination' => $pagination,
    ));
    die();
}

add_action('wp_ajax_filter_events', 'filter_events_ajax');
add_action('wp_ajax_nopriv_filter_events', 'filter_events_ajax');



// search in category
function enqueue_event_category_search_script($hook) {
    global $post_type;

    // Load script only on "Event" post edit/add page
    if ($post_type == 'event' && ($hook == 'post.php' || $hook == 'post-new.php')) {
        wp_enqueue_script('jquery'); // Ensure jQuery is loaded

        // Add inline JavaScript directly in functions.php
        wp_add_inline_script('jquery', '
            jQuery(document).ready(function ($) {
                // Insert search box above the category list
                var searchBox = $("<input>", {
                    type: "text",
                    id: "event-category-search",
                    placeholder: "Search Event Categories...",
                    style: "width: 100%; margin-bottom: 10px; padding: 5px;"
                });

                $("#event-categorydiv .inside").prepend(searchBox);

                // Function to filter checkboxes
                $("#event-category-search").on("keyup", function () {
                    var searchTerm = $(this).val().toLowerCase();

                    $("#event-categorydiv .categorychecklist label").each(function () {
                        var categoryLabel = $(this).text().toLowerCase();

                        if (categoryLabel.includes(searchTerm)) {
                            $(this).closest("li").show();
                        } else {
                            $(this).closest("li").hide();
                        }
                    });
                });
            });
        ');
    }
}
add_action('admin_enqueue_scripts', 'enqueue_event_category_search_script');


// pagination
function load_more_events_ajax_handler() {
    $page     = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $term_id  = isset($_POST['term_id']) ? intval($_POST['term_id']) : 0;
    $taxonomy = sanitize_text_field($_POST['taxonomy']);

    $events = new WP_Query([
        'post_type'      => 'event',
        'posts_per_page' => 12,
        'paged'          => $page,
		
        'meta_key'       => 'event_date',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_type'      => 'DATE',
		
        'tax_query'      => [[
            'taxonomy'         => $taxonomy,
            'field'            => 'term_id',
            'terms'            => $term_id,
            'include_children' => false,
        ]],
    ]);

    if ($events->have_posts()) :
        while ($events->have_posts()): $events->the_post();
            // Render each event card here the same way as in main loop
            get_template_part('template-parts/event-card'); // or copy-paste the card HTML directly
        endwhile;
        wp_reset_postdata();
    endif;

    wp_die();
}
add_action('wp_ajax_load_more_events', 'load_more_events_ajax_handler');
add_action('wp_ajax_nopriv_load_more_events', 'load_more_events_ajax_handler');

