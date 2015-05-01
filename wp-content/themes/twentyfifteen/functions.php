<?php
/**
 * Twenty Fifteen functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since Twenty Fifteen 1.0
 */
if ( ! isset( $content_width ) ) {
	$content_width = 660;
}

/**
 * Twenty Fifteen only works in WordPress 4.1 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '4.1-alpha', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
}

if ( ! function_exists( 'twentyfifteen_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on twentyfifteen, use a find and replace
	 * to change 'twentyfifteen' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'twentyfifteen', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 825, 510, true );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu',      'twentyfifteen' ),
		'social'  => __( 'Social Links Menu', 'twentyfifteen' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
	) );

	$color_scheme  = twentyfifteen_get_color_scheme();
	$default_color = trim( $color_scheme[0], '#' );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'twentyfifteen_custom_background_args', array(
		'default-color'      => $default_color,
		'default-attachment' => 'fixed',
	) ) );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	add_editor_style( array( 'css/editor-style.css', 'genericons/genericons.css', twentyfifteen_fonts_url() ) );
}
endif; // twentyfifteen_setup
add_action( 'after_setup_theme', 'twentyfifteen_setup' );

/**
 * Register widget area.
 *
 * @since Twenty Fifteen 1.0
 *
 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
 */
function twentyfifteen_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Widget Area', 'twentyfifteen' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar.', 'twentyfifteen' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'twentyfifteen_widgets_init' );

if ( ! function_exists( 'twentyfifteen_fonts_url' ) ) :
/**
 * Register Google fonts for Twenty Fifteen.
 *
 * @since Twenty Fifteen 1.0
 *
 * @return string Google fonts URL for the theme.
 */
function twentyfifteen_fonts_url() {
	$fonts_url = '';
	$fonts     = array();
	$subsets   = 'latin,latin-ext';

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Noto Sans, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Noto Sans font: on or off', 'twentyfifteen' ) ) {
		$fonts[] = 'Noto Sans:400italic,700italic,400,700';
	}

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Noto Serif, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Noto Serif font: on or off', 'twentyfifteen' ) ) {
		$fonts[] = 'Noto Serif:400italic,700italic,400,700';
	}

	/*
	 * Translators: If there are characters in your language that are not supported
	 * by Inconsolata, translate this to 'off'. Do not translate into your own language.
	 */
	if ( 'off' !== _x( 'on', 'Inconsolata font: on or off', 'twentyfifteen' ) ) {
		$fonts[] = 'Inconsolata:400,700';
	}

	/*
	 * Translators: To add an additional character subset specific to your language,
	 * translate this to 'greek', 'cyrillic', 'devanagari' or 'vietnamese'. Do not translate into your own language.
	 */
	$subset = _x( 'no-subset', 'Add new subset (greek, cyrillic, devanagari, vietnamese)', 'twentyfifteen' );

	if ( 'cyrillic' == $subset ) {
		$subsets .= ',cyrillic,cyrillic-ext';
	} elseif ( 'greek' == $subset ) {
		$subsets .= ',greek,greek-ext';
	} elseif ( 'devanagari' == $subset ) {
		$subsets .= ',devanagari';
	} elseif ( 'vietnamese' == $subset ) {
		$subsets .= ',vietnamese';
	}

	if ( $fonts ) {
		$fonts_url = add_query_arg( array(
			'family' => urlencode( implode( '|', $fonts ) ),
			'subset' => urlencode( $subsets ),
		), '//fonts.googleapis.com/css' );
	}

	return $fonts_url;
}
endif;

/**
 * JavaScript Detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since Twenty Fifteen 1.1
 */
function twentyfifteen_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'twentyfifteen_javascript_detection', 0 );

/**
 * Enqueue scripts and styles.
 *
 * @since Twenty Fifteen 1.0
 */
