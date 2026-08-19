<?php
defined( 'ABSPATH' ) || exit;

/**
 * أكواد قصيرة تُستخدم داخل الصفحات:
 * [clinic_doctors] [clinic_services] [clinic_insurance] [clinic_hours] [clinic_contact field="phone"]
 */

// شبكة الأطباء — يقبل specialty="slug" و limit="6".
add_shortcode( 'clinic_doctors', function ( $atts ) {
	$atts  = shortcode_atts( array( 'specialty' => '', 'limit' => -1 ), $atts );
	$args  = array(
		'post_type'      => 'clinic_doctor',
		'posts_per_page' => (int) $atts['limit'],
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	);
	if ( $atts['specialty'] ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'clinic_specialty',
				'field'    => 'slug',
				'terms'    => $atts['specialty'],
			),
		);
	}
	$q = new WP_Query( $args );
	if ( ! $q->have_posts() ) {
		return '<p>لم تتم إضافة أطباء بعد.</p>';
	}
	$out = '<div class="clinic-cards-grid">';
	while ( $q->have_posts() ) {
		$q->the_post();
		$id        = get_the_ID();
		$position  = function_exists( 'get_field' ) ? (string) get_field( 'position', $id ) : '';
		$specialty = get_the_term_list( $id, 'clinic_specialty', '', '، ' );
		$photo     = get_the_post_thumbnail( $id, 'medium_large', array( 'loading' => 'lazy' ) );
		if ( ! $photo ) {
			$photo = '<span class="dashicons dashicons-admin-users" style="font-size:80px;width:80px;height:80px;color:#BFD2F8;margin:60px auto;display:block;"></span>';
		}
		$out .= '<div class="clinic-card">';
		$out .= '<a class="card-photo" href="' . esc_url( get_permalink() ) . '">' . $photo . '</a>';
		$out .= '<div class="card-body">';
		if ( $specialty && ! is_wp_error( $specialty ) ) {
			$out .= '<div class="card-specialty">' . wp_strip_all_tags( $specialty ) . '</div>';
		}
		$out .= '<h3 class="card-name"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
		if ( $position ) {
			$out .= '<div class="card-position">' . esc_html( $position ) . '</div>';
		}
		$out .= '</div></div>';
	}
	wp_reset_postdata();
	return $out . '</div>';
} );

// شبكة الخدمات — يقبل limit.
add_shortcode( 'clinic_services', function ( $atts ) {
	$atts = shortcode_atts( array( 'limit' => -1 ), $atts );
	$q    = new WP_Query( array(
		'post_type'      => 'clinic_service',
		'posts_per_page' => (int) $atts['limit'],
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	) );
	if ( ! $q->have_posts() ) {
		return '<p>لم تتم إضافة خدمات بعد.</p>';
	}
	$out = '<div class="clinic-cards-grid">';
	while ( $q->have_posts() ) {
		$q->the_post();
		$photo = get_the_post_thumbnail( get_the_ID(), 'medium_large', array( 'loading' => 'lazy' ) );
		$out  .= '<div class="clinic-card">';
		if ( $photo ) {
			$out .= '<a class="card-photo" href="' . esc_url( get_permalink() ) . '">' . $photo . '</a>';
		}
		$out .= '<div class="card-body">';
		$out .= '<h3 class="card-name"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
		$out .= '<div class="card-excerpt">' . esc_html( wp_trim_words( get_the_excerpt(), 20 ) ) . '</div>';
		$out .= '</div></div>';
	}
	wp_reset_postdata();
	return $out . '</div>';
} );

