<?php
/**
 * Quail Forever functions and definitions
 *
 * @package Quail Forever
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'quail_forever_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function quail_forever_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Quail Forever, use a find and replace
	 * to change 'quail-forever' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'quail-forever', get_template_directory() . '/languages' );

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
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	//add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'quail-forever' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'quail_forever_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // quail_forever_setup
add_action( 'after_setup_theme', 'quail_forever_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function quail_forever_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'quail-forever' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'quail_forever_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function quail_forever_scripts() {
	wp_enqueue_style( 'quail-forever-style', get_stylesheet_uri() );

	wp_enqueue_script( 'quail-forever-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'quail-forever-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'quail_forever_scripts' );

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

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

// Make list-field column a dropdown rather than single-line text input
add_filter("gform_column_input_4_1_2", "set_column", 10, 5); // for single-shooter ticket form
function set_column($input_info, $field, $column, $value, $form_id){
    return array("type" => "select", "choices" => "A, B, C, D");
}