function twentyfifteen_scripts() {
	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'twentyfifteen-fonts', twentyfifteen_fonts_url(), array(), null );

	// Add Genericons, used in the main stylesheet.
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/genericons/genericons.css', array(), '3.2' );

	// Load our main stylesheet.
	wp_enqueue_style( 'twentyfifteen-style', get_stylesheet_uri() );

	// Load the Internet Explorer specific stylesheet.
	wp_enqueue_style( 'twentyfifteen-ie', get_template_directory_uri() . '/css/ie.css', array( 'twentyfifteen-style' ), '20141010' );
	wp_style_add_data( 'twentyfifteen-ie', 'conditional', 'lt IE 9' );

	// Load the Internet Explorer 7 specific stylesheet.
	wp_enqueue_style( 'twentyfifteen-ie7', get_template_directory_uri() . '/css/ie7.css', array( 'twentyfifteen-style' ), '20141010' );
	wp_style_add_data( 'twentyfifteen-ie7', 'conditional', 'lt IE 8' );

	wp_enqueue_script( 'twentyfifteen-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20141010', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'twentyfifteen-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20141010' );
	}

	wp_enqueue_script( 'twentyfifteen-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20150330', true );
	wp_localize_script( 'twentyfifteen-script', 'screenReaderText', array(
		'expand'   => '<span class="screen-reader-text">' . __( 'expand child menu', 'twentyfifteen' ) . '</span>',
		'collapse' => '<span class="screen-reader-text">' . __( 'collapse child menu', 'twentyfifteen' ) . '</span>',
	) );
}
add_action( 'wp_enqueue_scripts', 'twentyfifteen_scripts' );

/**
 * Add featured image as background image to post navigation elements.
 *
 * @since Twenty Fifteen 1.0
 *
 * @see wp_add_inline_style()
 */
function twentyfifteen_post_nav_background() {
	if ( ! is_single() ) {
		return;
	}

	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );
	$css      = '';

	if ( is_attachment() && 'attachment' == $previous->post_type ) {
		return;
	}

	if ( $previous &&  has_post_thumbnail( $previous->ID ) ) {
		$prevthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $previous->ID ), 'post-thumbnail' );
		$css .= '
			.post-navigation .nav-previous { background-image: url(' . esc_url( $prevthumb[0] ) . '); }
			.post-navigation .nav-previous .post-title, .post-navigation .nav-previous a:hover .post-title, .post-navigation .nav-previous .meta-nav { color: #fff; }
			.post-navigation .nav-previous a:before { background-color: rgba(0, 0, 0, 0.4); }
		';
	}

	if ( $next && has_post_thumbnail( $next->ID ) ) {
		$nextthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $next->ID ), 'post-thumbnail' );
		$css .= '
			.post-navigation .nav-next { background-image: url(' . esc_url( $nextthumb[0] ) . '); border-top: 0; }
			.post-navigation .nav-next .post-title, .post-navigation .nav-next a:hover .post-title, .post-navigation .nav-next .meta-nav { color: #fff; }
			.post-navigation .nav-next a:before { background-color: rgba(0, 0, 0, 0.4); }
		';
	}

	wp_add_inline_style( 'twentyfifteen-style', $css );
}
add_action( 'wp_enqueue_scripts', 'twentyfifteen_post_nav_background' );

/**
 * Display descriptions in main navigation.
 *
 * @since Twenty Fifteen 1.0
 *
 * @param string  $item_output The menu item output.
 * @param WP_Post $item        Menu item object.
 * @param int     $depth       Depth of the menu.
 * @param array   $args        wp_nav_menu() arguments.
 * @return string Menu item with possible description.
 */
function twentyfifteen_nav_description( $item_output, $item, $depth, $args ) {
	if ( 'primary' == $args->theme_location && $item->description ) {
		$item_output = str_replace( $args->link_after . '</a>', '<div class="menu-item-description">' . $item->description . '</div>' . $args->link_after . '</a>', $item_output );
	}

	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'twentyfifteen_nav_description', 10, 4 );

/**
 * Add a `screen-reader-text` class to the search form's submit button.
 *
 * @since Twenty Fifteen 1.0
 *
 * @param string $html Search form HTML.
 * @return string Modified search form HTML.
 */
