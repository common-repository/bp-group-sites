<?php
/**
 * BP Group Sites - Group Sites Directory.
 *
 * @package BuddyPress
 * @subpackage BP_Group_Sites
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header( 'buddypress' );

?>

<!-- theme/bpgsites/index.php -->
	<?php do_action( 'bp_before_directory_groupsites_page' ); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<article id="post-0" class="post-0 page type-page status-publish hentry">

				<header class="entry-header">
					<h1 class="entry-title">
						<?php

						echo sprintf(
							/* translators: %s: The plural name for Group Sites. */
							esc_html__( '%s Directory', 'bp-group-sites' ),
							esc_html( bpgsites_get_extension_plural() )
						);

						?>
					</h1>
				</header><!-- .entry-header -->

				<div class="entry-content">

					<div id="buddypress">

						<?php do_action( 'bp_before_directory_groupsites' ); ?>

						<?php do_action( 'bp_before_directory_groupsites_content' ); ?>

						<div id="blog-dir-search" class="dir-search" role="search">
							<?php bp_directory_blogs_search_form(); ?>
						</div><!-- #blog-dir-search -->

						<form action="" method="post" id="bpgsites-directory-form" class="dir-form">

							<div class="item-list-tabs" role="navigation">
								<ul>
									<li class="selected" id="bpgsites-all">
										<a href="<?php bp_root_domain(); ?>/<?php bpgsites_root_slug(); ?>">
											<?php

											// Show subnav title.
											printf(
												/* translators: 1: The plural name for Group Sites, 2: The number of Group Sites. */
												__( 'All %1$s %2$s', 'bp-group-sites' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												esc_html( bpgsites_get_extension_plural() ),
												'<span>' . esc_html( bpgsites_get_total_blog_count() ) . '</span>'
											);

											?>
										</a>
									</li>

									<?php do_action( 'bp_blogs_directory_blog_types' ); ?>

								</ul>
							</div><!-- .item-list-tabs -->

							<div class="item-list-tabs" id="subnav" role="navigation">
								<ul>

									<?php do_action( 'bp_blogs_directory_blog_sub_types' ); ?>

									<li id="bpgsites-order-select" class="last filter">

										<label for="bpgsites-order-by"><?php esc_html_e( 'Order By:', 'bp-group-sites' ); ?></label>
										<select id="bpgsites-order-by">
											<option value="active"><?php esc_html_e( 'Last Active', 'bp-group-sites' ); ?></option>
											<option value="newest"><?php esc_html_e( 'Newest', 'bp-group-sites' ); ?></option>
											<option value="alphabetical"><?php esc_html_e( 'Alphabetical', 'bp-group-sites' ); ?></option>

											<?php do_action( 'bp_blogs_directory_order_options' ); ?>

										</select>
									</li>
								</ul>
							</div>

							<div id="bpgsites-dir-list" class="bpgsites dir-list">

								<?php bp_locate_template( [ 'bpgsites/bpgsites-loop.php' ], true, false ); ?>

							</div><!-- #bpgsites-dir-list -->

							<?php do_action( 'bp_directory_groupsites_content' ); ?>

							<?php wp_nonce_field( 'directory_bpgsites', '_wpnonce-bpgsites-filter' ); ?>

							<?php do_action( 'bp_after_directory_groupsites_content' ); ?>

						</form><!-- #bpgsites-directory-form -->

						<?php do_action( 'bp_after_directory_groupsites' ); ?>

					</div><!-- .buddypress -->

				</div><!-- .entry-content -->

			</article><!-- #post-## -->

		</main><!-- #main -->
	</div><!-- #primary -->

	<?php do_action( 'bp_after_directory_groupsites_page' ); ?>

<?php get_sidebar( 'buddypress' ); ?>
<?php get_footer( 'buddypress' ); ?>