// شبكة شركات التأمين (شعارات).
add_shortcode( 'clinic_insurance', function () {
	$q = new WP_Query( array(
		'post_type'      => 'clinic_insurance',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	) );
	if ( ! $q->have_posts() ) {
		return '<p>لم تتم إضافة شركات تأمين بعد.</p>';
	}
	$out = '<div class="clinic-cards-grid" style="grid-template-columns:repeat(auto-fill,minmax(180px,1fr));">';
	while ( $q->have_posts() ) {
		$q->the_post();
		$id    = get_the_ID();
		$logo  = get_the_post_thumbnail( $id, 'medium', array( 'loading' => 'lazy', 'style' => 'height:90px;object-fit:contain;width:100%;' ) );
		$notes = function_exists( 'get_field' ) ? (string) get_field( 'notes', $id ) : '';
		$out  .= '<div class="clinic-card"><div class="card-body">';
		if ( $logo ) {
			$out .= $logo;
		}
		$out .= '<h3 class="card-name" style="font-size:16px;">' . esc_html( get_the_title() ) . '</h3>';
		if ( $notes ) {
			$out .= '<div class="card-excerpt" style="font-size:13px;color:#666;">' . esc_html( $notes ) . '</div>';
		}
		$out .= '</div></div>';
	}
	wp_reset_postdata();
	return $out . '</div>';
} );

// شبكة التخصصات / الأقسام.
add_shortcode( 'clinic_specialties', function () {
	$terms = get_terms( array(
		'taxonomy'   => 'clinic_specialty',
		'hide_empty' => false,
	) );
	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return '<p>لم تتم إضافة تخصصات بعد.</p>';
	}
	$out = '<div class="clinic-cards-grid">';
	foreach ( $terms as $term ) {
		$count = (int) $term->count;
		$out  .= '<div class="clinic-card"><div class="card-body">';
		$out  .= '<h3 class="card-name"><a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a></h3>';
		if ( $term->description ) {
			$out .= '<div class="card-excerpt">' . esc_html( $term->description ) . '</div>';
		}
		$out .= '<div class="card-specialty">' . $count . ' من الأطباء والخدمات</div>';
		$out .= '</div></div>';
	}
	return $out . '</div>';
} );

// جدول أوقات العمل.
add_shortcode( 'clinic_hours', function () {
	$s   = clinic_get_settings();
	$out = '<table class="clinic-hours-table"><tbody>';
	foreach ( clinic_days() as $key => $label ) {
		$day  = $s['hours'][ $key ] ?? array();
		$out .= '<tr><th>' . esc_html( $label ) . '</th>';
		if ( ! empty( $day['closed'] ) || empty( $day['from'] ) ) {
			$out .= '<td class="clinic-hours-closed">مغلق</td>';
		} else {
			$out .= '<td dir="ltr" style="text-align:end">' . esc_html( $day['from'] ) . ' – ' . esc_html( $day['to'] ) . '</td>';
		}
		$out .= '</tr>';
	}
	return $out . '</tbody></table>';
} );

// بيانات التواصل: [clinic_contact field="phone|whatsapp|email|address|map"].
add_shortcode( 'clinic_contact', function ( $atts ) {
	$atts = shortcode_atts( array( 'field' => 'phone' ), $atts );
	$s    = clinic_get_settings();
	switch ( $atts['field'] ) {
		case 'map':
			if ( empty( $s['map_embed'] ) ) {
				return '';
			}
			return '<iframe src="' . esc_url( $s['map_embed'] ) . '" style="width:100%;height:420px;border:0;border-radius:8px;" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>';
		case 'whatsapp':
			return $s['whatsapp'] ? '<a dir="ltr" href="https://wa.me/' . esc_attr( $s['whatsapp'] ) . '">' . esc_html( '+' . $s['whatsapp'] ) . '</a>' : '';
		case 'email':
			return $s['email'] ? '<a dir="ltr" href="mailto:' . esc_attr( $s['email'] ) . '">' . esc_html( $s['email'] ) . '</a>' : '';
		case 'phone':
			return $s['phone'] ? '<a dir="ltr" href="tel:' . esc_attr( preg_replace( '/\s+/', '', $s['phone'] ) ) . '">' . esc_html( $s['phone'] ) . '</a>' : '';
		default:
			return esc_html( $s[ $atts['field'] ] ?? '' );
	}
} );
