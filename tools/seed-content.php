<?php
/**
 * محتوى تجريبي: تخصصات، أطباء، خدمات، شركات تأمين، أخبار، تنبيه، إعدادات المستوصف.
 * الصور placeholders مولّدة بمكتبة GD — تُستبدل لاحقًا بالصور الحقيقية.
 */

// ---------- توليد صور Placeholder ----------

function clinic_placeholder_image( $kind, $w = 800, $h = 600 ) {
	$img = imagecreatetruecolor( $w, $h );
	$navy  = imagecolorallocate( $img, 31, 43, 108 );
	$blue  = imagecolorallocate( $img, 21, 158, 236 );
	$light = imagecolorallocate( $img, 232, 241, 255 );
	$white = imagecolorallocate( $img, 255, 255, 255 );

	if ( 'doctor' === $kind ) {
		imagefilledrectangle( $img, 0, 0, $w, $h, $light );
		// صورة شخصية رمزية: رأس + كتفان.
		imagefilledellipse( $img, (int) ( $w / 2 ), (int) ( $h * 0.38 ), (int) ( $w * 0.3 ), (int) ( $w * 0.3 ), $navy );
		imagefilledarc( $img, (int) ( $w / 2 ), (int) ( $h * 1.05 ), (int) ( $w * 0.75 ), (int) ( $h * 0.9 ), 180, 360, $navy, IMG_ARC_PIE );
	} elseif ( 'service' === $kind ) {
		imagefilledrectangle( $img, 0, 0, $w, $h, $blue );
		// صليب طبي أبيض في المنتصف.
		$cw = (int) ( $w * 0.08 );
		$cx = (int) ( $w / 2 );
		$cy = (int) ( $h / 2 );
		$len = (int) ( $h * 0.22 );
		imagefilledrectangle( $img, $cx - $cw, $cy - $len, $cx + $cw, $cy + $len, $white );
		imagefilledrectangle( $img, $cx - $len, $cy - $cw, $cx + $len, $cy + $cw, $white );
	} else { // insurance
		imagefilledrectangle( $img, 0, 0, $w, $h, $white );
		imagefilledrectangle( $img, (int) ( $w * 0.15 ), (int) ( $h * 0.3 ), (int) ( $w * 0.85 ), (int) ( $h * 0.7 ), $light );
		imagefilledellipse( $img, (int) ( $w * 0.3 ), (int) ( $h / 2 ), (int) ( $h * 0.28 ), (int) ( $h * 0.28 ), $navy );
		imagefilledrectangle( $img, (int) ( $w * 0.42 ), (int) ( $h * 0.44 ), (int) ( $w * 0.75 ), (int) ( $h * 0.49 ), $blue );
		imagefilledrectangle( $img, (int) ( $w * 0.42 ), (int) ( $h * 0.53 ), (int) ( $w * 0.65 ), (int) ( $h * 0.57 ), $blue );
	}

	$upload = wp_upload_dir();
	$file   = trailingslashit( $upload['path'] ) . uniqid( "placeholder-$kind-" ) . '.png';
	imagepng( $img, $file );
	imagedestroy( $img );

	$attachment_id = wp_insert_attachment( array(
		'post_mime_type' => 'image/png',
		'post_title'     => "صورة تجريبية - $kind",
		'post_status'    => 'inherit',
	), $file );
	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $file ) );
	return $attachment_id;
}

function clinic_seed_post( $type, $title, $content, $excerpt, $kind, $meta = array(), $terms = array() ) {
	$existing = get_page_by_path( sanitize_title( $title ), OBJECT, $type );
	if ( $existing ) {
		echo "  skip (exists): $title\n";
		return $existing->ID;
	}
	$id = wp_insert_post( array(
		'post_type'    => $type,
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_content' => $content,
		'post_excerpt' => $excerpt,
	) );
	if ( $kind ) {
		set_post_thumbnail( $id, clinic_placeholder_image( $kind, 'doctor' === $kind ? 600 : 800, 600 ) );
	}
	foreach ( $meta as $k => $v ) {
		update_post_meta( $id, $k, $v );
	}
	if ( $terms ) {
		wp_set_object_terms( $id, $terms, 'clinic_specialty' );
	}
	echo "  created: $title ($id)\n";
	return $id;
}

