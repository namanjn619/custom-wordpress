<main class="full-width course-enrolled">
    <div class="row">
    <div class="elumine-overlay"></div>
        <aside class="wdm-sidebar sidebar-lessons ld-nav-sidebar">
        <!--Commented <span class="expand expand-menu"><i class="ps ps-icon-arrow-right" aria-hidden="true"></i></span> -->
            <!-- <span class="close-icon"><i class="ps ps-icon-close" aria-hidden="true"></i></span> -->
            <div class="wdm-sidebar-content">
            <div class="enrolled-left">
                <div class="top-details">

                    <?php
                    include elumine_locate_custom_templates($template_part_relative . 'elumine-course-navigation-dynamic.php');
                    ?>
                    <div class="lessons-list-container">
                        <?php
                        $course = get_post($course_id);
                        $lessons = elumine_get_learndash_lessons_list($course_id, $user_id);
                        include elumine_locate_custom_templates($template_part_relative . 'content-widget-dynamic.php');
                        ?>
                    </div>
                </div>
                <?php
                dynamic_sidebar('elumine-ld-sidebar');
                ?>
            </div>
          </div>
        </aside>
        <span class="elumine-expand">
            <span class="elumine-icon icon-Expand"></span>
        </span>
        <span class="elumine-collapse">
            <span class="elumine-icon icon-Contract"></span>
        </span>
        <section class="enrolled-content-dynamic">
            <?php
                if (has_post_thumbnail(get_the_ID())) :
                    the_post_thumbnail();
                endif;
            ?>
            <div class="enrolled-content container">
                <?php
                $parent_topic_title = learndash_course_get_single_parent_step($course_id, get_the_ID());
                if (empty($parent_topic_title)) {
                    $parent_topic_title = $course_id;
                }
                ?>
                <span class="parent-topic-tile"><?php echo get_post_field('post_title', $parent_topic_title); ?></span>
                <h1 class="elumine-title" title="<?php echo get_post_field('post_title', get_the_ID()); ?>">
                    <?php //the_title();
                    if (isset($quiz_post) && !empty($quiz_post)) {
                        if ('sfwd-quiz' == $quiz_post->post_type) {
                            $el_pro_quiz_id = get_post_meta($quiz_post->ID, 'quiz_pro_id', true);
                            $quizMapper = new \WpProQuiz_Model_QuizMapper();
                            $quizModel = $quizMapper->fetch($el_pro_quiz_id);
                            // if (! $quizModel->isTitleHidden()) {
                                echo $quizModel->getName();
                            // }
                        }
                    }
                    ?>
                </h1>
                <div class="topic">
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
                                                    <?php 
                                                    _e('Instructions', 'Quiz Materials Label', 'elumine');
                                                    // printf(
                                                    //     /* translators: %s: Quiz Label placeholders: Quiz Materials Label */
                                                    //     _x('%s Instructions', 'Quiz Materials Label', 'elumine'),
                                                    //     LearnDash_Custom_Label::get_label('quiz')
                                                    // ); 
                                                    ?>
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
                    <?php
                    if (comments_open() || get_comments_number()) : ?>
                    <div class="top-responses">
                        <?php comments_template();?>
                    </div>
                <?php endif; ?>
                </div>

        </section>
    </div>
</main>
