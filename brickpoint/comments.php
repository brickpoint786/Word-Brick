<?php
/**
 * Comments.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="bp-comments bp-mt">
	<?php if ( have_comments() ) : ?>
		<h2 class="bp-h3"><?php printf( esc_html( _n( '%s Comment', '%s Comments', get_comments_number(), 'brickpoint' ) ), esc_html( number_format_i18n( get_comments_number() ) ) ); ?></h2>
		<ol class="bp-comment-list"><?php wp_list_comments( array( 'style' => 'ol', 'avatar_size' => 48 ) ); ?></ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>
	<?php comment_form( array( 'class_submit' => 'bp-btn btn-brick' ) ); ?>
</div>