// ---------- التخصصات ----------
echo "specialties:\n";
$specialties = array(
	'عيادة الأسرة والطب العام' => 'الكشف العام ومتابعة الأمراض المزمنة لجميع الأعمار',
	'الأسنان'                  => 'علاج وتجميل الأسنان والحشوات والتركيبات',
	'النساء والولادة'          => 'متابعة الحمل وصحة المرأة بطاقم نسائي كامل',
	'الأطفال'                  => 'صحة الطفل والتطعيمات ومتابعة النمو',
	'الباطنية'                 => 'أمراض الجهاز الهضمي والصدر والسكري والضغط',
	'الجلدية'                  => 'الأمراض الجلدية والعناية بالبشرة',
);
foreach ( $specialties as $name => $desc ) {
	if ( ! term_exists( $name, 'clinic_specialty' ) ) {
		wp_insert_term( $name, 'clinic_specialty', array( 'description' => $desc ) );
		echo "  created: $name\n";
	}
}

// ---------- الأطباء ----------
echo "doctors:\n";
$doctors = array(
	array( 'د. محمد العتيبي', 'عيادة الأسرة والطب العام', 'استشاري طب الأسرة', 'البورد السعودي لطب الأسرة', 'السبت – الخميس، 9ص – 9م', 'العربية، الإنجليزية' ),
	array( 'د. سارة الحربي', 'النساء والولادة', 'استشارية نساء وولادة', 'البورد العربي لأمراض النساء والولادة', 'الأحد – الأربعاء، 4م – 9م', 'العربية، الإنجليزية' ),
	array( 'د. أحمد الشمري', 'الأسنان', 'أخصائي طب وجراحة الأسنان', 'ماجستير طب الأسنان — جامعة الملك سعود', 'السبت – الخميس، 1م – 9م', 'العربية، الإنجليزية' ),
	array( 'د. نورة القحطاني', 'الأطفال', 'أخصائية طب الأطفال', 'البورد السعودي لطب الأطفال', 'السبت – الأربعاء، 9ص – 4م', 'العربية' ),
	array( 'د. خالد المطيري', 'الباطنية', 'استشاري الأمراض الباطنية', 'الزمالة الكندية للأمراض الباطنية', 'الأحد – الخميس، 5م – 10م', 'العربية، الإنجليزية' ),
	array( 'د. ريم الدوسري', 'الجلدية', 'أخصائية الأمراض الجلدية', 'ماجستير الأمراض الجلدية', 'الاثنين – الخميس، 4م – 9م', 'العربية، الإنجليزية' ),
);
foreach ( $doctors as $d ) {
	clinic_seed_post(
		'clinic_doctor',
		$d[0],
		'<p>' . $d[2] . ' بخبرة واسعة في مجاله، يستقبل المرضى في ' . $d[1] . '.</p>',
		'',
		'doctor',
		array(
			'position'       => $d[2],
			'qualifications' => $d[3],
			'schedule'       => $d[4],
			'languages'      => $d[5],
		),
		array( $d[1] )
	);
}

