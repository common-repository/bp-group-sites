<?php
/**
 * BP Group Sites Linkage Functions.
 *
 * Functions which don't need to be in the loop live here.
 *
 * @package BP_Group_Sites
 * @since 0.1
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * For a given blog ID, get the array of group IDs.
 *
 * @since 0.1
 *
 * @param int $blog_id The numeric ID of the blog.
 * @return array $group_ids Array of numeric IDs of groups.
 */
function bpgsites_get_groups_by_blog_id( $blog_id ) {

	// Construct option name.
	$option_name = BPGSITES_PREFIX . $blog_id;

	// Get option.
	$group_ids = get_site_option( $option_name, [] );

	// Make sure IDs are integers.
	array_walk( $group_ids, 'intval' );

	// --<
	return $group_ids;

}

/**
 * For a given group ID, add a given group ID.
 *
 * @since 0.1
 *
 * @param int $group_id The numeric ID of the group.
 * @return array $blog_ids Array of numeric IDs of blogs.
 */
function bpgsites_get_blogs_by_group_id( $group_id ) {

	// Get option if it exists.
	$blog_ids = groups_get_groupmeta( $group_id, BPGSITES_OPTION );

	// Sanity check.
	if ( ! is_array( $blog_ids ) ) {
		$blog_ids = [];
	}

	// Make sure IDs are integers.
	array_walk( $blog_ids, 'intval' );

	// --<
	return $blog_ids;

}

/**
 * For a given blog ID, check if it is associated with a given group ID.
 *
 * @since 0.1
 *
 * @param int $blog_id The numeric ID of the blog.
 * @param int $group_id The numeric ID of the group.
 * @return bool $return Whether or not the group is associated with a blog.
 */
function bpgsites_check_group_by_blog_id( $blog_id, $group_id ) {

	// Init return.
	$return = false;

	// Get array of group IDs.
	$group_ids = bpgsites_get_groups_by_blog_id( $blog_id );

	// Sanity check.
	if ( is_array( $group_ids ) && count( $group_ids ) > 0 ) {

		// Is the group ID in the array?
		if ( in_array( (int) $group_id, $group_ids, true ) ) {
			$return = true;
		}

	}

	// Allow for now.
	return $return;

}

/**
 * For a given group ID, check if it is associated with a given blog ID.
 *
 * @since 0.1
 *
 * @param int $group_id The numeric ID of the group.
 * @param int $blog_id The numeric ID of the blog.
 * @return bool $return Whether or not the blog is associated with a group.
 */
function bpgsites_check_blog_by_group_id( $group_id, $blog_id ) {

	// Init return.
	$return = false;

	// Get array of blog IDs.
	$blog_ids = bpgsites_get_blogs_by_group_id( $group_id );

	// Is the blog ID present?
	if ( in_array( (int) $blog_id, $blog_ids, true ) ) {
		$return = true;
	}

	// --<
	return $return;

}

/**
 * Reciprocal addition of IDs.
 *
 * @since 0.1
 *
 * @param int $blog_id The numeric ID of the blog.
 * @param int $group_id The numeric ID of the group.
 */
function bpgsites_link_blog_and_group( $blog_id, $group_id ) {

	// Set blog options.
	bpgsites_configure_blog_options( $blog_id );

	// Add to blog's option.
	bpgsites_add_group_to_blog( $blog_id, $group_id );

	// Add to group's option.
	bpgsites_add_blog_to_group( $group_id, $blog_id );

}

/**
 * Reciprocal deletion of IDs.
 *
 * @since 0.1
 *
 * @param int $blog_id The numeric ID of the blog.
 * @param int $group_id The numeric ID of the group.
 */
function bpgsites_unlink_blog_and_group( $blog_id, $group_id ) {

	// Remove from blog's option.
	bpgsites_remove_group_from_blog( $blog_id, $group_id );

	// Remove from group's option.
	bpgsites_remove_blog_from_group( $group_id, $blog_id );

	// Unset blog options.
	bpgsites_reset_blog_options( $blog_id );

}

