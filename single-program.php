<!-- for individual program -->
<?php
get_header();

while (have_posts()) {
  the_post();
  pageBanner();
?>
  <div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
      <p>
        <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('program'); ?>"><i class="fa fa-home" aria-hidden="true"></i> All Programs</a>
        <span class="metabox__main"><?php the_title(); ?></span>
      </p>
    </div>
    <div class="generic-content">
      <?php the_content(); ?>

      <!-- professor section -->
      <?php
      $relatedProfessor = new WP_Query(array(
        'posts_per_page' => -1,
        'post_type' => 'professor',
        'orderby' => 'title',
        'order' => 'ASC',
        'meta_query' => array(
          array(
            'key' => 'related_programs',
            'compare' => 'LIKE',
            'value' => '"' . get_the_ID() . '"'
          )
        )
      ));
      if ($relatedProfessor->have_posts()) { ?>
        <hr class="section-break">
        <h2 class="headline headline--medium"><?php echo get_the_title(); ?> Professor</h2>
        <ul class="professor-cards">
          <?php
          while ($relatedProfessor->have_posts()) {
            $relatedProfessor->the_post();
          ?>
            <li class="professor-card__list-item">

              <a class="professor-card" href="<?php the_permalink(); ?>">
                <img class="professor-card__image" src="<?php the_post_thumbnail_url('professorLandscrape'); ?>" alt="">
                <span class="professor-card__name"><?php the_title(); ?></span>
              </a>

            </li>
        </ul>
      <?php
          }
        }
        wp_reset_postdata();


        // end of professor section


        // for fetching related event of an individual program

        $today = date('Ymd');
        $homePageEvents = new WP_Query(array(
          'posts_per_page' => 2,
          'post_type' => 'event',
          'meta_key' => 'event_date',
          'orderby' => 'meta_value_num',
          'order' => 'ASC',
          'meta_query' => array(
            array(
              'key' => 'event_date',
              'compare' => '>=',
              'value' => $today,
              'type' => 'numeric'
            ),
            array(
              'key' => 'related_programs',
              'compare' => 'LIKE',
              'value' => '"' . get_the_ID() . '"'
            )
          )
        ));
        if ($homePageEvents->have_posts()) { ?>
      <hr class="section-break">
      <h2 class="headline headline--medium">Upcoming <?php echo get_the_title(); ?> Event</h2>

    <?php
          while ($homePageEvents->have_posts()) {
            $homePageEvents->the_post();
            get_template_part('template-parts/content', 'event');
          }
        }
    ?>
    </div>
  </div>

<?php }

get_footer();

?>