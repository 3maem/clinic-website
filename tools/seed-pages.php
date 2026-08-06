<?php
/**
 * إنشاء صفحات الموقع + القائمة الرئيسية.
 * يعمل عبر: wp eval-file tools/seed-pages.php
 */

function clinic_make_page( $slug, $title, $content, $template = '' ) {
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		wp_update_post( array( 'ID' => $existing->ID, 'post_title' => $title, 'post_content' => $content ) );
		return $existing->ID;
	}
	$id = wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_name'    => $slug,
		'post_title'   => $title,
		'post_content' => $content,
	) );
	if ( $template ) {
		update_post_meta( $id, '_wp_page_template', $template );
	}
	return $id;
}

$hero = <<<'HTML'
<!-- wp:cover {"dimRatio":80,"overlayColor":"clinic-navy","minHeight":520,"align":"full"} -->
<div class="wp-block-cover alignfull" style="min-height:520px"><span aria-hidden="true" class="wp-block-cover__background has-clinic-navy-background-color has-background-dim-80 has-background-dim"></span><div class="wp-block-cover__inner-container">
<!-- wp:paragraph {"align":"center","textColor":"clinic-light"} -->
<p class="has-text-align-center has-clinic-light-color has-text-color">نهتم بصحتك وصحة عائلتك</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"textAlign":"center","level":1,"textColor":"white","fontSize":"xx-large"} -->
<h1 class="wp-block-heading has-text-align-center has-white-color has-text-color has-xx-large-font-size">رعاية طبية متكاملة بجودة عالية</h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","textColor":"white"} -->
<p class="has-text-align-center has-white-color has-text-color">نخبة من الأطباء والاستشاريين في مختلف التخصصات، وأحدث التجهيزات الطبية لخدمتك على مدار الأسبوع.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"clinic-blue","textColor":"white"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-white-color has-clinic-blue-background-color has-text-color has-background wp-element-button" href="/book-appointment/">احجز موعدًا الآن</a></div>
<!-- /wp:button -->
<!-- wp:button {"backgroundColor":"clinic-light","textColor":"clinic-navy"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-clinic-navy-color has-clinic-light-background-color has-text-color has-background wp-element-button" href="/services/">تعرّف على خدماتنا</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
</div></div>
<!-- /wp:cover -->
HTML;

$home = $hero . <<<'HTML'

<!-- wp:group {"style":{"spacing":{"padding":{"top":"48px","bottom":"24px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:48px;padding-bottom:24px">
<!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">خدماتنا الطبية</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">نقدم مجموعة واسعة من الخدمات الطبية لجميع أفراد العائلة</p>
<!-- /wp:paragraph -->
<!-- wp:shortcode -->
[clinic_services limit="6"]
<!-- /wp:shortcode -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/services/">جميع الخدمات</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->

<!-- wp:group {"backgroundColor":"clinic-bg","style":{"spacing":{"padding":{"top":"48px","bottom":"48px"}}},"align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-clinic-bg-background-color has-background" style="padding-top:48px;padding-bottom:48px">
<!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">أطباؤنا</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">فريق طبي متميز من الاستشاريين والأخصائيين</p>
<!-- /wp:paragraph -->
<!-- wp:shortcode -->
[clinic_doctors limit="4"]
<!-- /wp:shortcode -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/doctors/">جميع الأطباء</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"48px","bottom":"24px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:48px;padding-bottom:24px">
<!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">آخر الأخبار</h2>
<!-- /wp:heading -->
<!-- wp:latest-posts {"postsToShow":3,"displayPostDate":true,"postLayout":"grid","columns":3,"displayFeaturedImage":true,"featuredImageSizeSlug":"medium_large"} /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"24px","bottom":"48px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="padding-top:24px;padding-bottom:48px">
<!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">شركات التأمين المعتمدة</h2>
<!-- /wp:heading -->
<!-- wp:shortcode -->
[clinic_insurance]
<!-- /wp:shortcode -->
</div>
<!-- /wp:group -->

<!-- wp:group {"backgroundColor":"clinic-navy","style":{"spacing":{"padding":{"top":"40px","bottom":"40px"}}},"align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-clinic-navy-background-color has-background" style="padding-top:40px;padding-bottom:40px">
<!-- wp:heading {"textAlign":"center","textColor":"white"} -->
<h2 class="wp-block-heading has-text-align-center has-white-color has-text-color">هل لديك استفسار؟ نسعد بخدمتك</h2>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","textColor":"clinic-light"} -->
<p class="has-text-align-center has-clinic-light-color has-text-color">اتصل بنا: [clinic_contact field="phone"] — أو راسلنا عبر واتساب</p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"backgroundColor":"clinic-blue","textColor":"white"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-white-color has-clinic-blue-background-color has-text-color has-background wp-element-button" href="/contact/">اتصل بنا</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
HTML;

$about = <<<'HTML'
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">عن المستوصف</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>مستوصف طبي يقدم رعاية صحية شاملة لجميع أفراد العائلة منذ تأسيسه، عبر فريق من الأطباء الاستشاريين والأخصائيين وبأحدث التجهيزات الطبية. نلتزم بمعايير الجودة وسلامة المرضى في جميع خدماتنا.</p>
<!-- /wp:paragraph -->
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"textColor":"clinic-blue"} -->
<h3 class="wp-block-heading has-clinic-blue-color has-text-color">رؤيتنا</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>أن نكون الخيار الأول للرعاية الصحية الأولية في منطقتنا.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"textColor":"clinic-blue"} -->
<h3 class="wp-block-heading has-clinic-blue-color has-text-color">رسالتنا</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>تقديم خدمات طبية آمنة وعالية الجودة بأسعار مناسبة وتعامل إنساني.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:heading {"level":3,"textColor":"clinic-blue"} -->
<h3 class="wp-block-heading has-clinic-blue-color has-text-color">قيمنا</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>الإتقان، الأمانة، الرحمة، والاحترام في كل تفاصيل عملنا.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- wp:heading -->
<h2 class="wp-block-heading">أوقات العمل</h2>
<!-- /wp:heading -->
<!-- wp:shortcode -->
[clinic_hours]
<!-- /wp:shortcode -->
HTML;

$departments = <<<'HTML'
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">الأقسام والتخصصات</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>تضم عيادات المستوصف مجموعة من التخصصات الطبية لخدمة جميع أفراد العائلة.</p>
<!-- /wp:paragraph -->
<!-- wp:shortcode -->
[clinic_specialties]
<!-- /wp:shortcode -->
HTML;

$services = <<<'HTML'
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">الخدمات الطبية</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>نقدم مجموعة متكاملة من الخدمات الطبية والتشخيصية.</p>
<!-- /wp:paragraph -->
<!-- wp:shortcode -->
[clinic_services]
<!-- /wp:shortcode -->
HTML;

$doctors = <<<'HTML'
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">أطباؤنا</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>تعرّف على فريقنا الطبي من الاستشاريين والأخصائيين.</p>
<!-- /wp:paragraph -->
<!-- wp:shortcode -->
[clinic_doctors]
<!-- /wp:shortcode -->
HTML;

$insurance = <<<'HTML'
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">التأمينات المعتمدة</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>نتعامل مع شركات التأمين التالية. إذا لم تجد شركتك أو أردت التأكد من تغطية فئتك، تواصل معنا على [clinic_contact field="phone"].</p>
<!-- /wp:paragraph -->
<!-- wp:shortcode -->
[clinic_insurance]
<!-- /wp:shortcode -->
HTML;

$faq = <<<'HTML'
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">الأسئلة الشائعة</h1>
<!-- /wp:heading -->
<!-- wp:details -->
<details class="wp-block-details"><summary>هل أحتاج إلى حجز موعد مسبق؟</summary><!-- wp:paragraph -->
<p>ننصح بالحجز المسبق لضمان موعدك، ويمكن استقبال الحالات المستعجلة مباشرة حسب التوفر.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<!-- wp:details -->
<details class="wp-block-details"><summary>هل تقبلون التأمين الطبي؟</summary><!-- wp:paragraph -->
<p>نعم، نتعامل مع عدد من شركات التأمين المعتمدة. راجع صفحة «التأمينات المعتمدة» أو اتصل بنا للتأكد من تغطية بطاقتك.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<!-- wp:details -->
<details class="wp-block-details"><summary>ما هي أوقات العمل؟</summary><!-- wp:paragraph -->
<p>تجد أوقات العمل المحدثة في صفحة «اتصل بنا» وأسفل الموقع.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<!-- wp:details -->
<details class="wp-block-details"><summary>هل تتوفر عيادة نسائية بطاقم نسائي؟</summary><!-- wp:paragraph -->
<p>نعم، تتوفر عيادة نساء وولادة بطاقم طبي نسائي كامل.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
<!-- wp:details -->
<details class="wp-block-details"><summary>كيف أحصل على تقرير طبي أو إجازة مرضية؟</summary><!-- wp:paragraph -->
<p>تصدر التقارير والإجازات المرضية من الاستقبال بعد معاينة الطبيب، وتُرسل الإجازات إلكترونيًا عبر المنصات المعتمدة.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->
HTML;

$contact = <<<'HTML'
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">اتصل بنا</h1>
<!-- /wp:heading -->
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">بيانات التواصل</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>📞 الهاتف: [clinic_contact field="phone"]<br>💬 واتساب: [clinic_contact field="whatsapp"]<br>✉️ البريد: [clinic_contact field="email"]<br>📍 العنوان: [clinic_contact field="address"]</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">أوقات العمل</h3>
<!-- /wp:heading -->
<!-- wp:shortcode -->
[clinic_hours]
<!-- /wp:shortcode --></div>
<!-- /wp:column -->
<!-- wp:column {"width":"60%"} -->
<div class="wp-block-column" style="flex-basis:60%"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">موقعنا على الخريطة</h3>
<!-- /wp:heading -->
<!-- wp:shortcode -->
[clinic_contact field="map"]
<!-- /wp:shortcode --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
HTML;

$booking = <<<'HTML'
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">احجز موعدًا</h1>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>عبّئ النموذج التالي وسيتواصل معك فريق الاستقبال لتأكيد الموعد، أو تواصل مباشرة عبر واتساب من الزر أسفل الشاشة.</p>
<!-- /wp:paragraph -->
CLINIC_FORM_PLACEHOLDER
HTML;

$form_id = (int) get_option( 'clinic_booking_form_id' );
if ( $form_id ) {
	$booking = str_replace( 'CLINIC_FORM_PLACEHOLDER', "<!-- wp:shortcode -->\n[fluentform id=\"$form_id\"]\n<!-- /wp:shortcode -->", $booking );
} else {
	$booking = str_replace( 'CLINIC_FORM_PLACEHOLDER', '<!-- wp:paragraph --><p>للحجز اتصل بنا على [clinic_contact field="phone"]</p><!-- /wp:paragraph -->', $booking );
}

$ids                = array();
$ids['home']        = clinic_make_page( 'home', 'الرئيسية', $home );
$ids['about']       = clinic_make_page( 'about', 'عن المستوصف', $about );
$ids['departments'] = clinic_make_page( 'departments', 'الأقسام والتخصصات', $departments );
$ids['services']    = clinic_make_page( 'services-page', 'الخدمات الطبية', $services );
$ids['doctors']     = clinic_make_page( 'doctors-page', 'الأطباء', $doctors );
$ids['news']        = clinic_make_page( 'news', 'الأخبار والمقالات', '' );
$ids['insurance']   = clinic_make_page( 'insurance', 'التأمينات المعتمدة', $insurance );
$ids['faq']         = clinic_make_page( 'faq', 'الأسئلة الشائعة', $faq );
$ids['contact']     = clinic_make_page( 'contact', 'اتصل بنا', $contact );
$ids['booking']     = clinic_make_page( 'book-appointment', 'احجز موعدًا', $booking );

// الصفحة الرئيسية وصفحة الأخبار.
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $ids['home'] );
update_option( 'page_for_posts', $ids['news'] );

// القائمة الرئيسية.
$menu_name = 'القائمة الرئيسية';
$menu      = wp_get_nav_menu_object( $menu_name );
if ( ! $menu ) {
	$menu_id = wp_create_nav_menu( $menu_name );
} else {
	$menu_id = $menu->term_id;
	// إفراغ القائمة لإعادة بنائها.
	foreach ( wp_get_nav_menu_items( $menu_id ) as $item ) {
		wp_delete_post( $item->ID, true );
	}
}

$menu_pages = array(
	array( 'الرئيسية', $ids['home'] ),
	array( 'عن المستوصف', $ids['about'] ),
	array( 'الأقسام والتخصصات', $ids['departments'] ),
	array( 'الخدمات الطبية', $ids['services'] ),
	array( 'الأطباء', $ids['doctors'] ),
	array( 'الأخبار', $ids['news'] ),
	array( 'التأمينات', $ids['insurance'] ),
	array( 'الأسئلة الشائعة', $ids['faq'] ),
	array( 'اتصل بنا', $ids['contact'] ),
);
foreach ( $menu_pages as $i => $p ) {
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'     => $p[0],
		'menu-item-object'    => 'page',
		'menu-item-object-id' => $p[1],
		'menu-item-type'      => 'post_type',
		'menu-item-status'    => 'publish',
		'menu-item-position'  => $i + 1,
	) );
}

// ربط القائمة بموقع Astra الرئيسي.
$locations            = get_theme_mod( 'nav_menu_locations', array() );
$locations['primary'] = $menu_id;
set_theme_mod( 'nav_menu_locations', $locations );

echo "pages created:\n";
foreach ( $ids as $k => $v ) {
	echo "  $k => $v\n";
}
echo "menu id: $menu_id\n";
