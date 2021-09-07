<?php
global $wpdb;
$liveposts = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title
FROM $wpdb->posts WHERE post_status = %d ", 'publish' ) );
foreach ( $liveposts as $livepost ) {
echo '<p>' .$livepost->post_title. '</p>';
}





var_dump( $wpdb->col_info ); // column information for the most recent query


