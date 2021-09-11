<!-- for individual professor -->
<?php
get_header();

while (have_posts()) {
  the_post();
  pageBanner();
?>


  <div class="container container--narrow page-section">
    <div class="generic-content">
      <div class="row-group">
        <div class="one-third">
          <?php the_post_thumbnail('professorPortrait'); ?>
        </div>
        <div class="two-thirds">
          <?php
          $likeCount = new WP_Query(array(
            'post_type' => 'like',
            'meta_query' => array(
              array(
                'key' => 'liked_professor_id',
                'compare' => '=',
                'value' => get_the_ID()
              )
            )
          ));

          $existStatus = 'no';

          if (is_user_logged_in()) {
            
            $existQuery = new WP_Query(array(
              'author' => get_current_user_id(),
              'post_type' => 'like',
              'meta_query' => array(
                array(
                  'key' => 'liked_professor_id',
                  'compare' => '=',
                  'value' => get_the_ID()
                )
              )
            ));

            if ($existQuery->found_posts) {
              $existStatus = 'yes';
            }

          }

          //  var_dump($existQuery->posts);

          ?>
          <!-- heart section -->
          <span class="like-box" data-like="<?php  echo $existQuery->posts[0]->ID; ?>" data-id="<?php echo the_ID(); ?>" data-exists="<?php echo $existStatus; ?>">
            <i class="fa fa-heart-o" aria-hidden="true"></i>
            <i class="fa fa-heart" aria-hidden="true"></i>
            <span class="like-count"><?php echo $likeCount->found_posts; ?></span>
          </span>
          <!--End heart section -->

          <?php the_content(); ?>
        </div>
      </div>
    </div>
    <hr class="section-break">
    <h2 class="headline headline--medium">Subject(s) Taught</h2>
    <?php
    $relatedPrograms = get_field('related_programs');
    // print_r( $relatedPrograms);
    if ($relatedPrograms) {
      echo '<ul class="link-list min-list">';
      foreach ($relatedPrograms as $program) { ?>
        <li><a href="<?php echo get_the_permalink($program) ?>"><?php echo get_the_title($program); ?></a></li>

    <?php }
    }
    echo '</ul>'
    ?>

  </div>

<?php }

get_footer();

?>