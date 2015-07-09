<?php

use YCMS\Extensions\Func;

/*
 * Loads the Options Panel
 *
 * If you're loading from a child theme use stylesheet_directory
 * instead of template_directory
 */

define('OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/inc/');
//require_once dirname(__FILE__) . '/inc/options-framework.php';


// Loads options.php from child or parent theme
//$optionsfile = locate_template( 'options.php' );
//load_template( $optionsfile );

/*
 * This is an example of how to add custom scripts to the options panel.
 * This one shows/hides the an option when a checkbox is clicked.
 *
 * You can delete it if you not using that option
 */
add_action('optionsframework_custom_scripts', function () { ?>

    <script type="text/javascript">
        jQuery(document).ready(function () {

            jQuery('#example_showhidden').click(function () {
                jQuery('#section-example_text_hidden').fadeToggle(400);
            });

            if (jQuery('#example_showhidden:checked').val() !== undefined) {
                jQuery('#section-example_text_hidden').show();
            }

        });
    </script>

<?php });
/*
 * This is an example of filtering menu parameters
 */

/*
function prefix_options_menu_filter( $menu ) {
	$menu['mode'] = 'menu';
	$menu['page_title'] = __( 'Hello Options', 'textdomain');
	$menu['menu_title'] = __( 'Hello Options', 'textdomain');
	$menu['menu_slug'] = 'hello-options';
	return $menu;
}

add_filter( 'optionsframework_menu', 'prefix_options_menu_filter' );
*/

/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 */

Func::make('optionsframework_option_name', function () {
    function optionsframework_option_name()
    {
        // Change this to use your theme slug
        return 'default';
    }
});


