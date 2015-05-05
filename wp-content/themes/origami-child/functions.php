<?php
/* Enqueues child theme stylesheet, loading first the parent theme stylesheet.
*/
function themify_custom_enqueue_child_theme_styles() {
    wp_enqueue_style( 'parent-theme-css', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'themify_custom_enqueue_child_theme_styles' );

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
add_filter("gform_column_input_4_1_2", "set_grade_column", 10, 5); // for single-shooter ticket form
function set_grade_column($input_info, $field, $column, $value, $form_id){
    return array("type" => "select", "choices" => "A, B, C, D");
}
// Make list-field column a dropdown rather than single-line text input
add_filter("gform_column_input_4_1_4", "set_member_column", 10, 5); // for single-shooter ticket form
function set_member_column($input_info, $field, $column, $value, $form_id){
    return array("type" => "select", "choices" => "No, Yes");
}