/**
 * Set blog options.
 *
 * @since 0.1
 *
 * @param int $blog_id The numeric ID of the blog.
 */
function bpgsites_configure_blog_options( $blog_id ) {

	// Kick out if already a group site.
	if ( bpgsites_is_groupsite( $blog_id ) ) {
		return;
	}

	// Go there.
	switch_to_blog( $blog_id );

	// Get existing comment_registration option.
	$existing_option = get_option( 'comment_registration', 0 );

	// Store it for later.
	add_option( 'bpgsites_saved_comment_registration', $existing_option );

	// Anonymous commenting - off by default.
	$anon_comments = apply_filters(
		'bpgsites_require_comment_registration',
		0 // Disallow.
	);

	// Update option.
	update_option( 'comment_registration', $anon_comments );

	// Switch back.
	restore_current_blog();

	// Add blog ID to globally stored option.
	bpgsites_register_groupsite( $blog_id );

}

/**
 * Unset blog options.
 *
 * @since 0.1
 *
 * @param int $blog_id The numeric ID of the blog.
 */
function bpgsites_reset_blog_options( $blog_id ) {

	// Kick out if still a group site.
	if ( bpgsites_is_groupsite( $blog_id ) ) {
		return;
	}

	// Go there.
	switch_to_blog( $blog_id );

	// Get saved comment_registration option.
	$previous_option = get_option( 'bpgsites_saved_comment_registration', 0 );

	// Remove our saved one.
	delete_option( 'bpgsites_saved_comment_registration' );

	// Update option.
	update_option( 'comment_registration', $previous_option );

	// Switch back.
	restore_current_blog();

	// Remove blog ID from globally stored option.
	bpgsites_deregister_groupsite( $blog_id );

}

/**
 * For a given blog ID, add a given group ID.
 *
 * @since 0.1
 *
 * @param int $blog_id The numeric ID of the blog.
 * @param int $group_id The numeric ID of the group.
 */
function bpgsites_add_group_to_blog( $blog_id, $group_id ) {

	// Get array of group IDs.
	$group_ids = bpgsites_get_groups_by_blog_id( $blog_id );

	// Add group ID.
	$group_ids[] = $group_id;

	// Save updated option.
	update_site_option( BPGSITES_PREFIX . $blog_id, $group_ids );

}

/**
 * For a given group ID, add a given blog ID.
 *
 * @since 0.1
 *
 * @param int $group_id The numeric ID of the group.
 * @param int $blog_id The numeric ID of the blog.
 */
function bpgsites_add_blog_to_group( $group_id, $blog_id ) {

	// Get array of blog IDs.
	$blog_ids = bpgsites_get_blogs_by_group_id( $group_id );

	// Is the blog ID present?
	if ( ! in_array( (int) $blog_id, $blog_ids, true ) ) {

		// No, add blog ID.
		$blog_ids[] = $blog_id;

		// Save updated option.
		groups_update_groupmeta( $group_id, BPGSITES_OPTION, $blog_ids );

	}

}

/**
 * For a given blog ID, remove a given group ID.
 *
 * @since 0.1
 *
 * @param int $blog_id The numeric ID of the blog.
 * @param int $group_id The numeric ID of the group.
 */
function bpgsites_remove_group_from_blog( $blog_id, $group_id ) {

	// Get array of group IDs.
	$group_ids = bpgsites_get_groups_by_blog_id( $blog_id );

	// Is the group ID present?
	if ( in_array( (int) $group_id, $group_ids, true ) ) {

		// Remove group ID and re-index.
		$updated = array_merge( array_diff( $group_ids, [ $group_id ] ) );

		// Save updated option.
		update_site_option( BPGSITES_PREFIX . $blog_id, $updated );

	}

}

/**
 * For a given group ID, remove a given blog ID.
 *
 * @since 0.1
 *
 * @param int $group_id The numeric ID of the group.
 * @param int $blog_id The numeric ID of the blog.
 */
