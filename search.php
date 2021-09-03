<!-- this now the search.php, it shows search results -->
<?php
get_header();
pageBanner(array(
    'title' => 'Search Result',
    'subtitle' => 'You Searched for "' . get_search_query() . '"'
));
?>


<div class="container container--narrow page-section">
    <?php
    if (have_posts()) {
        while (have_posts()) {
            the_post();
            get_template_part('template-parts/content', get_post_type());
        }
        echo paginate_links();
    } else {
        echo '<h2 class="headline headline--small-plus">No Result match that search.</h2>';
    }

    get_search_form();

    ?>

</div>



<?php
get_footer();
?>