/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 * If you are making your theme translatable, you should replace 'default'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */
Func::make('optionsframework_options', function () {
    function optionsframework_options()
    {


        // Test data
        $test_array = array(
            'one'   => __('One', 'default'),
            'two'   => __('Two', 'default'),
            'three' => __('Three', 'default'),
            'four'  => __('Four', 'default'),
            'five'  => __('Five', 'default')
        );

        // Multicheck Array
        $multicheck_array = array(
            'one'   => __('French Toast', 'default'),
            'two'   => __('Pancake', 'default'),
            'three' => __('Omelette', 'default'),
            'four'  => __('Crepe', 'default'),
            'five'  => __('Waffle', 'default')
        );

        // Multicheck Defaults
        $multicheck_defaults = array(
            'one'  => '1',
            'five' => '1'
        );

        // Background Defaults
        $background_defaults = array(
            'color'      => '',
            'image'      => '',
            'repeat'     => 'repeat',
            'position'   => 'top center',
            'attachment' => 'scroll'
        );

        // Typography Defaults
        $typography_defaults = array(
            'size'  => '15px',
            'face'  => 'georgia',
            'style' => 'bold',
            'color' => '#bada55'
        );

        // Typography Options
        $typography_options = array(
            'sizes'  => array('6', '12', '14', '16', '20'),
            'faces'  => array('Helvetica Neue' => 'Helvetica Neue', 'Arial' => 'Arial'),
            'styles' => array('normal' => 'Normal', 'bold' => 'Bold'),
            'color'  => false
        );

        // Pull all the categories into an array
        $options_categories = array();
        $options_categories_obj = get_categories();
        foreach ($options_categories_obj as $category) {
            $options_categories[ $category->cat_ID ] = $category->cat_name;
        }

        // Pull all tags into an array
        $options_tags = array();
        $options_tags_obj = get_tags();
        foreach ($options_tags_obj as $tag) {
            $options_tags[ $tag->term_id ] = $tag->name;
        }

        // Pull all the pages into an array
        $options_pages = array();
        $options_pages_obj = get_pages('sort_column=post_parent,menu_order');
        $options_pages[''] = 'Select a page:';
        foreach ($options_pages_obj as $page) {
            $options_pages[ $page->ID ] = $page->post_title;
        }

        // If using image radio buttons, define a directory path
        $imagepath = get_template_directory_uri() . '/images/';

        $options = array();

        $options[] = array(
            'name' => __('Basic Settings', 'default'),
            'type' => 'heading'
        );

        $options[] = array(
            'name'  => __('Input Text Mini', 'default'),
            'desc'  => __('A mini text input field.', 'default'),
            'id'    => 'example_text_mini',
            'std'   => 'Default',
            'class' => 'mini',
            'type'  => 'text'
        );

        $options[] = array(
            'name' => __('Input Text', 'default'),
            'desc' => __('A text input field.', 'default'),
            'id'   => 'example_text',
            'std'  => 'Default Value',
            'type' => 'text'
        );

        $options[] = array(
            'name'        => __('Input with Placeholder', 'default'),
            'desc'        => __('A text input field with an HTML5 placeholder.', 'default'),
            'id'          => 'example_placeholder',
            'placeholder' => 'Placeholder',
            'type'        => 'text'
        );

        $options[] = array(
            'name' => __('Textarea', 'default'),
            'desc' => __('Textarea description.', 'default'),
            'id'   => 'example_textarea',
            'std'  => 'Default Text',
            'type' => 'textarea'
        );

        $options[] = array(
            'name'    => __('Input Select Small', 'default'),
            'desc'    => __('Small Select Box.', 'default'),
            'id'      => 'example_select',
            'std'     => 'three',
            'type'    => 'select',
            'class'   => 'mini', //mini, tiny, small
            'options' => $test_array
        );

        $options[] = array(
            'name'    => __('Input Select Wide', 'default'),
            'desc'    => __('A wider select box.', 'default'),
            'id'      => 'example_select_wide',
            'std'     => 'two',
            'type'    => 'select',
            'options' => $test_array
        );

        if ($options_categories) {
            $options[] = array(
                'name'    => __('Select a Category', 'default'),
                'desc'    => __('Passed an array of categories with cat_ID and cat_name', 'default'),
                'id'      => 'example_select_categories',
                'type'    => 'select',
                'options' => $options_categories
            );
        }

        if ($options_tags) {
            $options[] = array(
                'name'    => __('Select a Tag', 'options_check'),
                'desc'    => __('Passed an array of tags with term_id and term_name', 'options_check'),
                'id'      => 'example_select_tags',
                'type'    => 'select',
                'options' => $options_tags
            );
        }

        $options[] = array(
            'name'    => __('Select a Page', 'default'),
            'desc'    => __('Passed an pages with ID and post_title', 'default'),
            'id'      => 'example_select_pages',
            'type'    => 'select',
            'options' => $options_pages
        );

        $options[] = array(
            'name'    => __('Input Radio (one)', 'default'),
            'desc'    => __('Radio select with default options "one".', 'default'),
            'id'      => 'example_radio',
            'std'     => 'one',
            'type'    => 'radio',
            'options' => $test_array
        );

        $options[] = array(
            'name' => __('Example Info', 'default'),
            'desc' => __('This is just some example information you can put in the panel.', 'default'),
            'type' => 'info'
        );

        $options[] = array(
            'name' => __('Input Checkbox', 'default'),
            'desc' => __('Example checkbox, defaults to true.', 'default'),
            'id'   => 'example_checkbox',
            'std'  => '1',
            'type' => 'checkbox'
        );

        $options[] = array(
            'name' => __('Advanced Settings', 'default'),
            'type' => 'heading'
        );

        $options[] = array(
            'name' => __('Check to Show a Hidden Text Input', 'default'),
            'desc' => __('Click here and see what happens.', 'default'),
            'id'   => 'example_showhidden',
            'type' => 'checkbox'
        );

        $options[] = array(
            'name'  => __('Hidden Text Input', 'default'),
            'desc'  => __('This option is hidden unless activated by a checkbox click.', 'default'),
            'id'    => 'example_text_hidden',
            'std'   => 'Hello',
            'class' => 'hidden',
            'type'  => 'text'
        );

        $options[] = array(
            'name' => __('Uploader Test', 'default'),
            'desc' => __('This creates a full size uploader that previews the image.', 'default'),
            'id'   => 'example_uploader',
            'type' => 'upload'
        );

        $options[] = array(
            'name'    => "Example Image Selector",
            'desc'    => "Images for layout.",
            'id'      => "example_images",
            'std'     => "2c-l-fixed",
            'type'    => "images",
            'options' => array(
                '1col-fixed' => $imagepath . '1col.png',
                '2c-l-fixed' => $imagepath . '2cl.png',
                '2c-r-fixed' => $imagepath . '2cr.png'
            )
        );

        $options[] = array(
            'name' => __('Example Background', 'default'),
            'desc' => __('Change the background CSS.', 'default'),
            'id'   => 'example_background',
            'std'  => $background_defaults,
            'type' => 'background'
        );

        $options[] = array(
            'name'    => __('Multicheck', 'default'),
            'desc'    => __('Multicheck description.', 'default'),
            'id'      => 'example_multicheck',
            'std'     => $multicheck_defaults, // These items get checked by default
            'type'    => 'multicheck',
            'options' => $multicheck_array
        );

        $options[] = array(
            'name' => __('Colorpicker', 'default'),
            'desc' => __('No color selected by default.', 'default'),
            'id'   => 'example_colorpicker',
            'std'  => '',
            'type' => 'color'
        );

        $options[] = array(
            'name' => __('Typography', 'default'),
            'desc' => __('Example typography.', 'default'),
            'id'   => "example_typography",
            'std'  => $typography_defaults,
            'type' => 'typography'
        );

        $options[] = array(
            'name'    => __('Custom Typography', 'default'),
            'desc'    => __('Custom typography options.', 'default'),
            'id'      => "custom_typography",
            'std'     => $typography_defaults,
            'type'    => 'typography',
            'options' => $typography_options
        );

        $options[] = array(
            'name' => __('Text Editor', 'default'),
            'type' => 'heading'
        );

        /**
         * For $settings options see:
         * http://codex.wordpress.org/Function_Reference/wp_editor
         * 'media_buttons' are not supported as there is no post to attach items to
         * 'textarea_name' is set by the 'id' you choose
         */

        $wp_editor_settings = array(
            'wpautop'       => true, // Default
            'textarea_rows' => 5,
            'tinymce'       => array('plugins' => 'wordpress')
        );

        $options[] = array(
            'name'     => __('Default Text Editor', 'default'),
            'desc'     => sprintf(__('You can also pass settings to the editor.  Read more about wp_editor in <a href="%1$s" target="_blank">the WordPress codex</a>',
                'default'), 'http://codex.wordpress.org/Function_Reference/wp_editor'),
            'id'       => 'example_editor',
            'type'     => 'editor',
            'settings' => $wp_editor_settings
        );

        return $options;
    }
});

