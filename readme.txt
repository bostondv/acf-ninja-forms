=== ACF Ninja Forms ===
Contributors: bostondv
Donate link: http://pomelodesign.com/donate
License: MIT
License URI: http://opensource.org/licenses/MIT
Tags: ninja forms, acf, advanced custom fields, forms
Requires at least: 3.8
Tested up to: 4.2
Stable tag: 1.0.2

Adds an Advanced Custom Fields field to select one or many Ninja Forms.

== Description ==

This is an Advanced Custom Field custom field to select one or many [Ninja Forms](http://www.ninjaforms.com/).

= Using the field =

The field lets you pick one or many fields.

The data returned is either a Form object or an array of Form objects.

If you have selected a single form and you want to display the form on the page, you can use:

`
<?php 
    $form = get_field('your_form_field');
    if ( function_exists( 'ninja_forms_display_form' ) ) {
        ninja_forms_display_form( $form[ 'id' ] );
    }
?>
`

You can find out more about the Ninja Forms methods to embed a form on a page in their [documentation](http://docs.ninjaforms.com/)

If you are using the field to select multiple forms, you will have to iterate over the array.  You can then use the form object as you like:

`
<?php
    $forms = get_field('your_forms');
    foreach( $forms as $form ){
        echo $form[ 'id' ];
    }
?>
`

= About =

Written by Boston Dell-Vandenberg of [Pomelo Design](http://www.pomelodesign.com). Pomelo Design is a web and mobile app development agency based in Toronto, Canada.

= Credits =

Thanks to Adam Pope for the [ACF Gravity Forms](https://github.com/stormuk/Gravity-Forms-ACF-Field) and Lewis Mcarey for the [Users Field ACF](https://github.com/lewismcarey/User-Field-ACF-Add-on) add-ons on which we based this on.

== Installation ==

1. Upload the `acf-ninja-forms` folder to the `/wp-content/plugins/` directory
1. Activate the Advanced Custom Fields: Ninja Forms Field plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 1.0.2 =

* Fixes allow null and allow multiple options
* Initial support for ACF v4

= 1.0.1 =

* Bug fixes

= 1.0.0 =

Release Date: May 29, 2015

* Initial release