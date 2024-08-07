<div class="elumine-course-buy">
    <?php
    $course_lessons_list = learndash_get_course_steps($course_id, array('sfwd-lessons'));
    $course_quizzes_list = learndash_course_get_steps_by_type($course_id, 'sfwd-quiz');
    $video_url = get_post_meta($course_id, 'wdm_video_url_field', true);
    if (wdm_check_wdm_course_review_plugin_installed()) {
        $ratingsDetails = rrf_get_course_rating_details($course_id);
    }
    $course_duration_days = wdm_get_course_expiry_setting($course_id);
    if (0 != $course_duration_days && is_int($course_duration_days)) {
        $course_duration = wdm_get_interval_string_from_days($course_duration_days);
    }
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $courses_access_from = ld_course_access_from( $course_id, $user_id );
        if ( empty( $courses_access_from ) ) {
            $courses_access_from = learndash_user_group_enrolled_to_course_from( $user_id, $course_id );
        }

        if ( !empty( $courses_access_from ) ) {

            $expire_on = ld_course_access_expires_on( $course_id, $user_id );
            if (!empty($expire_on)) {
                $course_expire_date = date(get_option('date_format'), $expire_on + (get_option('gmt_offset') * 3600));
            }
        }
    }
    $course_user_query = learndash_get_users_for_course(intval($course_id));
    $first_module = null;
    if (!empty($lessons)) {
        $first_module = reset($lessons)['permalink'];
    } else if (!empty($quizzes)) {
        $first_module = reset($quizzes)['permalink'];
    }

    if (!$has_access) {
        include elumine_locate_custom_templates($template_part_relative . 'sticky-buy-header.php');
    }
    global $elumine_dynamic_featured_image;
    $cover_image = $elumine_dynamic_featured_image->get_nth_featured_image(2, $course_id);
    $bg_style = '';
    $style = '';
    $url = '';
    if (!is_null($cover_image)) :
        $url = wp_get_attachment_image_url($cover_image['attachment_id'], 'full');
        $bg_style = "background-image: url($url)";
        $style = "background-color: rgba(0, 0, 0, 0.7);";
    endif;
    ?>
    <div class="el-banner-image el-defer-bg" style="<?php echo $bg_style; ?>">
        <section class="el-banner" style="<?php echo $style; ?>">
            <div class="el-banner-container">
                <div class="left-section">
                </div>
                <div class="right-section">
                    <?php
                    $course_categories = elumine_get_course_categories($course_id);
                    if (!empty($course_categories)) { ?>
                        <div class="buy-category-wrapper">
                            <?php
                            foreach ($course_categories as $category) {
                                echo "<span class='el-course-cat'>" . $category->name . "</span>";
                            }
                            ?>
                        </div> <?php
                        }
                        ?>
                    <h1 class="el-course-title"><?php the_title(); ?></h1>
                    <?php
                    if ($has_access) {
                        if (is_user_logged_in()) {
                            $user_id = get_current_user_id();
                            $progress = get_user_meta($user_id, '_sfwd-course_progress', true);
                            if (!empty($progress) && array_key_exists($course_id, $progress)) {
                                $completed = $progress[$course_id]['completed'];
                                $total = intval($progress[$course_id]['total']);
                                if ($total === 0) {
                                    $total = 1;
                                }
                                $progress_percentage = (int)$completed * 100 / $total;
                                $progress_percentage = ceil($progress_percentage);
                            } else {
                                $progress_percentage = 0;
                            }
                            $progress_percentage .= '%';
                            ?>
                            <div class="d-flex el-course-progress">
                                <div class="progress-fill" style="width: <?php echo $progress_percentage ?>;">
                                    <div class="el-percent-text">
                                        <?php
                                        printf(__("%s Completed", 'elumine'), $progress_percentage); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="buying-info-wrap">
                                <div>
                                    <?php
                                    $course_args     = array(
                                        'course_id'     => $course_id,
                                        'user_id'       => $user_id,
                                        'post_id'       => $course_id,
                                        'activity_type' => 'course',
                                    );
                                    $course_activity = learndash_get_user_activity($course_args);
                                    if ($course_activity) : ?>
                                        <div class="d-inline-flex mr-4  align-items-center">
                                            <i class="icon-Last-active mr-2"></i>
                                            <div class="d-inline-flex">
                                                <span class="last-active mr-1">
                                                    <?php
                                                    esc_html_e('Last active: ', 'elumine');
                                                    ?>
                                                </span>
                                                <span>
                                                    <?php
                                                    $date_time_display = get_date_from_gmt(date('Y-m-d H:i:s', $course_activity->activity_updated), 'Y-m-d H:i:s');
                                                    echo date_i18n(get_option('date_format'), strtotime($date_time_display));
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($course_expire_date) && !empty($course_expire_date)) : ?>
                                        <div class="d-inline-flex mr-4  align-items-center">
                                            <i class="icon-Expries-on mr-2"></i>
                                            <div class="d-inline-flex">
                                                <span class="last-active mr-1"><?php esc_html_e('Expires on:', 'elumine'); ?> </span>
                                                <span><?php echo esc_html($course_expire_date); ?></span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-4 el-actions">
                                    <?php if ('0%' != $progress_percentage) { ?>
                                        <a href="<?php echo esc_url($first_module); ?>" class="mr-2 start-over button"><?php echo esc_html__('Start Over', 'elumine') ?></a>
                                        <?php if ('100%' != $progress_percentage) : ?>
                                            <a href="<?php echo esc_url(elumine_continue_button_link($course_id)); ?>" class="button"><?php echo esc_html__('Resume', 'elumine'); ?></a>
                                        <?php endif; ?>
                                    <?php } else { ?>
                                        <a href="<?php echo esc_url($first_module); ?>" class="mr-2 start-over button"><?php echo esc_html(sprintf(__('Start %s', 'elumine'), \LearnDash_Custom_Label::get_label('course'))); ?></a>
                                    <?php } ?>
                                    <?php  if (! empty($course_certficate_link)) : ?>
                                        <div id="learndash_course_certificate" class="learndash_course_certificate">
                                            <a href='<?php echo esc_attr($course_certficate_link); ?>' class="btn-blue button"
                                                target="_blank">
                                                <?php echo apply_filters('ld_certificate_link_label', __('Print Your Certificate', 'elumine'), $user_id, $post->ID); ?>
                                            </a>
                                        </div>
                                        <br />
                                        <?php endif; ?>
                                </div>
                                <?php if ( learndash_course_completed( $user_id, $course_id ) ) : ?>
                                    <?php if ( function_exists( 'rrf_is_feedback_form_enabled' ) && rrf_is_feedback_form_enabled( $course_id ) && ! rrf_is_user_submitted_feedback_form( $user_id, $course_id ) ) : ?>
                                        <div class="el-additional-actions">
                                            <i class="icon-Share-Feedback"></i>
                                            <?php
                                            echo apply_filters('ld_after_course_status_template_container', '', learndash_course_status_idx($course_status), $course_id, $user_id);
                                            ?>
                                        </div>
                                    <?php endif;?>
                                <?php endif; ?>
                            </div>
                        <?php
                    }
                } else {
                    ?>
                        <div class="instrutor-wrap d-flex">
                            <div class="instructor d-inline-flex mr-4  align-items-center">
                                <?php
                                $author_data = elumine_get_author_meta(array('width' => 35, 'height' => 35));
                                ?>
                                <a href="<?php echo esc_url($author_data['link']); ?>">
                                    <?php echo $author_data['avatar']; ?>
                                    <span><?php the_author(); ?></span>
                                </a>
                            </div>
                            <?php if (0 < count($course_lessons_list)) : ?>
                                <div class="lessons d-inline-flex mr-4 align-items-center">
                                    <i class="icon-Lessons"></i>
                                    <?php if (count($course_lessons_list) > 1) : ?>
                                        <?php
                                        echo "<span>" . count($course_lessons_list) . ' ' .\LearnDash_Custom_Label::get_label('lessons') ." </span>";
                                        ?>
                                    <?php else : ?>
                                        <?php
                                        echo "<span>" . count($course_lessons_list) . ' ' .\LearnDash_Custom_Label::get_label('lesson') ." </span>";
                                        ?>
                                    <?php endif;?>
                                </div>
                            <?php endif;?>
                            <?php if (0 < count($course_quizzes_list)) : ?>
                                <div class="quizzes d-inline-flex mr-4 align-items-center">
                                    <i class="icon-Quiz"></i>
                                    <?php if (count($course_quizzes_list) > 1) : ?>
                                        <?php
                                        echo "<span>" . count($course_quizzes_list) . ' ' . \LearnDash_Custom_Label::get_label('quizzes') . "</span>";
                                        ?>
                                    <?php else : ?>
                                        <?php
                                        echo "<span>" . count($course_quizzes_list) . ' ' .\LearnDash_Custom_Label::get_label('quiz') ." </span>";
                                        ?>
                                    <?php endif;?>
                                </div>
                            <?php endif;?>
                        </div>
                        <div class="buying-info-wrap">
                            <div class="buying-info d-flex">
                                <?php if (isset($ratingsDetails) && !empty($ratingsDetails)) : ?>
                                    <div class="ratings d-inline-flex mr-4  align-items-center">
                                        <div class='el-ratings d-inline-flex align-items-center'>
                                            <i class="icon-Star-1 mr-1"></i>
                                            <span><?php echo $ratingsDetails['average_rating']; ?></span>
                                        </div>

                                        <span class="ml-2">(<?php echo esc_html($ratingsDetails['total_rating']); ?>)</span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($course_duration)) : ?>
                                    <div class="course-duration d-inline-flex mr-4  align-items-center">
                                        <i class="icon-Duration mr-2"></i>
                                        <span><?php echo esc_html($course_duration); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($course_user_query instanceof WP_User_Query) : ?>
                                    <div class="students inst d-inline-flex mr-4  align-items-center">
                                        <i class="icon-Enroll-Students mr-2"></i>
                                        <span>
                                            <?php echo esc_html(sprintf(_n("%d Students", "%d Student", 'elumine'), $course_user_query->get_total())); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="price-buy d-flex align-items-center mt-3">
                                <span class="mr-4 price-tag"><?php echo wdm_get_course_price($course_id); ?></span>
                                <?php echo learndash_payment_buttons($post); ?>
                            </div>
                        </div>
                    <?php
                }
                ?>


                </div>
            </div>
        </section>
    </div>
    <div class="elumine-learndash-content-wrap" id="el-description">
        <div class="elumine_content">
            <div class="el-tabs">
                <?php if ((isset($materials)) && (!empty($materials))) : ?>
                    <span class="material" data-id="el-material"><?php printf(_x('%s Materials', 'Course Materials Label', 'elumine'), LearnDash_Custom_Label::get_label('course')); ?></span>
                <?php endif; ?>
                <span class="intro el-current" data-id="el-intro"><?php esc_html_e('Introduction', 'elumine'); ?></span>
            </div>
            <?php echo '<div class="intro" data-id="el-intro" style="display: block;">' . $content . '</div>'; ?>
            <?php if ((isset($materials)) && (!empty($materials))) : ?>
                <?php echo '<div class="material" data-id="el-material">' . $materials . '</div>'; ?>
            <?php endif; ?>
        </div>
        <div class="course-buy-container">
            <?php
            include elumine_locate_custom_templates($template_part_relative . 'content-course-table.php');
            ?>
        </div>
    </div>
</div>