function bpgsites_remove_blog_from_group( $group_id, $blog_id ) {

	// Get array of blog IDs.
	$blog_ids = bpgsites_get_blogs_by_group_id( $group_id );

	// Is the blog ID present?
	if ( in_array( (int) $blog_id, $blog_ids, true ) ) {

		// Yes, remove blog ID and re-index.
		$updated = array_merge( array_diff( $blog_ids, [ $blog_id ] ) );

		// Save updated option.
		groups_update_groupmeta( $group_id, BPGSITES_OPTION, $updated );

	}

}

/**
 * Sever link when a site gets deleted.
 *
 * @since 0.1
 *
 * @param int  $blog_id The numeric ID of the blog.
 * @param bool $drop Dummy param to avoid callback errors.
 */
function bpgsites_remove_blog_from_groups( $blog_id, $drop = false ) {

	// Get array of group IDs.
	$group_ids = bpgsites_get_groups_by_blog_id( $blog_id );

	// Sanity check.
	if ( is_array( $group_ids ) && count( $group_ids ) > 0 ) {

		// Loop through them.
		foreach ( $group_ids as $group_id ) {

			// Unlink.
			bpgsites_remove_blog_from_group( $group_id, $blog_id );

		}

	}

	// Delete the site option.
	delete_site_option( BPGSITES_PREFIX . $blog_id );

}

/*
 * Sever links when blog deleted.
 *
 * This action has been deprecated since WordPress 5.1.
 *
 * @see wp_delete_site()
 */
if ( ! function_exists( 'wp_delete_site' ) ) {
	add_action( 'delete_blog', 'bpgsites_remove_blog_from_groups', 10 );
}

/**
 * Sever link when a site gets deleted.
 *
 * @since 0.2.8
 *
 * @param WP_Site $old_site Deleted site object.
 */
function bpgsites_remove_site_from_groups( $old_site ) {

	// Sever the link.
	bpgsites_remove_blog_from_groups( $old_site->blog_id );

}

/*
 * Sever links when a site is about to be deleted.
 *
 * This action replaces 'delete_blog' since WordPress 5.1.
 *
 * @see bpgsites_remove_blog_from_groups()
 */
add_action( 'wp_uninitialize_site', 'bpgsites_remove_site_from_groups', 10 );

/**
 * Sever link before a group gets deleted so we can still access meta.
 *
 * Our option will be deleted by groups_delete_group().
 *
 * @since 0.1
 *
 * @param int $group_id The numeric ID of the group.
 */
function bpgsites_remove_group_from_blogs( $group_id ) {

	// Get array of blog IDs.
	$blog_ids = bpgsites_get_blogs_by_group_id( $group_id );

	// Sanity check.
	if ( count( $blog_ids ) === 0 ) {
		return;
	}

	// Unlink them.
	foreach ( $blog_ids as $blog_id ) {
		bpgsites_remove_group_from_blog( $blog_id, $group_id );
	}

}

// Sever links just before group is deleted, while meta still exists.
add_action( 'groups_before_delete_group', 'bpgsites_remove_group_from_blogs', 10 );

/**
 * Check if blog is a groupblog.
 *
 * @since 0.1
 *
 * @param int $blog_id The numeric ID of the blog.
 * @return bool $return Whether the blog is a groupblog or not.
 */
function bpgsites_is_groupblog( $blog_id ) {

	// Init return.
	$return = false;

	// Do we have groupblogs enabled?
	if ( function_exists( 'get_groupblog_group_id' ) ) {

		// Yes, get group id.
		$group_id = get_groupblog_group_id( $blog_id );

		// Is this blog a groupblog?
		if ( is_numeric( $group_id ) && $group_id > 0 ) {
			$return = true;
		}

	}

	// --<
	return $return;

}

/**
 * Check if blog is a groupsite.
 *
 * @since 0.1
 *
 * @param int $blog_id The numeric ID of the blog.
 * @return bool $return Whether the blog is a groupsite.
 */