// ---------- الخدمات ----------
echo "services:\n";
$services = array(
	array( 'الفحص الطبي الشامل', 'فحوصات مخبرية وسريرية شاملة مع تقرير مفصل عن حالتك الصحية.', 'عيادة الأسرة والطب العام' ),
	array( 'تنظيف وتلميع الأسنان', 'إزالة الجير والتصبغات وتلميع الأسنان بأحدث الأجهزة.', 'الأسنان' ),
	array( 'متابعة الحمل', 'متابعة دورية للحمل مع فحص السونار بطاقم نسائي كامل.', 'النساء والولادة' ),
	array( 'تطعيمات الأطفال', 'جميع التطعيمات الأساسية والموسمية حسب جدول وزارة الصحة.', 'الأطفال' ),
	array( 'المختبر الطبي', 'تحاليل شاملة بنتائج سريعة ودقيقة خلال نفس اليوم.', 'عيادة الأسرة والطب العام' ),
	array( 'علاج مشاكل البشرة', 'تشخيص وعلاج حب الشباب والتصبغات والأكزيما.', 'الجلدية' ),
);
foreach ( $services as $s ) {
	clinic_seed_post( 'clinic_service', $s[0], '<p>' . $s[1] . '</p>', $s[1], 'service', array(), array( $s[2] ) );
}

// ---------- شركات التأمين ----------
echo "insurance:\n";
foreach ( array( 'بوبا العربية', 'التعاونية للتأمين', 'ميدغلف', 'ملاذ للتأمين', 'الراجحي تكافل' ) as $ins ) {
	clinic_seed_post( 'clinic_insurance', $ins, '', '', 'insurance', array( 'notes' => 'جميع الفئات' ) );
}

// ---------- الأخبار ----------
echo "news:\n";
$news = array(
	array( 'افتتاح عيادة الجلدية بأحدث الأجهزة', 'يسرنا الإعلان عن افتتاح عيادة الجلدية والتجميل بأحدث أجهزة الليزر والعناية بالبشرة، استقبال الحجوزات ابتداءً من الأسبوع القادم.' ),
	array( 'حملة التطعيم ضد الإنفلونزا الموسمية', 'انطلقت حملة التطعيم السنوية ضد الإنفلونزا الموسمية لجميع الأعمار. التطعيم متوفر يوميًا من 9 صباحًا حتى 9 مساءً دون حاجة لحجز مسبق.' ),
	array( 'عروض الفحص الشامل بمناسبة اليوم الوطني', 'بمناسبة اليوم الوطني، خصم خاص على باقات الفحص الشامل للرجال والنساء. العرض ساري حتى نهاية الشهر.' ),
);
foreach ( $news as $n ) {
	if ( ! get_page_by_path( sanitize_title( $n[0] ), OBJECT, 'post' ) ) {
		$nid = wp_insert_post( array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_title'   => $n[0],
			'post_content' => '<p>' . $n[1] . '</p>',
			'post_excerpt' => $n[1],
		) );
		set_post_thumbnail( $nid, clinic_placeholder_image( 'service' ) );
		echo "  created: {$n[0]} ($nid)\n";
	}
}

// ---------- تنبيه ----------
echo "announcement:\n";
if ( ! get_page_by_path( sanitize_title( 'حملة تطعيم الإنفلونزا متوفرة الآن — بدون موعد مسبق' ), OBJECT, 'clinic_announcement' ) ) {
	$aid = wp_insert_post( array(
		'post_type'   => 'clinic_announcement',
		'post_status' => 'publish',
		'post_title'  => 'حملة تطعيم الإنفلونزا متوفرة الآن — بدون موعد مسبق',
	) );
	update_post_meta( $aid, 'active', 1 );
	echo "  created ($aid)\n";
}

// ---------- إعدادات المستوصف ----------
$hours = array();
foreach ( array( 'sat', 'sun', 'mon', 'tue', 'wed', 'thu' ) as $day ) {
	$hours[ $day ] = array( 'closed' => false, 'from' => '09:00', 'to' => '21:00' );
}
$hours['fri'] = array( 'closed' => true, 'from' => '', 'to' => '' );
update_option( 'clinic_settings', array(
	'phone'     => '011 123 4567',
	'whatsapp'  => '966501234567',
	'email'     => 'info@clinic.example.com',
	'address'   => 'الرياض — حي النسيم، طريق الأمير محمد بن سلمان',
	'map_embed' => 'https://maps.google.com/maps?q=Riyadh&z=13&output=embed',
	'hours'     => $hours,
) );
echo "settings saved\n";
