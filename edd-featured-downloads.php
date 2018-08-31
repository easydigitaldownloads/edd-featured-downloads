<?php
/*
Plugin Name: Easy Digital Downloads - Featured Downloads
Plugin URI: https://easydigitaldownloads.com/downloads/edd-featured-downloads/
Description: Provides a intuitive interface and functionality for managing featured downloads
Version: 1.0.4
Author: Easy Digital Downloads
Author URI: https://easydigitaldownloads.com
License: GPL-2.0+
License URI: http://www.opensource.org/licenses/gpl-license.php
Text Domain: edd-featured-downloads
*/

/**
 * Add metabox to individual download edit screens
 * @since 1.0
*/
function edd_fd_add_featured_meta_box() {
	add_meta_box( 'edd_featured_download', sprintf( __( 'Feature %1$s', 'edd-featured-downloads' ), edd_get_label_singular(), edd_get_label_plural() ), 'edd_fd_render_featured_download_meta_box', 'download', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'edd_fd_add_featured_meta_box' );

/**
 * Render Metabox
 * @since 1.0
*/
function edd_fd_render_featured_download_meta_box() {
	global $post;
	$current = get_post_meta( $post->ID, 'edd_feature_download', true );
?>
	<p><label for="edd_feature_download">
		<input type="checkbox" name="edd_feature_download" id="edd_feature_download"  value="1" <?php checked( 1, $current ); ?>/> <?php _e( 'Feature this download', 'edd-featured-downloads' ); ?>
	</label></p>
<?php }

/**
 * Hook into save filter and make sure it gets saved
 * @since 1.0
*/
function edd_fd_edd_metabox_fields_save( $fields ) {
	$fields[] = 'edd_feature_download';
	return $fields;
}
add_filter( 'edd_metabox_fields_save', 'edd_fd_edd_metabox_fields_save' );

/**
 * Display post column
 * @since 1.0
*/
function edd_fd_download_columns( $download_columns ) {
	$download_columns['featured'] = __( 'Featured', 'edd-featured-downloads' );
	return $download_columns;
}
add_filter( 'manage_edit-download_columns', 'edd_fd_download_columns' );

/**
 * Adds compatibility with the Frontend Submissions extension.
 * 
 * @since 1.0.4
*/
function edd_fd_fes_download_table_columns( $columns ) {
	$columns['featured'] = __( 'Featured', 'edd-featured-downloads' );
	return $columns;
}
add_filter( 'fes_download_table_columns', 'edd_fd_fes_download_table_columns' );

/**
 * Show 'featured' in column
 * @since 1.0
*/
function edd_fd_render_download_columns( $column_name, $post_id ) {

	$featured = get_post_meta( $post_id, 'edd_feature_download', true );

	switch ( $column_name ) {
		case "featured":

		if ( $featured ) {
			$checked = 'checked';
			echo '<input style="visibility: hidden; display: none;" type="checkbox" checked="checked" readonly="readonly" />';
			_e( 'Featured', 'edd-featured-downloads' );
		}
		break;
	}
}
add_action( 'manage_posts_custom_column', 'edd_fd_render_download_columns', 10, 2 );

/**
 * Add to quick edit
 * @since 1.0
*/
function edd_fd_add_quick_edit( $column_name, $post_type ) {
	if ( $column_name != 'featured' ) {
		return;
	}

	static $printNonce = TRUE;

	if ( $printNonce ) {
		$printNonce = FALSE;
		wp_nonce_field( plugin_basename( __FILE__ ), 'download_edit_nonce' );
	}

?>

<fieldset class="inline-edit-col-right inline-edit-featured">
	<div class="inline-edit-col inline-edit-<?php echo $column_name ?>">
	<?php
		switch ( $column_name ) {
			case 'featured':
		?>
		<legend style="display: none;">Featured</legend>

		<label class="alignleft">
			<input id="edd_feature_download" name="edd_feature_download" class="edd_feature_download" type="checkbox" />
			<span class="checkbox-title"><?php _e( 'Feature Download', 'edd-featured-downloads' ); ?></span>
		</label>
		<?php break;
		}
	?>
	</div>
</fieldset>

<?php
}
add_action( 'quick_edit_custom_box', 'edd_fd_add_quick_edit', 10, 2 );

/**
 * Save function for quick edit
 * 
 * @since 1.0
 * 
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @param bool    $update  Whether this is an existing post being updated or not.
*/
function edd_fd_save_quick_edit_data( $post_id, $post, $update )  {
	
	if ( 'download' !== get_post_type( $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST[ 'download_edit_nonce' ] ) || ! wp_verify_nonce( $_POST['download_edit_nonce'], plugin_basename( __FILE__ ) ) ) {
		return;
	}

	if ( isset( $_REQUEST['edd_feature_download'] ) ) {
		update_post_meta( $post_id, 'edd_feature_download', TRUE );
	} else {
		delete_post_meta( $post_id, 'edd_feature_download' );
	}

}
add_action( 'save_post', 'edd_fd_save_quick_edit_data', 10, 3 );

/**
 * Load scripts in footer
 * @since 1.0
*/
function edd_fd_admin_edit_foot() {
	$slug = 'download';

	if ( ( isset( $_GET['page'] ) && $_GET['page'] == $slug ) || ( isset( $_GET['post_type'] ) && $_GET['post_type'] == $slug ) ) {
		echo '<script type="text/javascript" src="', plugins_url( 'scripts/admin_edit.js', __FILE__ ), '"></script>';
	}
}
add_action( 'admin_footer-edit.php', 'edd_fd_admin_edit_foot', 11 );

/**
 * Template tag to show the featured downloads
 * @since 1.0
*/
if ( ! function_exists('edd_fd_show_featured_downloads') ) {
	function edd_fd_show_featured_downloads( $thumbnails = true, $columns = 3, $number = 3, $price = true, $excerpt = true, $full_content = true, $buy_button = true, $orderby = 'post_date', $order = 'DESC' ) {

		switch ( intval( $columns ) ) :
			case 1:
				$column_width = '100%'; break;
			case 2:
				$column_width = '50%'; break;
			case 3:
				$column_width = '33%'; break;
			case 4:
				$column_width = '25%'; break;
			case 5:
				$column_width = '20%'; break;
			case 6:
				$column_width = '16.6%'; break;
		endswitch;

		if ( post_type_exists('download') ) {
			$post_type_obj = get_post_type_object( 'download' );
		}

		$args = apply_filters( 'edd_fd_featured_downloads_args', array(
			'post_type' => 'download',
			'orderby' => $orderby,
			'order' => $order,
			'posts_per_page' => $number,
			'meta_key' => 'edd_feature_download',
		));

		$featured_downloads = new WP_Query( $args );

		ob_start();
		if ( $featured_downloads->have_posts() ) : $i = 1; ?>
		<div class="edd_downloads_list">
			<?php while ( $featured_downloads->have_posts() ) : $featured_downloads->the_post(); ?>

				<div itemscope itemtype="http://schema.org/Product" class="edd_download" id="edd_download_<?php echo get_the_ID(); ?>" style="width: <?php echo $column_width; ?>; float: left;">
					<div class="edd_download_inner">
						<?php

						do_action( 'edd_download_before' );

						if ( $thumbnails ) :
							edd_get_template_part( 'shortcode', 'content-image' );
						endif;

						edd_get_template_part( 'shortcode', 'content-title' );

						if ( $excerpt && !$full_content )
							edd_get_template_part( 'shortcode', 'content-excerpt' );
						else if ( $full_content )
							edd_get_template_part( 'shortcode', 'content-full' );

						if ( $price )
							edd_get_template_part( 'shortcode', 'content-price' );

						if ( $buy_button )
							edd_get_template_part( 'shortcode', 'content-cart-button' );

						do_action( 'edd_download_after' );

						?>
					</div>
				</div>

			<?php endwhile; ?>
		</div>
		<?php endif; wp_reset_postdata();

		$html = ob_get_clean();
		echo apply_filters( 'edd_fd_featured_downloads_html', $html, $featured_downloads );
	}
}

/**
 * Featured Downloads Shortcode
 * 
 * Created a new shortcode as filtering the shortcode atts is not possible yet
 * https://core.trac.wordpress.org/ticket/15155
 * @since 1.0
 */
function edd_fd_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
			'category'         => '',
			'exclude_category' => '',
			'tags'             => '',
			'exclude_tags'     => '',
			'relation'         => 'AND',
			'number'           => 10,
			'price'            => 'no',
			'excerpt'          => 'yes',
			'full_content'     => 'no',
			'buy_button'       => 'yes',
			'columns'          => 3,
			'thumbnails'       => 'true',
			'orderby'          => 'post_date',
			'order'            => 'DESC',
		), $atts )
	);

	$query = array(
		'post_type'      => 'download',
		'posts_per_page' => absint( $number ),
		'orderby'        => $orderby,
		'order'          => $order,
		'meta_key'		=> 'edd_feature_download'
	);

	switch ( $orderby ) {
		case 'price':
			$orderby           = 'meta_value';
			$query['meta_key'] = 'edd_price';
			$query['orderby']  = 'meta_value_num';
		break;

		case 'title':
			$query['orderby'] = 'title';
		break;

		case 'id':
			$query['orderby'] = 'ID';
		break;

		case 'random':
			$query['orderby'] = 'rand';
		break;

		default:
			$query['orderby'] = 'post_date';
		break;
	}

	if ( $tags || $category || $exclude_category || $exclude_tags ) {
		$query['tax_query'] = array(
			'relation'     => $relation
		);

		if ( $tags ) {
			$query['tax_query'][] = array(
				'taxonomy' => 'download_tag',
				'terms'    => explode( ',', $tags ),
				'field'    => 'slug'
			);
		}

		if ( $category ) {
			$query['tax_query'][] = array(
				'taxonomy' => 'download_category',
				'terms'    => explode( ',', $category ),
				'field'    => 'slug'
			);
		}

		if ( $exclude_category ) {
			$query['tax_query'][] = array(
				'taxonomy' => 'download_category',
				'terms'    => explode( ',', $exclude_category ),
				'field'    => 'slug',
				'operator' => 'NOT IN',
			);
		}

		if ( $exclude_tags ) {
			$query['tax_query'][] = array(
				'taxonomy' => 'download_tag',
				'terms'    => explode( ',', $exclude_tags ),
				'field'    => 'slug',
				'operator' => 'NOT IN',
			);
		}
	}

	if ( get_query_var( 'paged' ) )
		$query['paged'] = get_query_var('paged');
	else if ( get_query_var( 'page' ) )
		$query['paged'] = get_query_var( 'page' );
	else
		$query['paged'] = 1;

	switch ( intval( $columns ) ) :
		case 1:
			$column_width = '100%'; break;
		case 2:
			$column_width = '50%'; break;
		case 3:
			$column_width = '33%'; break;
		case 4:
			$column_width = '25%'; break;
		case 5:
			$column_width = '20%'; break;
		case 6:
			$column_width = '16.6%'; break;
	endswitch;

	// Allow the query to be manipulated by other plugins
	$query = apply_filters( 'edd_featured_downloads_query', $query );

	$downloads = new WP_Query( $query );
	if ( $downloads->have_posts() ) :
		$i = 1;
		ob_start(); ?>
		<div class="edd_downloads_list">
			<?php while ( $downloads->have_posts() ) : $downloads->the_post(); ?>
				<div itemscope itemtype="http://schema.org/Product" class="edd_download" id="edd_download_<?php echo get_the_ID(); ?>" style="width: <?php echo $column_width; ?>; float: left;">
					<div class="edd_download_inner">
						<?php

						do_action( 'edd_download_before' );

						if ( 'false' != $thumbnails ) :
							edd_get_template_part( 'shortcode', 'content-image' );
						endif;

						edd_get_template_part( 'shortcode', 'content-title' );

						if ( $excerpt == 'yes' && $full_content != 'yes' )
							edd_get_template_part( 'shortcode', 'content-excerpt' );
						else if ( $full_content == 'yes' )
							edd_get_template_part( 'shortcode', 'content-full' );

						if ( $price == 'yes' )
							edd_get_template_part( 'shortcode', 'content-price' );

						if ( $buy_button == 'yes' )
							edd_get_template_part( 'shortcode', 'content-cart-button' );

						do_action( 'edd_download_after' );

						?>
					</div>
				</div>
				<?php if ( $i % $columns == 0 ) { ?><div style="clear:both;"></div><?php } ?>
			<?php $i++; endwhile; ?>

			<div style="clear:both;"></div>

			<?php wp_reset_postdata(); ?>
		</div>
		<?php
		$display = ob_get_clean();
	else:
		$display = sprintf( _x( 'No %s found', 'download post type name', 'edd-featured-downloads' ), edd_get_label_plural() );
	endif;

	return apply_filters( 'edd_fd_shortcode', $display, $atts, $buy_button, $columns, $column_width, $downloads, $excerpt, $full_content, $price, $thumbnails, $query );
}
add_shortcode( 'edd_featured_downloads', 'edd_fd_shortcode' );

