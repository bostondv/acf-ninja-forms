<?php
/*
Plugin Name: Advanced Custom Fields: Ninja Forms Field
Plugin URI: https://github.com/bostondv/acf-ninja-forms
Description: Adds an Advanced Custom Fields field to select one or many Ninja Forms.
Version: 1.0.0
Author: bostondv
Author URI: http://www.pomelodesign.com
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html
*/

function include_field_types_ninja_forms( $version ) {
  include_once( 'ninja-forms-field.php' );
}

add_action( 'acf/include_field_types', 'include_field_types_ninja_forms' ); 
