<main class="course-enrolled">
    <div class="row">
    <div class="elumine-overlay"></div>
        <div class="col-12 col-xs-12 col-sm-12 col-md-3 col-lg-3">
        <aside class="wdm-sidebar sidebar-lessons  pr-md-4">
        <!--Commented <span class="expand expand-menu"><i class="ps ps-icon-arrow-right" aria-hidden="true"></i></span> -->
            <!-- <span class="close-icon"><i class="ps ps-icon-close" aria-hidden="true"></i></span> -->
            <div class="wdm-sidebar-content p-sm-15">
            <div class="enrolled-left">
                <div class="top-details">
                    <div class="title">
                        <h3><?php printf(_x('%s %s', 'elumine'), LearnDash_Custom_Label::get_label('course'), LearnDash_Custom_Label::get_label('lessons')); ?></h3>
                    </div>
                    <?php
                    include elumine_locate_custom_templates($template_part_relative . 'elumine-mobile-navigation.php');
                    ?>
                    <div class="lessons-list-container">
                        <?php
                        $course = get_post($course_id);
                        $lessons = elumine_get_learndash_lessons_list($course_id, $user_id);
                        include elumine_locate_custom_templates($template_part_relative . 'content-widget.php');
                        ?>
                    </div>
                </div>
                <?php
                dynamic_sidebar('elumine-ld-sidebar');
                ?>
            </div>
          </div>
        </aside>
    </div>
        <section class="enrolled-right wdm-sidebar-right col-12 col-xs-12 col-sm-12 col-md-9 col-lg-9">
            <div class="enrolled-top-details d-flex align-items-center justify-content-between">
                <div class="course-title elumine-fixed-title align-items-center justify-content-center">
                <div class="elu-nav"><span></span><span></span><span></span></div>
                    <div class="title for-mobile"><h1 class="title" title="<?php echo get_the_title(learndash_get_course_id(get_the_ID())); ?>"><?php echo get_the_title(learndash_get_course_id(get_the_ID())); ?></h1>
                        <!-- <hr> -->
                    </div>
                </div>
                <?php
                $course_duration = wdm_get_course_expiry_setting($course_id);
                ?>
            <div class="timer-progress">
                <?php
                if ($course_duration != 0) {
                    $course_spent_time = wdm_get_course_spent_time($course_id);
                    if (!$course_spent_time) {
                        $course_spent_time = 1;
                    }
                    if ($course_spent_time > $course_duration) {
                        $course_spent_time = $course_duration;
                    }
                    ?>
                    <i class="secondary-color ps ps-icon-watch1 hidden-md-down" style="font-size: 42px;"></i>
                    <div class="value d-flex justify-content-center align-items-center">
                        <span class="up"><?php echo $course_spent_time;?></span>
                        <hr>
                        <span class="down"><span class="slash">/</span><span><?php echo $course_duration.__(' Days', 'elumine'); ?></span></span>
                    </div>
                    <?php
                    if ($course_duration != 0 && is_user_logged_in()) {
                        echo "<div class='vertical-line'></div>";
                    }
                }
                ?>
                <div class="enrolled-progress">
                    <?php
                        $user_id = get_current_user_id();
                        $progress = get_user_meta($user_id, '_sfwd-course_progress', true);
                    if (is_user_logged_in()) {
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
                        ?>
                        <div class="progress-circle for-mobile"><span class="label mr-2"><?php _e('Progress:', 'elumine'); ?></span><span class="value"><?php echo $progress_percentage."%"; ?></span></div>
                        <div class="progress-circle" data-progress="<?php echo $progress_percentage; ?>">
                        <div class="circle">
                            <div class="full progress-circle__slice">
                                <div class="progress-circle__fill"></div>
                          </div>
                            <div class="progress-circle__slice">
                                <div class="progress-circle__fill"></div>
                                <div class="progress-circle__fill progress-circle__bar"></div>
                            </div>
                        </div>
                        <div class="progress-circle__overlay"><?php echo $progress_percentage."%"; ?></div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            </div>
            <div class="enrolled-content">
                <div class="topic">
                    <div class="title">
                        <?php
                        if (isset($quiz_post) && !empty($quiz_post)) {
                            if ('sfwd-quiz' === $quiz_post->post_type) {
                                $el_pro_quiz_id = get_post_meta($quiz_post->ID, 'quiz_pro_id', true);
                                $quizMapper = new \WpProQuiz_Model_QuizMapper();
                                $quizModel = $quizMapper->fetch($el_pro_quiz_id);
                                if (! $quizModel->isTitleHidden()) {
                                    echo $quizModel->getName();
                                }
                            }
                        }
                        // the_title();
                        ?>
                    </div>
                    <div class="content">
                    <?php
                    if (!version_compare(LEARNDASH_VERSION, '2.6.0', '>=')) {
                        if (! empty($lesson_progression_enabled) && ! learndash_is_quiz_accessable(null, $quiz_post)) {
                            if (empty($quiz_settings['lesson'])) {
                                echo sprintf(wp_kses_post(_x('Please go back and complete the previous %s.', 'placeholder lesson', 'elumine')), LearnDash_Custom_Label::label_to_lower('lesson'));
                            } else {
                                echo sprintf(wp_kses_post(_x('Please go back and complete the previous %s.', 'placeholder topic', 'elumine')), LearnDash_Custom_Label::label_to_lower('topic'));
                            }
                        }
                    } else {
                        if (! empty($lesson_progression_enabled)) {
                            $last_incomplete_step = learndash_is_quiz_accessable(null, $quiz_post, true);
                            if (1 !== $last_incomplete_step) {
                                if (is_a($last_incomplete_step, 'WP_Post')) {
                                    if ($last_incomplete_step->post_type === learndash_get_post_type_slug('topic')) {
                                        echo sprintf(
                                            // translators: placeholders: topic URL.
                                            esc_html_x('Please go back and complete the previous %s.', 'placeholders: topic URL', 'elumine'),
                                            '<a class="learndash-link-previous-incomplete" href="' . learndash_get_step_permalink($last_incomplete_step->ID, $course_id) . '">' . LearnDash_Custom_Label::label_to_lower('topic') . '</a>'
                                        );
                                    } elseif ($last_incomplete_step->post_type === learndash_get_post_type_slug('lesson')) {
                                        echo sprintf(
                                            // translators: placeholders: lesson URL.
                                            esc_html_x('Please go back and complete the previous %s.', 'placeholders: lesson URL', 'elumine'),
                                            '<a class="learndash-link-previous-incomplete" href="' . learndash_get_step_permalink($last_incomplete_step->ID, $course_id) . '">' . LearnDash_Custom_Label::label_to_lower('lesson') . '</a>'
                                        );
                                    } elseif ($last_incomplete_step->post_type === learndash_get_post_type_slug('quiz')) {
                                        echo sprintf(
                                            // translators: placeholders: quiz URL.
                                            esc_html_x('Please go back and complete the previous %s.', 'placeholders: quiz URL', 'elumine'),
                                            '<a class="learndash-link-previous-incomplete" href="' . learndash_get_step_permalink($last_incomplete_step->ID, $course_id) . '">' . LearnDash_Custom_Label::label_to_lower('quiz') . '</a>'
                                        );
                                    }
                                }
                            }
                        }
                    }

                    if ($show_content) {
                        ?>
                        <div class="wdm_quiz_content">
                            <div class="learndash_content elumine_content"><?php echo $content; ?></div>
                            <?php
                            if (( isset($materials) ) && ( !empty($materials) )) :
                                ?>
                                    <div id="learndash_topic_materials learndash_quiz_materials" class="learndash_topic_materials learndash_quiz_materials">
                                        <div class="sub-heading">
                                            <h3>
                                                <span class="ml-0">
                                                    <?php printf(
                                                        /* translators: %s: Quiz Label placeholders: Quiz Materials Label */
                                                        _x('%s Materials', 'Quiz Materials Label', 'elumine'),
                                                        LearnDash_Custom_Label::get_label('quiz')
                                                    ); ?>
                                                </span>
                                            </h3>
                                        </div>
                                        <div class="wdm-material-content"><?php echo $materials; ?></div>
                                    </div>
                                    <?php
                            endif;
                            ?>
                            <?php
                            if ($attempts_left) {
                                echo $quiz_content;
                            } else {
                                ?>
                                    <p id="learndash_already_taken">
                                        <?php echo sprintf(
                                            /* translators: 1: Quiz Label 2: Attempts Count */
                                            _x('You have already taken this %1$s %2$d time(s) and may not take it again.', 'placeholders: quiz, attempts count', 'elumine'),
                                            LearnDash_Custom_Label::label_to_lower('quiz'),
                                            $attempts_count
                                        ); ?>
                                    </p>
                                    <?php
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    </div>
                </div>

        </section>
    </div>
</main>