function twentyfifteen_search_form_modify( $html ) {
	return str_replace( 'class="search-submit"', 'class="search-submit screen-reader-text"', $html );
}
add_filter( 'get_search_form', 'twentyfifteen_search_form_modify' );

/**
 * Implement the Custom Header feature.
 *
 * @since Twenty Fifteen 1.0
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 *
 * @since Twenty Fifteen 1.0
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 *
 * @since Twenty Fifteen 1.0
 */
require get_template_directory() . '/inc/customizer.php';

/**
* Set Number of List Field Rows by Field Value
* http://gravitywiz.com/2012/06/03/set-number-of-list-field-rows-by-field-value/
*/
class GWAutoListFieldRows {
    
    private static $_is_script_output;
    
    function __construct( $args ) {
        
        $this->_args = wp_parse_args( $args, array( 
            'form_id'       => false,
            'input_html_id' => false,
            'list_field_id' => false
        ) );
        
        extract( $this->_args ); // gives us $form_id, $input_html_id, and $list_field_id
        
        if( ! $form_id || ! $input_html_id || ! $list_field_id )
            return;
        
        add_filter( 'gform_pre_render_' . $form_id, array( $this, 'pre_render' ) );
        
    }
    
    function pre_render( $form ) {
        ?>
        
        <style type="text/css"> #field_<?php echo $form['id']; ?>_<?php echo $this->_args['list_field_id']; ?> .gfield_list_icons { display: none; } </style>
        
        <?php
        
        add_filter( 'gform_register_init_scripts', array( $this, 'register_init_script' ) );
        
        if( ! self::$_is_script_output )
            $this->output_script();
        
        return $form;
    }
    
    function register_init_script( $form ) {
        
        // remove this function from the filter otherwise it will be called for every other form on the page
        remove_filter( 'gform_register_init_scripts', array( $this, 'register_init_script' ) );
                
        $args = array(
            'formId'      => $this->_args['form_id'],
            'listFieldId' => $this->_args['list_field_id'],
            'inputHtmlId' => $this->_args['input_html_id']
            );
        
        $script = "new gwalfr(" . json_encode( $args ) . ");";
        $key = implode( '_', $args );
        
        GFFormDisplay::add_init_script( $form['id'], 'gwalfr_' . $key , GFFormDisplay::ON_PAGE_RENDER, $script );
        
    }
    
    function output_script() {
        ?>
        
        <script type="text/javascript">
            
        window.gwalfr;
        
        (function($){
        
            gwalfr = function( args ) {
                
                this.formId      = args.formId, 
                this.listFieldId = args.listFieldId, 
                this.inputHtmlId = args.inputHtmlId;
                
                this.init = function() {
                    
                    var gwalfr = this,
                        triggerInput = $( this.inputHtmlId );
                    
                    // update rows on page load
                    this.updateListItems( triggerInput, this.listFieldId, this.formId );
                    
                    // update rows when field value changes
                    triggerInput.change(function(){
                        gwalfr.updateListItems( $(this), gwalfr.listFieldId, gwalfr.formId );
                    });
                    
                }
                
                this.updateListItems = function( elem, listFieldId, formId ) {
                    
                    var listField = $( '#field_' + formId + '_' + listFieldId ),
                        count = parseInt( elem.val() );
                        rowCount = listField.find( 'table.gfield_list tbody tr' ).length,
                        diff = count - rowCount;
                    
                    if( diff > 0 ) {
                        for( var i = 0; i < diff; i++ ) {
                            listField.find( '.add_list_item:last' ).click();
                        }    
                    } else {
                        
                        // make sure we never delete all rows
                        if( rowCount + diff == 0 )
                            diff++;
                            
                        for( var i = diff; i < 0; i++ ) {
                            listField.find( '.delete_list_item:last' ).click();
                        }
                        
                    }
                }
                
                this.init();
                
            }
            
        })(jQuery);
        
        </script>
        
        <?php
    }
    
}

/**
* Require All Columns of List Field
* http://gravitywiz.com/require-all-columns-of-list-field/
*/
class GWRequireListColumns {

