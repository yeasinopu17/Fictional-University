<?php
add_action('rest_api_init', 'universityLikeRoutes');

function universityLikeRoutes() {
    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'POST',
        'callback' => 'createLike'
    ));

    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'DELETE',
        'callback' => 'deleteLike'
    ));
}

function createLike($data) {

    if (is_user_logged_in()) {

        $professorId = sanitize_text_field($data['professorId']);

        $existQuery = new WP_Query(array(
            'author' => get_current_user_id(),
            'post_type' => 'like',
            'meta_query' => array(
                array(
                    'key' => 'liked_professor_id',
                    'compare' => '=',
                    'value' => $professorId
                )
            )
        ));

        if ($existQuery->found_posts == 0 and get_post_type($professorId) == 'professor') {

            return wp_insert_post(array(
                'post_type' => 'like',
                'post_status' => 'publish',
                'post_title' => 'Our php create post title',
                'meta_input' => array(
                    'liked_professor_id' => $professorId
                )
            ));
        } else {
            die('You can like a professor once');
        }
    } else {

        die('Only logged in  user can create like');
    }
}


function deleteLike($data) {
    $likeId = sanitize_text_field($data['like']);

    if (get_current_user_id() == get_post_field('post_author', $likeId) and get_post_type($likeId) == 'like') {
        
        wp_delete_post($likeId, true);
        return 'Like deleted :' . $likeId;

    } else {
        
        wp_die('You Do not have permission to delete.', 'Insufficient Privillige');
    }
}
