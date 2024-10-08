<?php
/*
Plugin Name: Animated Timeline
Description: Creates an animated timeline with SVG and JavaScript
Version: 1.0
Author: nexTab & Unleashed Design
Author URI: https://nextab.de
Text Domain: nxt-timeline
*/

#region Enqueue Scripts in frontend
add_action( 'wp_enqueue_scripts', 'nxt_timeline_enqueue_scripts', 999999);
function nxt_timeline_enqueue_scripts() {
	global $post;
	if (strpos($post->post_content, 'svg-target') === false) return;
	wp_register_script('timeline', plugin_dir_url(__FILE__) . 'js/timeline.js', false, '1.0', true);
	
	$options = get_option('nxt_timeline_options');
	wp_localize_script('timeline', 'nxtTimelineOptions', $options);
	
	wp_enqueue_script('timeline');
}
#endregion Enqueue Scripts in frontend

#region Enqueue Scripts in WP backend
function nxt_timeline_enqueue_color_picker($hook_suffix) {
    if ('settings_page_nxt_timeline' !== $hook_suffix) {
        return;
    }

    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('nxt-timeline-color-picker', plugin_dir_url(__FILE__) . 'js/nxt-timeline-color-picker.js', array('jquery', 'wp-color-picker'), '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'nxt_timeline_enqueue_color_picker');
#endregion Enqueue Scripts in WP backend

#region Admin Page WP
// Add a menu item under the Settings menu
add_action('admin_menu', 'nxt_timeline_add_admin_menu');
function nxt_timeline_add_admin_menu() {
    add_options_page('Animated Timeline Settings', 'Animated Timeline', 'manage_options', 'nxt_timeline', 'nxt_timeline_options_page');
}

// Add settings link on plugin overview page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'nxt_timeline_add_settings_link');
function nxt_timeline_add_settings_link($links) {
	$settings_link = '<a href="options-general.php?page=nxt_timeline">Settings</a>';
	array_unshift($links, $settings_link);
	return $links;
}
#endregion Admin Page WP

#region Settings for Admin Page
// Register settings
add_action('admin_init', 'nxt_timeline_settings_init');
function nxt_timeline_settings_init() {
    register_setting('nxt_timeline', 'nxt_timeline_options');

    // Timeline Stops Section
    add_settings_section(
        'nxt_timeline_stops_section',
        'Timeline Stops',
        'nxt_timeline_stops_section_callback',
        'nxt_timeline'
    );

	add_settings_field(
        'offset_x',
        'Offset X',
        'nxt_timeline_offset_x_render',
        'nxt_timeline',
        'nxt_timeline_stops_section'
    );

    add_settings_field(
        'offset_y',
        'Offset Y',
        'nxt_timeline_offset_y_render',
        'nxt_timeline',
        'nxt_timeline_stops_section'
    );

    add_settings_field(
        'element_type',
        'Element Type',
        'nxt_timeline_element_type_render',
        'nxt_timeline',
        'nxt_timeline_stops_section'
    );

    add_settings_field(
        'element_stroke_width',
        'Element Stroke Width',
        'nxt_timeline_element_stroke_width_render',
        'nxt_timeline',
        'nxt_timeline_stops_section'
    );

    // Timeline Path Section
    add_settings_section(
        'nxt_timeline_path_section',
        'Timeline Path',
        'nxt_timeline_path_section_callback',
        'nxt_timeline'
    );
    
    add_settings_field(
        'path_style',
        'Path Style',
        'nxt_timeline_path_style_render',
        'nxt_timeline',
        'nxt_timeline_path_section'
    );

	add_settings_field(
		'path_dash_length',
		'Path Dash Length',
		'nxt_timeline_path_dash_length_render',
		'nxt_timeline',
		'nxt_timeline_path_section'
	);

	add_settings_field(
		'path_dash_gap',
		'Path Dash Gap',
		'nxt_timeline_path_dash_gap_render',
		'nxt_timeline',
		'nxt_timeline_path_section'
	);

    add_settings_field(
        'path_width',
        'Path Width',
        'nxt_timeline_path_width_render',
        'nxt_timeline',
        'nxt_timeline_path_section'
    );

    add_settings_field(
        'animated_path_width',
        'Animated Path Width',
        'nxt_timeline_animated_path_width_render',
        'nxt_timeline',
        'nxt_timeline_path_section'
    );

	// Customize Colors Section
    add_settings_section(
        'nxt_timeline_colors_section',
        'Customize Colors',
        'nxt_timeline_color_section_callback',
        'nxt_timeline'
    );

	$color_fields = [
        'element_fill_color',
        'element_stroke_color',
        'path_color',
        'animated_path_color'
    ];

	foreach ($color_fields as $field) {
        add_settings_field(
            $field . '_type',
            ucfirst(str_replace('_', ' ', $field)) . ' Type',
            'nxt_timeline_color_type_render',
            'nxt_timeline',
            'nxt_timeline_colors_section',
            ['field' => $field]
        );
    }

	// Customize the shape of the timeline path
	add_settings_section(
		'nxt_timeline_shape_section',
		'Customize Path Bend',
		'nxt_timeline_shape_section_callback',
		'nxt_timeline'
	);

	add_settings_field(
        'path_curve_vertical_offset',
        'Path Curve Vertical Offset',
        'nxt_timeline_path_curve_vertical_offset_render',
        'nxt_timeline',
        'nxt_timeline_shape_section'
    );

	add_settings_field(
        'path_curve_roundness',
        'Path Curve Roundness',
        'nxt_timeline_path_curve_roundness_render',
        'nxt_timeline',
        'nxt_timeline_shape_section'
    );

    add_settings_field(
        'path_curve_horizontal_offset',
        'Path Curve Horizontal Offset',
        'nxt_timeline_path_curve_horizontal_offset_render',
        'nxt_timeline',
        'nxt_timeline_shape_section'
    );

    add_settings_field(
        'path_curve_correct_last_y',
        'Path Correct Last Y Offset',
        'nxt_timeline_path_curve_correct_last_y_render',
        'nxt_timeline',
        'nxt_timeline_shape_section'
    );
}

function nxt_timeline_stops_section_callback() {
    echo '<p>Customize the appearance of timeline stops.</p>';
}

function nxt_timeline_path_section_callback() {
    echo '<p>Customize the appearance of the timeline path.</p>';
}

function nxt_timeline_color_section_callback() {
	echo '<p>Customize the colors of the timeline elements.</p>';
}

function nxt_timeline_shape_section_callback() {
	echo '<p>Customize the shape of the timeline path.</p>';
}
#endregion Settings for Admin Page

#region Render functions for each setting
function nxt_timeline_offset_x_render() {
    $options = get_option('nxt_timeline_options');
    ?>
    <input type='number' name='nxt_timeline_options[offset_x]' value='<?php echo $options['offset_x'] ?? 40; ?>'>
    <?php
}

function nxt_timeline_offset_y_render() {
    $options = get_option('nxt_timeline_options');
    ?>
    <input type='number' name='nxt_timeline_options[offset_y]' value='<?php echo $options['offset_y'] ?? 20; ?>'>
    <?php
}

function nxt_timeline_element_type_render() {
    $options = get_option('nxt_timeline_options');
    ?>
    <select name='nxt_timeline_options[element_type]'>
        <option value='circle' <?php selected($options['element_type'] ?? 'circle', 'circle'); ?>>Circle</option>
        <option value='square' <?php selected($options['element_type'] ?? 'circle', 'square'); ?>>Square</option>
        <option value='none' <?php selected($options['element_type'] ?? 'circle', 'none'); ?>>None</option>
    </select>
    <?php
}

function nxt_timeline_element_fill_color_render() {
    $options = get_option('nxt_timeline_options');
    ?>
    <input type='color' name='nxt_timeline_options[element_fill_color]' value='<?php echo $options['element_fill_color'] ?? '#ffffff'; ?>'>
    <?php
}

function nxt_timeline_element_stroke_color_render() {
    $options = get_option('nxt_timeline_options');
    ?>
    <input type='color' name='nxt_timeline_options[element_stroke_color]' value='<?php echo $options['element_stroke_color'] ?? '#6c1300'; ?>'>
    <?php
}

function nxt_timeline_element_stroke_width_render() {
    $options = get_option('nxt_timeline_options');
    ?>
    <input type='number' name='nxt_timeline_options[element_stroke_width]' value='<?php echo $options['element_stroke_width'] ?? 4; ?>'>
    <?php
}

function nxt_timeline_path_color_render() {
    $options = get_option('nxt_timeline_options');
    ?>
    <input type='color' name='nxt_timeline_options[path_color]' value='<?php echo $options['path_color'] ?? '#25536E33'; ?>'>
    <?php
}

function nxt_timeline_path_style_render() {
    $options = get_option('nxt_timeline_options');
    ?>
    <select name='nxt_timeline_options[path_style]' id='path_style'>
        <option value='solid' <?php selected($options['path_style'], 'solid'); ?>>Solid</option>
        <option value='dashed' <?php selected($options['path_style'], 'dashed'); ?>>Dashed</option>
    </select>
    <script>
        jQuery(document).ready(function($) {
            $('#path_style').on('change', function() {
                var isDashed = $(this).val() === 'dashed';
                $('input[name="nxt_timeline_options[path_dash_length]"]').prop('disabled', !isDashed);
                $('input[name="nxt_timeline_options[path_dash_gap]"]').prop('disabled', !isDashed);
            });
        });
    </script>
    <?php
}

function nxt_timeline_path_dash_length_render() {
    $options = get_option('nxt_timeline_options');
    ?>
    <input type='number' name='nxt_timeline_options[path_dash_length]' min='1' 
           value='<?php echo isset($options['path_dash_length']) ? $options['path_dash_length'] : 10; ?>'
           <?php echo $options['path_style'] !== 'dashed' ? 'disabled' : ''; ?>>
    <?php
}

function nxt_timeline_path_dash_gap_render() {
    $options = get_option('nxt_timeline_options');
    ?>
    <input type='number' name='nxt_timeline_options[path_dash_gap]' min='1' 
           value='<?php echo isset($options['path_dash_gap']) ? $options['path_dash_gap'] : 5; ?>'
           <?php echo $options['path_style'] !== 'dashed' ? 'disabled' : ''; ?>>
    <?php
}

function nxt_timeline_path_width_render() {
    $options = get_option('nxt_timeline_options');
    ?>
    <input type='number' name='nxt_timeline_options[path_width]' value='<?php echo $options['path_width'] ?? 3; ?>'>
    <?php
}

function nxt_timeline_animated_path_color_render() {
    $options = get_option('nxt_timeline_options');
    ?>
    <input type='color' name='nxt_timeline_options[animated_path_color]' value='<?php echo $options['animated_path_color'] ?? '#25536E'; ?>'>
    <?php
}

function nxt_timeline_animated_path_width_render() {
    $options = get_option('nxt_timeline_options');
    ?>
    <input type='number' name='nxt_timeline_options[animated_path_width]' value='<?php echo $options['animated_path_width'] ?? 3; ?>'>
    <?php
}

function nxt_timeline_path_curve_roundness_render() {
    $options = get_option('nxt_timeline_options');
    $value = $options['path_curve_roundness'] ?? 80;
    ?>
    <input type='range' name='nxt_timeline_options[path_curve_roundness]' min='0' max='200' value='<?php echo $value; ?>'>
    <span class="path_curve_roundness_value"><?php echo $value; ?></span>
    <script>
        document.querySelector('input[name="nxt_timeline_options[path_curve_roundness]"]').addEventListener('input', function(e) {
            document.querySelector('.path_curve_roundness_value').textContent = e.target.value;
        });
    </script>
    <?php
}

function nxt_timeline_path_curve_vertical_offset_render() {
    $options = get_option('nxt_timeline_options');
    $value = $options['path_curve_vertical_offset'] ?? 85;
    ?>
    <input type='range' name='nxt_timeline_options[path_curve_vertical_offset]' min='0' max='200' value='<?php echo $value; ?>'>
    <span class="path_curve_vertical_offset_value"><?php echo $value; ?></span>
    <script>
        document.querySelector('input[name="nxt_timeline_options[path_curve_vertical_offset]"]').addEventListener('input', function(e) {
            document.querySelector('.path_curve_vertical_offset_value').textContent = e.target.value;
        });
    </script>
    <?php
}

function nxt_timeline_path_curve_horizontal_offset_render() {
    $options = get_option('nxt_timeline_options');
    $value = $options['path_curve_horizontal_offset'] ?? 100;
    ?>
    <input type='range' name='nxt_timeline_options[path_curve_horizontal_offset]' min='0' max='200' value='<?php echo $value; ?>'>
    <span class="path_curve_horizontal_offset_value"><?php echo $value; ?></span>
    <script>
        document.querySelector('input[name="nxt_timeline_options[path_curve_horizontal_offset]"]').addEventListener('input', function(e) {
            document.querySelector('.path_curve_horizontal_offset_value').textContent = e.target.value;
        });
    </script>
    <?php
}

function nxt_timeline_path_curve_correct_last_y_render() {
    $options = get_option('nxt_timeline_options');
    $value = $options['path_curve_correct_last_y'] ?? 0;
    ?>
    <input type='range' name='nxt_timeline_options[path_curve_correct_last_y]' min='-100' max='100' value='<?php echo $value; ?>'>
    <span class="path_curve_correct_last_y_value"><?php echo $value; ?></span>
    <script>
        document.querySelector('input[name="nxt_timeline_options[path_curve_correct_last_y]"]').addEventListener('input', function(e) {
            document.querySelector('.path_curve_correct_last_y_value').textContent = e.target.value;
        });
    </script>
    <?php
}

function nxt_timeline_color_type_render($args) {
    $options = get_option('nxt_timeline_options');
    $field = $args['field'];
    $type = $options[$field . '_type'] ?? 'color';
    $color_value = $options[$field] ?? '#000';
    $css_var_value = $options[$field . '_css_var'] ?? '';
    ?>
    <select name='nxt_timeline_options[<?php echo $field; ?>_type]' class='color-type-select' data-field='<?php echo $field; ?>'>
        <option value='color' <?php selected($type, 'color'); ?>>Color</option>
        <option value='css_var' <?php selected($type, 'css_var'); ?>>CSS Variable</option>
    </select>
    <div class='color-input-container' <?php echo $type === 'css_var' ? 'style="display:none;"' : 'style="display: inline-block; vertical-align: top; margin-left: 0.5rem;"'; ?>>
        <input type='text' class='color-picker-input' name='nxt_timeline_options[<?php echo $field; ?>]' value='<?php echo esc_attr($color_value); ?>' data-alpha-enabled="true">
    </div>
    <input type='text' class='css-var-input' name='nxt_timeline_options[<?php echo $field; ?>_css_var]' value='<?php echo esc_attr($css_var_value); ?>' placeholder='var(--color-name)' <?php echo $type === 'color' ? 'style="display:none;"' : 'style="display: inline-block; vertical-align: top; margin-left: 0.5rem;"'; ?>>
    <?php
}

#endregion Render functions for each setting

#region Create the options page
function nxt_timeline_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h1>Animated Timeline Settings</h1>
        <?php
        settings_fields('nxt_timeline');
        do_settings_sections('nxt_timeline');
        submit_button();
        ?>
    </form>
    <?php
}
#endregion Create the options page