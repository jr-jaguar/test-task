<?php
/**
 * dart-theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package dart-theme
 */

if (!function_exists('dart_theme_setup')) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function dart_theme_setup()
    {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on dart-theme, use a find and replace
         * to change 'dart-theme' to the name of your theme in all the template files.
         */
        load_theme_textdomain('dart-theme', get_template_directory() . '/languages');

        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus(array(
            'menu-1' => esc_html__('Primary', 'dart-theme'),
        ));

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));

        // Set up the WordPress core custom background feature.
        add_theme_support('custom-background', apply_filters('dart_theme_custom_background_args', array(
            'default-color' => 'ffffff',
            'default-image' => '',
        )));

        // Add theme support for selective refresh for widgets.
        add_theme_support('customize-selective-refresh-widgets');
    }
endif;
add_action('after_setup_theme', 'dart_theme_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function dart_theme_content_width()
{
    $GLOBALS['content_width'] = apply_filters('dart_theme_content_width', 640);
}

add_action('after_setup_theme', 'dart_theme_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function dart_theme_widgets_init()
{
    register_sidebar(array(
        'name' => esc_html__('Sidebar', 'dart-theme'),
        'id' => 'sidebar-1',
        'description' => esc_html__('Add widgets here.', 'dart-theme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}

add_action('widgets_init', 'dart_theme_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function dart_theme_scripts()
{
    wp_enqueue_style('dart-theme-style', get_stylesheet_uri());

    wp_enqueue_script('dart-theme-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true);

    wp_enqueue_script('dart-theme-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}

add_action('wp_enqueue_scripts', 'dart_theme_scripts');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';


/************************For test task*****************************/

/**
 * Add custom settings for theme
 */

/**
 * Add font's color setting
 */


add_action('customize_register', 'dt_custom_option');

function dt_custom_option($wp_customize)
{

    $wp_customize->add_section('dt_font_color_section', array(
        'title' => __('Font Color', 'dark-theme'),
        'priority' => 20,
        'description' => __('Choose color for fonts', 'dark-theme'),
    ));

    $wp_customize->add_setting(
        'dt_font_color',
        array(
            'default' => '#000',
            'transport' => 'postMessage'
        )
    );


    $wp_customize->add_control(new WP_Customize_Color_Control(
        $wp_customize,
        'dt_font_color',
        array(
            'label' => __('Font Color', 'dark-theme'),
            'section' => 'dt_font_color_section',
            'settings' => 'dt_font_color',
            'priority' => 1
        )
    ));


}

/**
 * Add custom Logo
 */

add_theme_support('custom-logo');

/**
 * Stars
 */


add_action('add_meta_boxes', 'dt_stars_field', 1);

function dt_stars_field()
{
    add_meta_box('extra_fields', 'Rating Stars', 'dt_stars_fields_box_func', 'post', 'normal', 'high');
}

function dt_stars_fields_box_func($post)
{
    ?>


    <p><select name="stars">
            <?php $stars = (int)get_post_meta($post->ID, 'stars', 1);
            for ($i = 0; $i <= 5; $i++) {
                echo '<option value="' . $i . '"' . selected($stars, $i) . ">$i</option>";
            }
            ?></select></p>

    <input type="hidden" name="dt_stars_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>"/>
    <?php
}


add_action('save_post', 'dt_stars_fields_update', 0);


function dt_stars_fields_update($post_id)
{
    if (!wp_verify_nonce($_POST['dt_stars_fields_nonce'], __FILE__)) return false;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return false;
    if (!current_user_can('edit_post', $post_id)) return false;

    if (!isset($_POST['stars'])) return false;
    
    update_post_meta($post_id, 'stars', $_POST['stars']);
    return $post_id;
}

/* Load posts with AJAX */

function dt_loadmore_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script( 'dt_loadmore', get_stylesheet_directory_uri() . '/js/loadmore.js', array('jquery') );
}

add_action( 'wp_enqueue_scripts', 'dt_loadmore_scripts' );

function dt_load_posts(){
    $args = unserialize(stripslashes($_POST['query']));
    $args['paged'] = $_POST['page'] + 1;
    $args['post_status'] = 'publish';
    $q = new WP_Query($args);
    if( $q->have_posts() ):
        while($q->have_posts()): $q->the_post();
            get_template_part( 'template-parts/content', get_post_format() );
        endwhile;
    endif;
    wp_reset_postdata();
    die();
}


add_action('wp_ajax_loadmore', 'dt_load_posts');
add_action('wp_ajax_nopriv_loadmore', 'dt_load_posts');