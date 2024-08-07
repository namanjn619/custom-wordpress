<?php

if (!function_exists('elumine_child_enqueue_styles')) {
    /*
    *   Function to load parent and child theme CSS
    *
    */
    function elumine_child_enqueue_styles() {
        wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
        wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css' );
        wp_enqueue_style( 'custom-child-style', get_stylesheet_directory_uri() . '/css/my_custom_style.css' );
    }
}

if (!function_exists('elumine_child_theme_slug_setup')) {
    /*
    *   Function to load child theme textdomain
    *
    */
    function elumine_child_theme_slug_setup() {
        load_child_theme_textdomain( 'elumine-child', get_stylesheet_directory() . '/languages' );
    }
}

function my_enqueue_admin() {
      
      wp_enqueue_script( 'ajax-script', get_stylesheet_directory_uri(__FILE__) . 'ajax_data_save.js', array('jquery'),  );
      wp_localize_script( 'ajax-script', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
 }
add_action( 'wp_enqueue_scripts', 'my_enqueue_admin' );

function create_response_group_leader(){
    global $wpdb;
    $answer_table_name = $wpdb->prefix . "wdm_qanda_answers_table";

    $student_response = $_POST['teacher_student_response'];
    $response_question_id = $_POST['response_question_id'];
    $response_user_id = $_POST['response_user_id'];
    $response_timestamp = $_POST['response_timestamp'];

    $output_res = "";
    if($wpdb->insert($answer_table_name, array(
        'question_ID'=> $response_question_id,
        'user_id'=> $response_user_id ,
        'answer_text'=> $student_response,
        'created_at'=> $response_timestamp
    )) == false){
        echo 'Error';
    } else{
        $reply_user = get_user_by( 'id', $response_user_id );
        $reply_username = $reply_user->user_login;
        $time = get_date_from_gmt( date( 'Y-m-d H:i:s', $response_timestamp ), get_option('time_format'));
        $output_res .= "
            <div class='messages_fetch'>
                <div class='wdm_username'>
                    <h1>$reply_username</h1>
                </div>
                <div class='wdm_message'>
                    <h4>$student_response</h4>
                </div>
            </div>
            <div class='time_fetch'>
                <h1>$time</h1>
            </div>
        ";
    }

    echo $output_res;
    die();
}

add_action('wp_ajax_create_response_group_leader', 'create_response_group_leader');
add_action('wp_ajax_nopriv_create_response_group_leader', 'create_response_group_leader');
/*
* Load Child Theme and parent theme CSS
*
*/
add_action( 'wp_enqueue_scripts', 'elumine_child_enqueue_styles' );

/*
* Load Child theme text-domain
*
*/
add_action( 'after_setup_theme', 'elumine_child_theme_slug_setup' );