function bpgsites_is_groupsite( $blog_id ) {

	// Init return.
	$return = false;

	// Get groups this site belongs to.
	$group_ids = bpgsites_get_groups_by_blog_id( $blog_id );

	// If we have any group IDs, then it is.
	if ( count( $group_ids ) > 0 ) {
		$return = true;
	}

	// --<
	return $return;

}

/**
 * Get array of all groupsite blog IDs.
 *
 * @since 0.1
 *
 * @return array $blog_ids Array of numeric IDs of the group site blogs.
 */
function bpgsites_get_groupsites() {

	// Create option if it doesn't exist.
	if ( ! bp_groupsites()->admin->option_exists( 'bpgsites_groupsites' ) ) {
		bp_groupsites()->admin->option_set( 'bpgsites_groupsites', [] );
		bp_groupsites()->admin->options_save();
	}

	// Get existing option.
	$existing = bp_groupsites()->admin->option_get( 'bpgsites_groupsites' );

	// --<
	return $existing;

}

/**
 * Get all blogs that are (or can be) group sites.
 *
 * At present, this means excluding the root blog and group blogs, but additional
 * blogs (such as "working papers") can be excluded using the provided filter.
 *
 * @since 0.1
 *
 * @return array $filtered_blogs An array of all possible group sites.
 */
function bpgsites_get_all_possible_groupsites() {

	// Get all blogs via BP_Blogs_Blog.
	$all = BP_Blogs_Blog::get_all();

	// Init return.
	$filtered_blogs = [];

	// Get array of blog IDs.
	if ( is_array( $all['blogs'] ) && count( $all['blogs'] ) > 0 ) {
		foreach ( $all['blogs'] as $blog ) {

			// Is it the BP root blog?
			if ( (int) bp_get_root_blog_id() === (int) $blog->blog_id ) {
				continue;
			}

			// Is it a groupblog?
			if ( bpgsites_is_groupblog( $blog->blog_id ) ) {
				continue;
			}

			// Okay, none of those - add it.
			$filtered_blogs[] = $blog->blog_id;

		}
	}

	// Allow other plugins to exclude further blogs.
	return apply_filters( 'bpgsites_get_all_possible_groupsites', $filtered_blogs );

}

/**
 * Store blog ID in plugin data.
 *
 * @since 0.1
 *
 * @param int $blog_id The numeric ID of the blog.
 */
function bpgsites_register_groupsite( $blog_id ) {

	// Create option if it doesn't exist.
	if ( ! bp_groupsites()->admin->option_exists( 'bpgsites_groupsites' ) ) {
		bp_groupsites()->admin->option_set( 'bpgsites_groupsites', [] );
	}

	// Get existing option.
	$existing = bp_groupsites()->admin->option_get( 'bpgsites_groupsites' );

	// Make sure IDs are integers.
	array_walk( $existing, 'intval' );

	// Bail if the blog already present.
	if ( in_array( (int) $blog_id, $existing, true ) ) {
		return;
	}

	// Add to the array - key is the same for easier removal.
	$existing[ (int) $blog_id ] = (int) $blog_id;

	// Overwrite.
	bp_groupsites()->admin->option_set( 'bpgsites_groupsites', $existing );

	// Save.
	bp_groupsites()->admin->options_save();

}

/**
 * Clear blog ID from plugin data.
 *
 * @since 0.1
 *
 * @param int $blog_id The numeric ID of the blog.
 */
function bpgsites_deregister_groupsite( $blog_id ) {

	// Get existing option.
	$existing = bp_groupsites()->admin->option_get( 'bpgsites_groupsites' );

	// Sanity check.
	if ( false === $existing ) {
		return;
	}

	// Make sure IDs are integers.
	array_walk( $existing, 'intval' );

	// Bail if the blog is not present.
	if ( ! in_array( (int) $blog_id, $existing, true ) ) {
		return;
	}

	// Add to the array - key is the same as the value.
	unset( $existing[ $blog_id ], $existing[ (int) $blog_id ] );

	// Overwrite.
	bp_groupsites()->admin->option_set( 'bpgsites_groupsites', $existing );

	// Save.
	bp_groupsites()->admin->options_save();

}
