<?php

require get_theme_file_path('/inc/search-route.php');

function pageBanner($args = null) {

  if (!$args['title']) {
    $args['title'] = get_the_title();
  }
  if (!$args['subtitle']) {
    $args['subtitle'] = get_field('page_banner_subtitle');
  }
  if (!$args['photo']) {
    if (get_field('page_banner_background_image') and !is_archive() and !is_home()) {
      $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
    } else {
      $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
    }
  }
?>
  <div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']; ?>)">
    </div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>
      <div class="page-banner__intro">
        <p><?php echo $args['subtitle']; ?></p>
      </div>
    </div>
  </div>

<?php
}

function university_files() {
  wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

  wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyAjfCmfWT-lTM3sa4BzIsuhm8qVvwrgkjM', NULL, '1.0', true);
  wp_enqueue_script('axios', '//cdn.jsdelivr.net/npm/axios/dist/axios.min.js', NULL, '1.0', true);
  wp_enqueue_script('glidejs', '//cdn.jsdelivr.net/npm/@glidejs/glide', NULL, '1.0', true);

  wp_enqueue_script('main-university-js', get_theme_file_uri('/scripts.js'), array('jquery'), '1.0', true);
  wp_enqueue_style('university_main_styles', get_stylesheet_uri());

  wp_localize_script('main-university-js', 'universityData', array(
    'root_url' => get_site_url(),
    'nonce' => wp_create_nonce('wp_rest')
  ));
}


add_action('wp_enqueue_scripts', 'university_files');


function university_features() {
  // register_nav_menu('headerMenuLocation', 'Header Menu Location');
  // register_nav_menu('footerLocationOne', 'Footer Location One');// create location of menu in admin panel
  // register_nav_menu('footerLocationTwo', 'Footer Location Two');
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails'); // for feature image, this will only work for blog
  add_image_size('professorLandscrape', 400, 260, true);
  add_image_size('professorPortrait', 480, 650, true);
  add_image_size('pageBanner', 1500, 350, true);
}


add_action('after_setup_theme', 'university_features'); // for title appear in every page


function university_adjust_queries($query) {

  // for event query manipulation
  if (!is_admin() and is_post_type_archive('event') and $query->is_main_query()) {
    $today = date('Ymd');
    $query->set('meta_key', 'event_date');
    $query->set('orderby', 'meta_value_num');
    $query->set('order', 'ASC');
    $query->set('meta_query', array(
      array(
        'key' => 'event_date',
        'compare' => '>=',
        'value' => $today,
        'type' => 'numeric'
      )
    ));
  }


  //for program query manipulation
  if (!is_admin() and is_post_type_archive('program') and $query->is_main_query()) {
    $query->set('posts_per_page', -1); //-1 means all will fetch
    $query->set('orderby', 'title');
    $query->set('order', 'ASC');
  }


  //for campuses query manipulation
  // by default campus query fetch 10 row for the first page
  // we want all at the first page
  if (!is_admin() and is_post_type_archive('campus') and $query->is_main_query()) {
    $query->set('posts_per_page', -1); //-1 means all will fetch
  }
}
add_action('pre_get_posts', 'university_adjust_queries');


function universityMapKey($api) {
  $api['key'] = 'AIza';
  return $api;
}

add_filter('acf/fields/google_map/api', 'universityMapKey');

function university_custom_rest() {
  register_rest_field('post', 'authorName', array(
    'get_callback' => function () {
      return get_the_author();
    }
  ));
}
add_action('rest_api_init', 'university_custom_rest');



// log in redirect subscriber acc to home page, instead of dashboard
function redirectSubsToHome() {
  $currentUser = wp_get_current_user();
  if (count($currentUser->roles) == 1 and $currentUser->roles[0] == 'subscriber') {
    wp_redirect(site_url('/'));
    exit;
  }
}

add_action('admin_init', 'redirectSubsToHome');


// delete top bar for subscriber
function noSubsAdminBar() {
  $currentUser = wp_get_current_user();
  if (count($currentUser->roles) == 1 and $currentUser->roles[0] == 'subscriber') {
    show_admin_bar(false);
  }
}

add_action('wp_loaded', 'noSubsAdminBar');


// customize login screen logo link
function ourHeaderUrl() {
  return esc_url(site_url('/'));
}

add_filter('login_headerurl', 'ourHeaderUrl');

//customize login screen css
function ourLoginCSS() {
  wp_enqueue_style('university_main_styles', get_stylesheet_uri());
  wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
}

add_filter('login_enqueue_scripts', 'ourLoginCSS');

// login screen heading
function my_login_logo_url_title() {
  return get_bloginfo('name');
}
add_filter('login_headertitle', 'my_login_logo_url_title');


// force note post to be private
function makeNotePrivate($data, $postarr) {
  var_dump($postarr);
  echo $postarr;

  if ($data['post_type'] == 'note') {
    $data['post_title'] = sanitize_text_field($data['post_title']);
    $data['post_content'] = sanitize_textarea_field($data['post_content']);

    // limit user to create no more than 5 post
    if (count_user_posts(get_current_user_id(), 'note') > 5 and !$postarr['ID']) {
      die('You have reached the note limit');
    }
  }

  if ($data['post_type'] == 'note' and $data['post_type'] != 'trash') {
    $data['post_status'] = 'private';
  }
  return $data;
}

add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);