/**
 * Filter the [downloads] shortcode to include a new "featured" attribute.
 * Example: [downloads featured="yes"]
 *
 * @since 1.0.3
 * 
 * @param array  $out       The output array of shortcode attributes.
 * @param array  $pairs     The supported attributes and their defaults.
 * @param array  $atts      The user defined shortcode attributes.
 * @param string $shortcode The shortcode name. In this case, "downloads".
 * 
 * @return array $out       The output array of shortcode attributes.
 */
function edd_fd_shortcode_atts_downloads( $out, $pairs, $atts, $shortcode ) {
	if ( isset( $atts['featured'] ) && 'yes' === $atts['featured'] ) {
		$out['featured'] = 'yes';
	}

	return $out;
}
add_filter( 'shortcode_atts_downloads', 'edd_fd_shortcode_atts_downloads', 10, 4 );

/**
 * Query downloads by the "edd_feature_download" meta key.
 *
 * @since 1.0.3
 * 
 * @param array  $query
 * @param string $atts 
 * 
 * @return array $query
 */
function edd_fd_filter_downloads_query( $query, $atts ) {
	if ( isset( $atts['featured'] ) && 'yes' === $atts['featured'] ) {
		$query['meta_key'] = 'edd_feature_download';
	}

	return $query;
}
add_filter( 'edd_downloads_query', 'edd_fd_filter_downloads_query', 10, 2 );