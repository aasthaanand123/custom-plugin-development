<!-- register custom post type -->
 <?php
function contact_init() {
    register_post_type( 'contact', array(
        'public' => true,
        'labels' => array(
            'name'          => esc_html__('Contact Form'),
            'all_items'     => esc_html__('All Entries'),
            'singular_name' => esc_html__("Entry"),
        ),
        'menu_icon' => "dashicons-smartphone",
        'supports'           => array('title'),  
        'capabilities'       => array(
        'create_posts' => false, 
        'delete_post' => 'delete_posts', 
        ),
    ) );
}
add_action( 'init', 'contact_init' );

// register custom endpoint
add_action("rest_api_init", "register_routes");
function register_routes(){
    register_rest_route( "form-submissions-api", "submit", array(
        'methods'=>"POST",
        "callback"=>"handle_submission_form",
    ) );
    
}

function handle_submission_form($data){
    // handle data
    $headers=$data->get_headers();
    $params=$data->get_json_params();
    
    $nonce=$headers['x_wp_nonce'][0];

    // verify nonce
    if(!wp_verify_nonce($nonce, "wp_rest")){
        return "Message Not Sent!";
    }
    // insert message in the database
    $post_id=wp_insert_post( array(
        'post_type'=>'contact',
    ));
     if($post_id){
        update_post_meta($post_id, 'name', sanitize_text_field($params['name']));
        update_post_meta($post_id, 'email', sanitize_email($params['email']));
        update_post_meta($post_id, 'query', sanitize_textarea_field($params['query']));
        
        return "Thank you for your response!";
     }
}

// get the data to display on the custom post type: contact form page on the dashboard
// Hook to add custom columns
add_filter('manage_contact_posts_columns', 'set_custom_contact_columns');
function set_custom_contact_columns($columns) {
    unset($columns['title']);
    $date=$columns['date'];
    unset($columns['date']);
    // Add custom columns
    $columns['name'] = 'Name';
    $columns['email'] = 'Email';
    $columns['query'] = 'Query/Feedback';
    $columns['date']=$date;
    return $columns;
}

// Hook to fill custom columns with data
add_action('manage_contact_posts_custom_column', 'custom_contact_column', 10, 2);
function custom_contact_column($column, $post_id) {
    switch ($column) {
        case 'name':
            // Get the name from post meta
            $name = get_post_meta($post_id, 'name', true);
            echo $name ? esc_html($name) : 'No Name';
            break;

        case 'email':
            // Get the email from post meta
            $email = get_post_meta($post_id, 'email', true);
            echo $email ? esc_html($email) :'No Email';
            break;

        case 'query':
            // Get the query/feedback from post meta
            $query = get_post_meta($post_id, 'query', true);
            echo $query ? esc_html($query) : 'No Query';
            break;
    }
}