    private $field_ids;
    
    public static $fields_with_req_cols = array();

    function __construct($form_id = '', $field_ids = array(), $required_cols = array()) {

        $this->field_ids = ! is_array( $field_ids ) ? array( $field_ids ) : $field_ids;
        $this->required_cols = ! is_array( $required_cols ) ? array( $required_cols ) : $required_cols;
        
        if( ! empty( $this->required_cols ) ) {
            
            // convert values from 1-based index to 0-based index, allows users to enter "1" for column "0"
            $this->required_cols = array_map( create_function( '$val', 'return $val - 1;' ), $this->required_cols );
            
            if( ! isset( self::$fields_with_req_cols[$form_id] ) )
                self::$fields_with_req_cols[$form_id] = array();
            
            // keep track of which forms/fields have special require columns so we can still apply GWRequireListColumns 
            // to all list fields and then override with require columns for specific fields as well
            self::$fields_with_req_cols[$form_id] = array_merge( self::$fields_with_req_cols[$form_id], $this->field_ids );
            
        }
        
        $form_filter = $form_id ? "_{$form_id}" : $form_id;
        add_filter("gform_validation{$form_filter}", array(&$this, 'require_list_columns'));

    }

    function require_list_columns($validation_result) {

        $form = $validation_result['form'];
        $new_validation_error = false;

        foreach($form['fields'] as &$field) {

            if(!$this->is_applicable_field($field, $form))
                continue;

            $values = rgpost("input_{$field['id']}");

            // If we got specific fields, loop through those only
            if( count( $this->required_cols ) ) {

                foreach($this->required_cols as $required_col) {
                    if(empty($values[$required_col])) {
                        $new_validation_error = true;
                        $field['failed_validation'] = true;
                        $field['validation_message'] = $field['errorMessage'] ? $field['errorMessage'] : 'All inputs must be filled out.';
                    }
                }

            } else {
                
                // skip fields that have req cols specified by another GWRequireListColumns instance
                $fields_with_req_cols = rgar( self::$fields_with_req_cols, $form['id'] );
                if( is_array( $fields_with_req_cols ) && in_array( $field['id'], $fields_with_req_cols ) )
                    continue;
                
                foreach($values as $value) {
                    if(empty($value)) {
                        $new_validation_error = true;
                        $field['failed_validation'] = true;
                        $field['validation_message'] = $field['errorMessage'] ? $field['errorMessage'] : 'All inputs must be filled out.';
                    }
                }

            }
        }

        $validation_result['form'] = $form;
        $validation_result['is_valid'] = $new_validation_error ? false : $validation_result['is_valid'];

        return $validation_result;
    }

    function is_applicable_field($field, $form) {

        if($field['pageNumber'] != GFFormDisplay::get_source_page($form['id']))
            return false;
    
        if( GFFormsModel::get_input_type( $field ) != 'list' || RGFormsModel::is_field_hidden($form, $field, array()))
            return false;
    
        // if the field has already failed validation, we don't need to fail it again
        if(!$field['isRequired'] || $field['failed_validation'])
            return false;
    
        if(empty($this->field_ids))
            return true;
    
        return in_array($field['id'], $this->field_ids);
    }

}

// accepted parameters
// new GWRequireListColumns($form_id, $field_ids);
 
// apply to all list fields on all forms
//new GWRequireListColumns();
 
// apply to all list fields on a specific form
//new GWRequireListColumns(4);
 
// apply to specific list field on a specific form
# new GWRequireListColumns(4, 2);
 
// apply to specific list fields (plural) on a specific form
# new GWRequireListColumns(4, array(2,3));

// require specific field columns on a specific form
//new GWRequireListColumns( 240, 1, array( 2, 3 ) );

new GWAutoListFieldRows( array( 
    'form_id' => 4,
    'list_field_id' => 1,
    'input_html_id' => '#ginput_quantity_4_2'
) );

// Require all columns on Single-Shooter Tickets
new GWRequireListColumns(4);

// Require all columns on Team Tickets
new GWRequireListColumns(5);
