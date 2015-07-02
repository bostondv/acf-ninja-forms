<?php

class acf_field_ninja_forms extends acf_field {
  
  // vars
  var $settings; // will hold info such as dir / path
  var$defaults; // will hold default field options
    
    
  /*
  *  __construct
  *
  *  Set name / label needed for actions / filters
  *
  *  @since 3.6
  *  @date  23/01/13
  */
  
  function __construct()
  {
    // vars
    $this->name = 'ninja_forms_field';
    $this->label = __( 'Ninja Forms', 'acf-ninja-forms' );
    $this->category = __( 'Relational', 'acf' ); // Basic, Content, Choice, etc
    $this->defaults = array(
      'allow_null' => 0,
      'allow_multiple' => 0,
    );
    
    
    // do not delete!
    parent::__construct();
      
      
    // settings
    $this->settings = array(
      'path' => apply_filters('acf/helpers/get_path', __FILE__),
      'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
      'version' => '1.0.0'
    );
  }
  
  
  /*
  *  create_options()
  *
  *  Create extra options for your field. This is rendered when editing a field.
  *  The value of $field['name'] can be used (like below) to save extra data to the $field
  *
  *  @type  action
  *  @since 3.6
  *  @date  23/01/13
  *
  *  @param $field  - an array holding all the field's data
  */
  
  function create_options( $field )
  {
    // defaults?
    /*
    $field = array_merge($this->defaults, $field);
    */
    
    // key is needed in the field names to correctly save the data
    $key = $field['name'];
    
    
    // Create Field Options HTML
    ?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
  <td class="label">
    <label><?php _e('Allow Null?', 'acf'); ?></label>
  </td>
  <td>
    <?php
    
    do_action('acf/create_field', array(
      'type'    =>  'radio',
      'name'    =>  'fields['.$key.'][allow_null]',
      'value'   =>  $field['allow_null'],
      'layout'  =>  'horizontal',
      'choices' =>  array(
        1 =>  __( 'Yes', 'acf' ),
        0 =>  __( 'No', 'acf' ),
      )
    ));
    
    ?>
  </td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
  <td class="label">
    <label><?php _e('Select multiple values?', 'acf'); ?></label>
  </td>
  <td>
    <?php
    
    do_action('acf/create_field', array(
      'type'    =>  'radio',
      'name'    =>  'fields['.$key.'][allow_multiple]',
      'value'   =>  $field['allow_multiple'],
      'layout'  =>  'horizontal',
      'choices' =>  array(
        1 =>  __( 'Yes', 'acf' ),
        0 =>  __( 'No', 'acf' ),
      )
    ));
    
    ?>
  </td>
</tr>
    <?php
    
  }
  
  
  /*
  *  create_field()
  *
  *  Create the HTML interface for your field
  *
  *  @param $field - an array holding all the field's data
  *
  *  @type  action
  *  @since 3.6
  *  @date  23/01/13
  */
  
  function create_field( $field )
  {
    /*
    *  Review the data of $field.
    *  This will show what data is available
    */
    
    // vars
    $field = array_merge($this->defaults, $field);
    $choices = array();
    $forms = ninja_forms_get_all_forms();
    $multiple = ( $field['allow_multiple'] == true ? ' multiple' : '');
    $field_name = ( $field['allow_multiple'] == true ? $field['name'] . '[]' : $field['name'] );

    if ( $forms ) {
      foreach( $forms as $form ) {
        $choices[ $form[ 'id' ] ] = ucfirst( $form[ 'data' ][ 'form_title' ] );
      }
    }

    // Override field settings and render
    $field['choices'] = $choices;
    $field['type'] = 'select';
    ?>

      <select name="<?php echo $field_name; ?>" id="<?php echo $field['name'];?>"<?php echo $multiple; ?>>
        <?php
          if ( $field['allow_null'] == true ) :
            $selected = '';
            if ( is_array( $field['value'] ) ) {
              if ( in_array( '', $field['value'] ) ) {
                $selected = ' selected="selected"';
              }
            } else {
              if ( $field['value'] == '' ) {
                $selected = ' selected="selected"';
              }
            }
            ?>
            <option value="" <?php echo $selected; ?>><?php _e( '- Select -', 'acf' ); ?></option>
          <?php
          endif;
          foreach ( $field['choices'] as $key => $value ) : 
            $selected = '';
            if ( is_array( $field['value'] ) ) {
              if ( in_array( $key, $field['value'] ) ) {
                $selected = ' selected="selected"';
              }
            } else {
              if ( $field['value'] == $key ) {
                $selected = ' selected="selected"';
              }
            }
            ?>
            <option value="<?php echo $key; ?>"<?php echo $selected; ?>>
              <?php echo $value; ?>
            </option>
          <?php endforeach;
        ?>
      </select>
    <?php
  }
  
  /*
  *  format_value()
  *
  *  This filter is applied to the $value after it is loaded from the db and before it is passed to the create_field action
  *
  *  @type  filter
  *  @since 3.6
  *  @date  23/01/13
  *
  *  @param $value  - the value which was loaded from the database
  *  @param $post_id - the $post_id from which the value was loaded
  *  @param $field  - the field array holding all the field options
  *
  *  @return  $value  - the modified value
  */
  
  function format_value( $value, $post_id, $field ) {
    if ( ! $value ) {
      return false;
    }

    if ( $value == 'null' ) {
      return false;
    }

    if ( is_array( $value ) ) {
      foreach( $value as $k => $v ) {
        $form = ninja_forms_get_form_by_id( $v );
        $value[ $k ] = $form;
      }
    } else {
      $value = ninja_forms_get_form_by_id( $value );
    }

    return $value;
  }

}

// create field
new acf_field_FIELD_NAME();
