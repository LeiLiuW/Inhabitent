<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * @package RED_Starter_Theme
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function red_starter_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}
add_filter( 'body_class', 'red_starter_body_classes' );

// Remove "Editor" links from sub-menus
function inhabitent_remove_submenus() {
    remove_submenu_page( 'themes.php', 'theme-editor.php' );
    remove_submenu_page( 'plugins.php', 'plugin-editor.php' );
}
add_action( 'admin_menu', 'inhabitent_remove_submenus', 110 );

// adjusting archive page loop for Product

function inhabitent_modify_product_archive($query){
	if(is_post_type_archive('product') && !is_admin() && $query->is_main_query()){
		$query->set('posts_per_page',16);
		$query->set('order','ASC');
		$query->set('orderby','title');
	}
}
add_action('pre_get_posts','inhabitent_modify_product_archive');


// closed comment, delete
// function tent_comments_ajax_handler(){
// 	check_ajax_referer('tent_comments_status','security');
// 	//only allowed aurthor to close the comment
// 	if (!current_user_can('edit_others_post')){
// 		exit;
// 	}
// 	$id = $_POST('the_post_id');
// 	if (isset($id) && is_numeric($id)){
//     $the_post =array (
// 			'id'=>$id,
// 			'comments_status'=>'open'
// 		);
// 	wp_update_post($the_post);
// 	}
//
// }
// add_action('wp_ajax_tent_comments_ajax','tent_comments_ajax_handler');
//delete

function inhabitent_login_header_url() {
    return home_url();
		//get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'inhabitent_login_header_url' );

function inhabitent_login_header_url_title() {
    return 'Inhabitent Ltd.';
}
add_filter( 'login_headertitle', 'inhabitent_login_header_url_title' );

function inhabitent_login_logo() {?>
    <style type="text/css">
      .login h1  a {
				background-image:url(<?php echo get_template_directory_uri();?>/Images/inhabitent-logo-text-dark.svg);
        height: 53px;
				width:300px;
				padding-bottom: 30px;
				background-size:  300px 53px;
			}
    </style>
		<?php
	}
add_filter('login_head', 'inhabitent_login_logo');


function inhabitent_inline_styles_method() {
	if(!is_page_template('about.php')){
		return;
	}
	$imageUrl() -> CFS()->get(about_banner_image);
	if(!$imageUrl){
		return;
	}

  $custom_css = "
        .about{
                background: url({$imageUrl});
        }";
    wp_add_inline_style( 'inhabitent-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'inhabitent_inline_styles_method' );


/**
 * Customize excerpt length and style.
 *
 * @param  string The raw post content.
 * @return string
 */
function red_wp_trim_excerpt( $text ) {
	$raw_excerpt = $text;

	if ( '' == $text ) {
		// retrieve the post content
		$text = get_the_content('');

		// delete all shortcode tags from the content
		$text = strip_shortcodes( $text );

		$text = apply_filters( 'the_content', $text );
		$text = str_replace( ']]>', ']]&gt;', $text );

		// indicate allowable tags
		$allowed_tags = '<p>,<a>,<em>,<strong>,<blockquote>,<cite>';
		$text = strip_tags( $text, $allowed_tags );

		// change to desired word count
		$excerpt_word_count = 50;
		$excerpt_length = apply_filters( 'excerpt_length', $excerpt_word_count );

		// create a custom "more" link
		$excerpt_end = '<span>[...]</span><p><a href="' . get_permalink() . '" class="read-more">Read more &rarr;</a></p>'; // modify excerpt ending
		$excerpt_more = apply_filters( 'excerpt_more', ' ' . $excerpt_end );

		// add the elipsis and link to the end if the word count is longer than the excerpt
		$words = preg_split( "/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );

		if ( count( $words ) > $excerpt_length ) {
			array_pop( $words );
			$text = implode( ' ', $words );
			$text = $text . $excerpt_more;
		} else {
			$text = implode( ' ', $words );
		}
	}

	return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );
}

remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
add_filter( 'get_the_excerpt', 'red_wp_trim_excerpt' );
