<?php
function university_post_types() {
  //campus post type
  register_post_type('campus', array(
    'capability_type' => 'campus', // def post
    'map_meta_cap' => true,
    'has_archive' => true,
    'public' => true,
    'labels' => array(
      'name' => 'Campuses',
      'add_new_item' => 'Add New Campus',
      'edit_item' => 'Edit Campus',
      'all_items' => 'All Campuses',
      'singular_name' => 'Campus'
    ),
    'menu_icon' => 'dashicons-location-alt',
    'rewrite' => array(
      'slug' => 'campuses'
    ),
    'supports' => array('title', 'editor', 'excerpt'),
    'show_in_rest' => true
  ));

  //Event post type
  register_post_type('event', array(
    'capability_type' => 'event', // def post
    'map_meta_cap' => true,
    'has_archive' => true,
    'public' => true,
    'labels' => array(
      'name' => 'Events',
      'add_new_item' => 'Add New Event',
      'edit_item' => 'Edit Event',
      'all_items' => 'All Events',
      'singular_name' => 'Event'
    ),
    'menu_icon' => 'dashicons-calendar-alt',
    'rewrite' => array(
      'slug' => 'events'
    ),
    'supports' => array('title', 'editor', 'excerpt'),
    'show_in_rest' => true
  ));

  // Program Post type
  register_post_type('program', array(
    'has_archive' => true,
    'public' => true,
    'labels' => array(
      'name' => 'Programs',
      'add_new_item' => 'Add New Program',
      'edit_item' => 'Edit Program',
      'all_items' => 'All Programs',
      'singular_name' => 'Program'
    ),
    'menu_icon' => 'dashicons-awards',
    'rewrite' => array('slug' => 'programs'),
    'supports' => array('title'),
    'show_in_rest' => true
  ));

  // Professor Post type
  register_post_type('professor', array(
    'show_in_rest' => true,
    'supports' => array('title', 'editor', 'thumbnail'),
    'public' => true,
    'labels' => array(
      'name' => 'Professors',
      'add_new_item' => 'Add New Professor',
      'edit_item' => 'Edit Professor',
      'all_items' => 'All Professors',
      'singular_name' => 'Professor'
    ),
    'menu_icon' => 'dashicons-welcome-learn-more'
  ));

  // Notes Post type
  register_post_type('note', array(
    'capability_type' => 'note',
    'map_meta_cap' => true,
    'show_in_rest' => true,
    'supports' => array('title', 'editor'),
    'public' => false, //PRIVATE FOR EACH USER
    'show_ui' => true,
    'labels' => array(
      'name' => 'Notes',
      'add_new_item' => 'Add New Note',
      'edit_item' => 'Edit Note',
      'all_items' => 'All Notes',
      'singular_name' => 'Note'
    ),
    'menu_icon' => 'dashicons-welcome-write-blog'
  ));

  // like Post type
  register_post_type('like', array(
    'supports' => array('title'),
    'public' => false, //PRIVATE FOR EACH USER
    'show_ui' => true,
    'labels' => array(
      'name' => 'Likes',
      'add_new_item' => 'Add New Like',
      'edit_item' => 'Edit Like',
      'all_items' => 'All Likes',
      'singular_name' => 'Like'
    ),
    'menu_icon' => 'dashicons-heart'
  ));
}

add_action('init', 'university_post_types');


