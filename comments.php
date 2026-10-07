<?php
/**
 * Comments template.
 *
 * @package Asosyoloji
 */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area aso-container">
	<?php if ( have_comments() ) : ?>
		<div class="section-heading">
			<div>
				<div class="section-kicker"><?php esc_html_e( 'Tartışma', 'asosyoloji' ); ?></div>
				<h2 class="section-title">
					<?php
					/* translators: %1$s: Number of comments. */
					printf(
						esc_html( _nx( '%1$s yorum', '%1$s yorum', get_comments_number(), 'yorum sayısı', 'asosyoloji' ) ),
						esc_html( number_format_i18n( get_comments_number() ) )
					);
					?>
				</h2>
			</div>
		</div>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => 56,
				)
			);
			?>
		</ol>

		<?php the_comments_pagination(); ?>
	<?php endif; ?>

	<?php if ( comments_open() ) : ?>
		<?php comment_form(); ?>
	<?php endif; ?>
</section>
