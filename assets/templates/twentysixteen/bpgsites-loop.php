<?php
/**
 * BP Group Sites - Group Sites Loop.
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_legacy_theme_object_filter().
 *
 * @package BuddyPress
 * @subpackage BP_Group_Sites
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Fires before the start of the blogs loop.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_blogs_loop' );

// Search for them - TODO: add AJAX query string compatibility.
if ( bpgsites_has_blogs( bp_ajax_querystring( 'bpgsites' ) ) ) {

	?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="blog-dir-count-top">
			<?php bpgsites_blogs_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="blog-dir-pag-top">
			<?php bp_blogs_pagination_links(); ?>
		</div>

	</div>

	<?php

	/**
	 * Fires before the blogs directory list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_directory_blogs_list' );

	?>

	<ul id="blogs-list" class="item-list">

	<?php while ( bp_blogs() ) : ?>
		<?php bp_the_blog(); ?>

		<li>
			<div class="item-avatar">
				<a href="<?php bp_blog_permalink(); ?>"><?php bp_blog_avatar( 'type=thumb' ); ?></a>
			</div>

			<div class="item">
				<div class="item-title"><a href="<?php bp_blog_permalink(); ?>"><?php bp_blog_name(); ?></a></div>
				<div class="item-meta"><span class="activity"><?php bp_blog_last_active(); ?></span></div>

				<?php

				/**
				 * Fires after the listing of a blog item in the blogs loop.
				 *
				 * @since 1.2.0
				 */
				do_action( 'bp_directory_blogs_item' );

				?>
			</div>

			<div class="action">

				<?php

				/**
				 * Fires inside the blogs action listing area.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_directory_blogs_actions' );

				?>

				<div class="meta">

					<?php bp_blog_latest_post(); ?>

				</div>

			</div>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php

	/**
	 * Fires after the blogs directory list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_directory_blogs_list' );

	?>

	<?php bp_blog_hidden_fields(); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="blog-dir-count-bottom">
			<?php bpgsites_blogs_pagination_count(); ?>
		</div>

		<div class="pagination-links" id="blog-dir-pag-bottom">
			<?php bp_blogs_pagination_links(); ?>
		</div>

	</div>

	<?php

} else {

	?>

	<div id="message" class="info">
		<p><?php esc_html_e( 'Sorry, there were no sites found.', 'bp-group-sites' ); ?></p>
	</div>

	<?php

}

/**
 * Fires after the display of the blogs loop.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_blogs_loop' ); ?>
