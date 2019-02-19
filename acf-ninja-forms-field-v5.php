<?php

class acf_field_ninja_forms extends acf_field {

  /*
  *  __construct
  *
  *  This function will setup the field type data
  *
  *  @type  function
  *  @since 5.0.0
  *  @param n/a
  *  @return  n/a
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
  }

  /*
   *  get_ninja_forms_version()
   *  Check Ninja Forms version
   *
   *  @type  function
   *  @since 1.0.3
   *  @param n/a
   *  @return  $version (int) the activate version of Ninja Forms
   */

   function get_ninja_forms_version()
   {
       return version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ? 2 : 3;
   }

  /*
  *  render_field_settings()
  *
  *  Create extra settings for your field. These are visible when editing a field
  *
  *  @type  action
  *  @since 3.6
  *  @date  23/01/13
  *
  *  @param $field (array) the $field being edited
  *  @return  n/a
  */

  function render_field_settings( $field ) {

    /*
    *  acf_render_field_setting
    *
    *  This function will create a setting for your field. Simply pass the $field parameter and an array of field settings.
    *  The array of settings does not require a `value` or `prefix`; These settings are found from the $field array.
    *
    *  More than one setting can be added by copy/paste the above code.
    *  Please note that you must also have a matching $defaults value for the field name (font_size)
    */

    // allow_null
    acf_render_field_setting( $field, array(
      'label'        => __( 'Allow Null?', 'acf' ),
      'instructions' => '',
      'name'         => 'allow_null',
      'type'         => 'true_false',
      'ui'           => 1,
    ));

    // multiple
    acf_render_field_setting( $field, array(
      'label'        => __( 'Select multiple values?', 'acf' ),
      'instructions' => '',
      'name'         => 'allow_multiple',
      'type'         => 'true_false',
      'ui'           => 1,
    ));

    // // ui
    // acf_render_field_setting( $field, array(
    //   'label'        => __('Stylised UI','acf'),
    //   'instructions' => '',
    //   'name'         => 'ui',
    //   'type'         => 'true_false',
    //   'ui'           => 1,
    // ));

  }

  /*
  *  render_field()
  *
  *  Create the HTML interface for your field
  *
  *  @param $field (array) the $field being rendered
  *
  *  @type  action
  *  @since 3.6
  *  @date  23/01/13
  *
  *  @param $field (array) the $field being edited
  *  @return  n/a
  */

  function render_field( $field ) {


    /*
    *  Review the data of $field.
    *  This will show what data is available
    */

    // vars
    $nf_version = $this->get_ninja_forms_version();
    $field = array_merge($this->defaults, $field);
    $forms = $nf_version === 2 ? ninja_forms_get_all_forms() : Ninja_Forms()->form()->get_forms();
    $field_name = ( $field['allow_multiple'] == true ? $field['name'] . '[]' : $field['name'] );

		// convert
		$value = acf_get_array($field['value']);
		$choices = acf_get_array($field['choices']);

    if ( $forms ) {
      foreach( $forms as $form ) {
        if ($nf_version === 2) {
          $choices[ $form[ 'id' ] ] = ucfirst( $form[ 'data' ][ 'form_title' ] );
        } else {
            $choices[ $form->get_id() ] = ucfirst( $form->get_setting( 'title' ) );
        }
      }
    }

    // placeholder
		if( empty($field['placeholder']) ) {
			$field['placeholder'] = _x('Select', 'verb', 'acf');
    }

    // add empty value (allows '' to be selected)
		if( empty($value) ) {
			$value = array('');
		}

    // Override field type
    $field['type'] = 'select';

    if( $field['allow_null'] && !$field['multiple'] ) {
			$choices = array( '' => "- {$field['placeholder']} -" ) + $choices;
    }

    // vars
    $select = array(
      'id'                => $field['id'],
      'class'             => $field['class'],
      'name'              => $field['name'],
      'data-ui'           => $field['ui'],
      'data-ajax'         => $field['ajax'],
      'data-multiple'     => $field['multiple'] == true ? 1 : 0,
      'data-placeholder'  => $field['placeholder'],
      'data-allow_null'   => $field['allow_null']
    );

    // multiple
    if( $field['multiple'] ) {

      $select['multiple'] = 'multiple';
      $select['size'] = 5;
      $select['name'] .= '[]';
    }


    // special atts
    if( !empty($field['readonly']) ) $select['readonly'] = 'readonly';
    if( !empty($field['disabled']) ) $select['disabled'] = 'disabled';


    // hidden input is needed to allow validation to see <select> element with no selected value
    if( $field['multiple'] || $field['ui'] ) {
      acf_hidden_input(array(
        'id'    => $field['id'] . '-input',
        'name'  => $field['name']
      ));
    }


    // append
    $select['value'] = $value;
    $select['choices'] = $choices;

    // render
    acf_select_input( $select );
  }

  /*
  *  format_value()
  *
  *  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
  *
  *  @type  filter
  *  @since 3.6
  *  @date  23/01/13
  *
  *  @param $value (mixed) the value which was loaded from the database
  *  @param $post_id (mixed) the $post_id from which the value was loaded
  *  @param $field (array) the field array holding all the field options
  *
  *  @return  $value (mixed) the modified value
  */

  function format_value( $value, $post_id, $field ) {
    $nf_version = $this->get_ninja_forms_version();

    if ( ! $value ) {
      return false;
    }

    if ( $value == 'null' ) {
      return false;
    }

    if ( is_array( $value ) ) {
      foreach( $value as $k => $v ) {
        if ($nf_version === 2) {
          $form = ninja_forms_get_form_by_id( $v );
        } else {
          $form_object = Ninja_Forms()->form( $v )->get();
          $form = array( 'id' => $v, 'data' => $form_object->get_settings(), 'date_updated' => $form_object->get_setting( 'date_updated' ) );
        }

        $value[ $k ] = $form;
      }
    } else {
      if ($nf_version === 2) {
        $value = ninja_forms_get_form_by_id( $value );
      } else {
        $form_object = Ninja_Forms()->form( $value )->get();
        $value = array( 'id' => $value, 'data' => $form_object->get_settings(), 'date_updated' => $form_object->get_setting( 'date_updated' ) );
      }
    }

    return $value;
  }
}

new acf_field_ninja_forms();
