<?php
/**
 * Single event template.
 *
 * @package Asosyoloji
 */

get_header();

while ( have_posts() ) :
	the_post();

	$post_id    = get_the_ID();
	$start_ts   = absint( get_post_meta( $post_id, 'em_start_date_time', true ) );
	$end_ts     = absint( get_post_meta( $post_id, 'em_end_date_time', true ) );
	$start_time = get_post_meta( $post_id, 'em_start_time', true );
	$end_time   = get_post_meta( $post_id, 'em_end_time', true );
	$all_day    = (bool) get_post_meta( $post_id, 'em_all_day', true );
	$price      = get_post_meta( $post_id, 'em_fixed_event_price', true );
	$types      = taxonomy_exists( 'em_event_type' ) ? wp_get_post_terms( $post_id, 'em_event_type', array( 'fields' => 'names' ) ) : array();
	$venues     = taxonomy_exists( 'em_venue' ) ? wp_get_post_terms( $post_id, 'em_venue', array( 'fields' => 'names' ) ) : array();
	$event_data = function_exists( 'asosyoloji_weekly_event_data' ) ? asosyoloji_weekly_event_data( $post_id ) : array();
	$city       = sanitize_text_field( $event_data['city'] ?? '' );
	$organizer  = sanitize_text_field( $event_data['organizer'] ?? '' );
	$event_url  = esc_url( $event_data['event_url'] ?? '' );
	$is_free    = ! empty( $event_data['free'] );

	$start_date = $start_ts ? gmdate( 'Y-m-d', $start_ts ) : '';
	$end_date   = $end_ts ? gmdate( 'Y-m-d', $end_ts ) : $start_date;
	$is_multiday = $start_date && $end_date && $start_date !== $end_date;

	$date_label = '';
	if ( $start_date ) {
		if ( $is_multiday ) {
			$date_label = wp_date( 'd F Y', strtotime( $start_date ) ) . ' – ' . wp_date( 'd F Y', strtotime( $end_date ) );
		} else {
			$date_label = wp_date( 'd F Y', strtotime( $start_date ) );
		}
	}

	$time_label = '';
	if ( ! $all_day ) {
		if ( $start_time && $end_time ) {
			$time_label = $start_time . ' – ' . $end_time;
		} elseif ( $start_time ) {
			$time_label = $start_time;
		}
	}

	$share_links = function_exists( 'asosyoloji_share_links' ) ? asosyoloji_share_links( $post_id ) : array();
	$entry_image = asosyoloji_get_post_image( $post_id, 'full', array( 'class' => 'event-detail__image' ) );
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'event-detail' ); ?>>
		<header class="event-detail__hero">
			<div class="aso-container event-detail__hero-grid">
				<div class="event-detail__intro">
					<div class="event-detail__kicker">
						<?php echo ! is_wp_error( $types ) && ! empty( $types ) ? esc_html( $types[0] ) : esc_html__( 'Etkinlik', 'asosyoloji' ); ?>
					</div>

					<h1 class="event-detail__title"><?php the_title(); ?></h1>

					<?php if ( has_excerpt() ) : ?>
						<div class="event-detail__deck"><?php echo esc_html( get_the_excerpt() ); ?></div>
					<?php endif; ?>

					<div class="event-detail__facts">
						<?php if ( $date_label ) : ?>
							<div class="event-detail__fact">
								<span class="event-detail__fact-label"><?php esc_html_e( 'Tarih', 'asosyoloji' ); ?></span>
								<strong><?php echo esc_html( $date_label ); ?></strong>
							</div>
						<?php endif; ?>

						<?php if ( $all_day || $time_label ) : ?>
							<div class="event-detail__fact">
								<span class="event-detail__fact-label"><?php esc_html_e( 'Saat', 'asosyoloji' ); ?></span>
								<strong><?php echo $all_day ? esc_html__( 'Tüm gün', 'asosyoloji' ) : esc_html( $time_label ); ?></strong>
							</div>
						<?php endif; ?>

						<?php if ( ! is_wp_error( $venues ) && ! empty( $venues ) ) : ?>
							<div class="event-detail__fact">
								<span class="event-detail__fact-label"><?php esc_html_e( 'Mekan', 'asosyoloji' ); ?></span>
								<strong><?php echo esc_html( $venues[0] ); ?></strong>
							</div>
						<?php endif; ?>

						<?php if ( '' !== (string) $price ) : ?>
							<div class="event-detail__fact">
								<span class="event-detail__fact-label"><?php esc_html_e( 'Bilet', 'asosyoloji' ); ?></span>
								<strong><?php echo esc_html( $price ); ?></strong>
							</div>
						<?php endif; ?>

						<?php if ( $city ) : ?>
							<div class="event-detail__fact">
								<span class="event-detail__fact-label"><?php esc_html_e( 'Şehir', 'asosyoloji' ); ?></span>
								<strong><?php echo esc_html( $city ); ?></strong>
							</div>
						<?php endif; ?>

						<?php if ( $organizer ) : ?>
							<div class="event-detail__fact">
								<span class="event-detail__fact-label"><?php esc_html_e( 'Organizatör', 'asosyoloji' ); ?></span>
								<strong><?php echo esc_html( $organizer ); ?></strong>
							</div>
						<?php endif; ?>

						<?php if ( $is_free ) : ?>
							<div class="event-detail__fact">
								<span class="event-detail__fact-label"><?php esc_html_e( 'Katılım', 'asosyoloji' ); ?></span>
								<strong><?php esc_html_e( 'Ücretsiz', 'asosyoloji' ); ?></strong>
							</div>
						<?php endif; ?>
					</div>

					<div class="event-detail__actions">
						<?php if ( function_exists( 'asosyoloji_weekly_ics_url' ) ) : ?>
							<a class="aso-button" href="<?php echo esc_url( asosyoloji_weekly_ics_url( $post_id ) ); ?>"><?php esc_html_e( 'Takvime Ekle', 'asosyoloji' ); ?></a>
						<?php endif; ?>
						<?php if ( function_exists( 'asosyoloji_weekly_google_calendar_url' ) ) : ?>
							<a class="aso-button aso-button--secondary" href="<?php echo esc_url( asosyoloji_weekly_google_calendar_url( $post_id ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Google Calendar', 'asosyoloji' ); ?></a>
						<?php endif; ?>
						<?php if ( $event_url ) : ?>
							<a class="aso-button aso-button--secondary" href="<?php echo esc_url( $event_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Etkinlik Sayfası', 'asosyoloji' ); ?></a>
						<?php endif; ?>

						<?php if ( $share_links ) : ?>
							<div class="event-detail__share">
								<span><?php esc_html_e( 'Paylaş', 'asosyoloji' ); ?></span>
								<?php foreach ( $share_links as $network => $share_url ) : ?>
									<a href="<?php echo esc_url( $share_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( ucfirst( $network ) ); ?></a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( $entry_image ) : ?>
					<figure class="event-detail__media"><?php echo wp_kses_post( $entry_image ); ?></figure>
				<?php endif; ?>
			</div>
		</header>

		<div class="aso-container event-detail__layout">
			<div class="event-detail__content entry-content">
				<?php the_content(); ?>
			</div>

			<aside class="event-detail__aside">
				<div class="event-detail__aside-card">
					<div class="event-detail__aside-label"><?php esc_html_e( 'Etkinlik Bilgileri', 'asosyoloji' ); ?></div>

					<?php if ( $date_label ) : ?>
						<p><strong><?php esc_html_e( 'Tarih', 'asosyoloji' ); ?></strong><br><?php echo esc_html( $date_label ); ?></p>
					<?php endif; ?>

					<?php if ( $all_day || $time_label ) : ?>
						<p><strong><?php esc_html_e( 'Saat', 'asosyoloji' ); ?></strong><br><?php echo $all_day ? esc_html__( 'Tüm gün', 'asosyoloji' ) : esc_html( $time_label ); ?></p>
					<?php endif; ?>

					<?php if ( ! is_wp_error( $venues ) && ! empty( $venues ) ) : ?>
						<p><strong><?php esc_html_e( 'Mekan', 'asosyoloji' ); ?></strong><br><?php echo esc_html( $venues[0] ); ?></p>
					<?php endif; ?>

					<?php if ( ! is_wp_error( $types ) && ! empty( $types ) ) : ?>
						<p><strong><?php esc_html_e( 'Tür', 'asosyoloji' ); ?></strong><br><?php echo esc_html( implode( ', ', $types ) ); ?></p>
					<?php endif; ?>

					<?php if ( $city ) : ?>
						<p><strong><?php esc_html_e( 'Şehir', 'asosyoloji' ); ?></strong><br><?php echo esc_html( $city ); ?></p>
					<?php endif; ?>

					<?php if ( $organizer ) : ?>
						<p><strong><?php esc_html_e( 'Organizatör', 'asosyoloji' ); ?></strong><br><?php echo esc_html( $organizer ); ?></p>
					<?php endif; ?>
				</div>
			</aside>
		</div>
	</article>
	<?php
endwhile;

get_footer();
