=== Easy Digital Downloads Featured Downloads ===
Contributors: sumobi, easydigitaldownloads
Tags: easy digital downloads, digital downloads, e-downloads, edd, featured downloads, featured
Requires at least: 3.9
Tested up to: 4.8
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily feature your downloads

== Description ==

This plugin requires [Easy Digital Downloads](http://wordpress.org/extend/plugins/easy-digital-downloads/ "Easy Digital Downloads"). It's aimed at developers/clients who need to show a list of featured downloads.

1. Provides a template tag so finely-tuned placement of featured downloads in your theme is possible.
1. Provides a shortcode which will simply list all the featured downloads, without the need to enter any IDs.
1. Provides a simple interface for managing featured downloads in the WordPress admin. A "feature download" checkbox will be added to each download edit/publish screen as well as the quick edit boxes. At a glance you'll also be able to see which downloads have been featured on your website from the main download listing.

In true EDD fashion, hooks are provided for developers to fine tune the HTML if needed.

= Shortcode Usage =

The following shortcode is available to display your featured images. Most of the shortcode attributes from the main [downloads] shortcode are available to use

    [edd_featured_downloads]

= Template Tag Usage =

The following template tag is available for showing the featured downloads anywhere in your theme.

    if( function_exists( 'edd_fd_show_featured_downloads') ) {
	    edd_fd_show_featured_downloads();
    }

The template tag uses the exact same HTML as the shortcode so can be modified accordingly by overriding the EDD templates.

= Building your own Query =

To build your own query using [WP_Query](https://codex.wordpress.org/Class_Reference/WP_Query "WP_Query") you can use the `meta_key` parameter with a value of `edd_feature_download`. The following example builds a simple unordered list with all the featured downloads.

    <?php

    $args = array(
	    'post_type' => 'download',
	    'meta_key' => 'edd_feature_download',
    );

    $featured_downloads = new WP_Query( $args );

    if( $featured_downloads->have_posts() ) : ?>

	    <ul>
		    <?php while( $featured_downloads->have_posts() ) : $featured_downloads->the_post(); ?>
		    <li>
		       <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
		            <?php the_title(); ?>
		       </a>
		    </li>
		    <?php endwhile; ?>
	    </ul>

    <?php endif; wp_reset_postdata(); ?>

== Installation ==

1. Upload entire `edd-featured-downloads` to the `/wp-content/plugins/` directory, or just upload the ZIP package via 'Plugins > Add New > Upload' in your WP Admin
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Feature your downloads and then use either the included template tag or shortcode to show the featured downloads anywhere on your website.

== Screenshots ==

1. Feature a download quickly from the publish/edit screen.
2. Feature a download quickly from the quick edit menu.
3. See which downloads have been featured at a glance.

== Changelog ==

= 1.0.2 =

* Fix: Fatal error on some PHP versions

= 1.0.1 =
* Tweak: Updated plugin information

= 1.0 =
* Initial release
