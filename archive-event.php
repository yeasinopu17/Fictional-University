<!-- This is used for all events -->
<?php
get_header();
pageBanner(array(
    'title' => 'All Events',
    'subtitle' => 'See What is going on in our world.'
));
?>

<div class="container container--narrow page-section">
    <?php
    while (have_posts()) {
        the_post();
        get_template_part('template-parts/content','event');
    }
    echo paginate_links();
    ?>
    <hr class="section-break">
    <p>Looking for Past Events <a href="<?php echo site_url('/past-events') ?>">Click Here.</a></p>
</div>



<?php
get_footer();
?>