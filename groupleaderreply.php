<?php /* Template Name: GroupLeaderReply */ ?>
<?php
    if (isset($_GET['question_ID'])) {
         $ppc = $_GET['question_ID'];
    } 
    else {
        $relocate = get_site_url();
        header("Location:".  $relocate);
        exit;
    }

    global $wpdb;
    $current_page_user = get_current_user_id();
    $question_table_name = $wpdb->prefix . "wdm_qanda_questions_table";
    $answer_table_name = $wpdb->prefix . "wdm_qanda_answers_table";
    $question_fetch = $wpdb->get_results("SELECT * FROM $question_table_name WHERE question_ID = $ppc");
    $reply_fetch = $wpdb->get_results("SELECT * FROM $answer_table_name WHERE question_ID = $ppc");

     get_header(); ?>

<?php
    if( current_user_can( 'administrator' ) or current_user_can( 'group_leader' )){
        ?>
            <div id="primary" class="content-area">
                <main id="main" class="site-main" role="main">
                    <div class="wdm_outer_div">
                        <div class="wdm_inner_div">
                            <div class="wdm_chat_app">
                                <div class="app_heading">
                                    <h1>Question Details</h1>
                                    <div class="wdm_question_details">
                                        <?php
                                            foreach($question_fetch as $all_questions){
                                                $question_pass_id = $all_questions->question_ID;
                                                $question_text = $all_questions->question_text;
                                                $question_time = $all_questions->created_at;
                                                $course_id = $all_questions->course_id;
                                                $question_course_name = get_the_title($course_id);
                                                $cltq_id = $all_questions->cltq_id;
                                                $question_clqt_name = get_the_title($cltq_id);
                                                $question_student_name = $all_questions->student_id;
                                                $question_user = get_user_by( 'id', $question_student_name );
                                                $question_username = $question_user->user_login;
                                            ?>
                                            <h3><span>Student Name :</span> <?php echo $question_username; ?></h3>
                                            <div class="date_time_div">
                                                <h3><span>Date :</span> <?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $question_time ), get_option('date_format')); ?></h3>
                                                <h3><span>Time :</span> <?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $question_time ), get_option('time_format')); ?></h3>
                                            </div>
                                            <div class="course_detail_div">
                                                 <h3><span>Course :</span> <?php echo $question_course_name; ?></h3>
                                                <h3><span>L/Q/T : </span> <?php echo $question_clqt_name; ?></h3>
                                            </div>
                                            <?php
                                            }
                                        ?>
                                    </div>
                                </div>
                                <div id="wdm_chat_box_gl">
                                    <?php
                                        foreach($question_fetch as $one_question){
                                            // Question text
                                            $question_text = $one_question->question_text;
                                            // Question username
                                            $user_ID = $one_question->student_id;
                                            $user = get_user_by( 'id', $user_ID );
                                            $username = $user->user_login;
                                            // Question time
                                            $question_time = $one_question->created_at;
                                            ?>
                                            <div class="messages_fetch_left">
                                                <div class="wdm_message_left">
                                                    <h4><?php echo $question_text; ?></h4>
                                                </div>
                                                <div class="wdm_username_left">
                                                    <h1><?php echo $username; ?></h1>
                                                </div>
                                            </div>
                                            <div class="time_fetch_left">
                                                <h1><?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $question_time ), get_option('time_format')) ?></h1>
                                            </div>
                                    <?php
                                        }
                                    ?>
                                    <?php
                                    // current user
                                    $current_user = get_current_user_id();
                                    foreach($reply_fetch as $one_reply){
                                        // Reply text
                                        $reply_text = $one_reply->answer_text;
                                        // Reply username
                                        $reply_user_ID = $one_reply->user_id;
                                        $reply_user = get_user_by( 'id', $reply_user_ID );
                                        $reply_username = $reply_user->user_login;
                                        // Reply Time
                                        $reply_time = $one_reply->created_at;
                                        
                                        
                                        if($reply_user_ID == $current_user){
                                        ?>
                                            <div class="messages_fetch">
                                                <div class="wdm_username">
                                                    <h1><?php echo $reply_username; ?></h1>
                                                </div>
                                                <div class="wdm_message">
                                                    <h4><?php echo $reply_text; ?></h4>
                                                </div>
                                            </div>
                                            <div class="time_fetch">
                                                <h1><?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $reply_time ), get_option('time_format')) ?></h1>
                                            </div>

                                        <?php
                                        }elseif($reply_user_ID != $current_user){
                                            ?>
                                            <div class="messages_fetch_left">
                                                <div class="wdm_message_left">
                                                    <h4><?php echo $reply_text; ?></h4>
                                                </div>
                                                <div class="wdm_username_left">
                                                    <h1><?php echo $reply_username; ?></h1>
                                                </div>
                                            </div>
                                            <div class="time_fetch_left">
                                                <h1><?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $reply_time ), get_option('time_format')) ?></h1>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="wdm_reply_box">
                                    <div class="reply_box_gm">
                                        <form id="response_form_submit_gl" method="post">
                                            <input type="text" id="question" name="teacher_student_response" id="teacher_student_response" required="required" placeholder="Text space to write a Response to the question">
                                            <input type="hidden" name="response_question_id" id="response_question_id" value="<?php echo $ppc; ?>">
                                            <input type="hidden" name="response_user_id" id="response_user_id" value="<?php echo $current_page_user; ?>">
                                            <input type="hidden" name="response_timestamp" id="response_timestamp" value="<?php echo current_time('timestamp', 1); ?>">

                                            <input type="hidden" name="action" value="create_response_group_leader">

                                            <button class="course-button medium-btn" id="gl_response_button" name="response_post" type="submit"><i class="fa fa-arrow-right"></i></button>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </main><!-- .site-main -->
            </div><!-- .content-area -->
        <?php
    }else{
        ?>
            <h1>You are not a Admin!</h1>
            <h4>Login and Try Again!</h4>
        <?php
    }
?>


<?php get_footer(); ?>
<script>
    <?php require_once("adminreplyjs.js");?>
</script>

<?php