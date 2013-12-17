<?php

class checkbox_input_module extends Unit_Module {

    var $name = 'checkbox_input_module';
    var $label = 'Check Box Input';
    var $description = 'Allows adding check boxes to the unit';
    var $front_save = true;
    var $response_type = 'view';

    function __construct() {
        $this->on_create();
    }

    function checkbox_input_module() {
        $this->__construct();
    }

    function get_response_form($user_ID, $response_request_ID, $show_label = true) {
        $response = $this->get_response($user_ID, $response_request_ID);
        if (count($response >= 1)) {
            $student_checked_answers = get_post_meta($response->ID, 'student_checked_answers', true);
            ?>
            <div class="module_text_response_answer">
                <?php if ($show_label) { ?>
                    <label><?php _e('Response', 'cp'); ?></label>
                <?php } ?>
                <div class="front_response_content radio_input_module">
                    <ul class='radio_answer_check_li'>
                        <?php
                        $answers = get_post_meta($response_request_ID, 'answers', true);
                        $checked_answers = get_post_meta($response_request_ID, 'checked_answers', true);

                        foreach ($answers as $answer) {
                            ?>
                            <li>
                                <input class="radio_answer_check" type="checkbox" value='<?php echo esc_attr($answer); ?>' disabled <?php echo (isset($student_checked_answers) && in_array($answer, $student_checked_answers) ? 'checked' : ''); ?> /><?php echo $answer; ?><?php
                                if (isset($student_checked_answers) && in_array($answer, $student_checked_answers)) {
                                    echo (in_array($answer, $checked_answers) ? '<span class="correct_answer">✓</span>' : '<span class="not_correct_answer">✘</span>');
                                };
                                ?>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>

            <?php
        } else {
            _e('No answer / response', 'cp');
        }
        ?>
        <div class="full regular-border-devider"></div>
        <?php
    }

    function get_response($user_ID, $response_request_ID) {
        $already_respond_posts_args = array(
            'posts_per_page' => 1,
            'meta_key' => 'user_ID',
            'meta_value' => $user_ID,
            'post_type' => 'module_response',
            'post_parent' => $response_request_ID,
            'post_status' => 'publish'
        );

        $already_respond_posts = get_posts($already_respond_posts_args);

        if (isset($already_respond_posts[0]) && is_object($already_respond_posts[0])) {
            $response = $already_respond_posts[0];
        } else {
            $response = $already_respond_posts;
        }

        return $response;
    }

    function front_main($data) {

        $response = $this->get_response(get_current_user_id(), $data->ID);

        if (is_object($response)) {
            $student_checked_answers = get_post_meta($response->ID, 'student_checked_answers', true);
        }

        if (count($response) == 0) {
            $enabled = 'enabled';
        } else {
            $enabled = 'disabled';
        }
        ?>
        <div class="<?php echo $this->name; ?> front-single-module<?php echo ($this->front_save == true ? '-save' : '');?>">
            <h2 class="module_title"><?php echo $data->post_title; ?></h2>
            <div class="module_description"><?php echo $data->post_content; ?></div>

            <ul class='radio_answer_check_li'>
                <?php
                if (isset($data->answers) && !empty($data->answers)) {
                    foreach ($data->answers as $answer) {
                        ?>
                        <li>
                            <input class="checkbox_answer_check" type="checkbox" name="<?php echo $this->name . '_front_' . $data->ID; ?>[]" value='<?php echo esc_attr($answer); ?>' <?php echo $enabled; ?> <?php echo (isset($student_checked_answers) && in_array($answer, (is_array($student_checked_answers) ? $student_checked_answers : array())) ? 'checked' : ''); ?> /><?php echo $answer; ?>
                        </li>
                        <?php
                    }
                }
                ?>
            </ul>

        </div>
        <?php
    }

    function admin_main($data) {
        ?>

        <div class="<?php if (empty($data)) { ?>draggable-<?php } ?>module-holder-<?php echo $this->name; ?> module-holder-title" <?php if (empty($data)) { ?>style="display:none;"<?php } ?>>

            <h3 class="module-title sidebar-name">
                <span class="h3-label"><?php echo $this->label; ?><?php echo (isset($data->post_title) ? ' (' . $data->post_title . ')' : ''); ?></span>
            </h3>

            <div class="module-content">
                <?php
                if (isset($data->ID)) {
                    parent::get_module_delete_link($data->ID);
                } else {
                    parent::get_module_remove_link();
                }
                ?>
                <!--<input type="hidden" name="<?php echo $this->name; ?>_checked_index[]" class='checked_index' value="0" />-->

                <input type="hidden" name="<?php echo $this->name; ?>_module_order[]" class="module_order" value="<?php echo (isset($data->module_order) ? $data->module_order : 999); ?>" />
                <input type="hidden" name="module_type[]" value="<?php echo $this->name; ?>" />
                <input type="hidden" name="<?php echo $this->name; ?>_id[]" value="<?php echo (isset($data->ID) ? $data->ID : ''); ?>" />
                <label><?php _e('Title', 'cp'); ?>
                    <input type="text" name="<?php echo $this->name; ?>_title[]" value="<?php echo esc_attr(isset($data->post_title) ? $data->post_title : ''); ?>" />
                </label>

                <div class="editor_in_place">
                    <?php
                    $args = array("textarea_name" => $this->name . "_content[]", "textarea_rows" => 5);
                    wp_editor(stripslashes((isset($data->post_content) ? $data->post_content : '')), (esc_attr(isset($data->ID) ? 'editor_' . $data->ID : rand(1, 9999))), $args);
                    ?>
                </div>

                <div class="checkbox-editor">
                    <table class="form-table">
                        <tbody class="ci_items">
                            <tr>
                                <th width="90%">
                        <div class="checkbox_answer"><?php _e('Answers', 'cp'); ?></div>
                        <div class="checkbox_answer_check"><?php _e('Correct'); ?></div>
                        </th>
                        <th width="10%">
                            <a class="checkbox_new_link"><?php _e('Add New', 'cp'); ?></a>
                        </th>
                        </tr>

                        <?php
                        $i = 1;
                        ?>

                        <?php
                        if (isset($data->ID)) {

                            $answer_cnt = 0;

                            if (isset($data->answers)) {
                                foreach ($data->answers as $answer) {
                                    ?>
                                    <tr>
                                        <td width="90%">
                                            <input class="checkbox_answer" type="text" name="<?php echo $this->name . '_checkbox_answers[' . (isset($data->module_order) ? $data->module_order : 999) . '][]'; ?>" value='<?php echo esc_attr((isset($answer) ? $answer : '')); ?>' />
                                            <input class="checkbox_answer_check" type="checkbox" name="<?php echo $this->name . '_checkbox_check[' . (isset($data->module_order) ? $data->module_order : 999) . '][]'; ?>" value='<?php echo esc_attr((isset($answer) ? $answer : '')); ?>' <?php
                                            if (in_array($answer, $data->checked_answers)) {
                                                echo 'checked';
                                            }
                                            ?> />
                                        </td>
                                        <?php if ($answer_cnt >= 2) { ?>
                                            <td width="10%">    
                                                <a class="checkbox_remove" onclick="jQuery(this).parent().parent().remove();">Remove</a>
                                            </td>
                                        <?php } else { ?>
                                            <td width="10%">&nbsp;</td>
                                        <?php } ?>
                                    </tr>
                                    <?php
                                    $answer_cnt++;
                                }
                            }
                        } else {
                            ?>
                            <tr>
                                <td width="90%">
                                    <input class="checkbox_answer" type="text" name="<?php echo $this->name . '_checkbox_answers[' . (isset($data->module_order) ? $data->module_order : 999) . '][]'; ?>" />
                                    <input class="checkbox_answer_check" type="checkbox" name="<?php echo $this->name . '_checkbox_check[' . (isset($data->module_order) ? $data->module_order : 999) . '][]'; ?>" checked />
                                </td>
                                <td width="10%">&nbsp;</td>  
                            </tr>

                            <tr>
                                <td width="90%">
                                    <input class="checkbox_answer" type="text" name="<?php echo $this->name . '_checkbox_answers[' . (isset($data->module_order) ? $data->module_order : 999) . '][]'; ?>" />
                                    <input class="checkbox_answer_check" type="checkbox" name="<?php echo $this->name . '_checkbox_check[' . (isset($data->module_order) ? $data->module_order : 999) . '][]'; ?>" />
                                </td>
                                <td width="10%">&nbsp;</td>  
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>

                </div>

            </div>

        </div>

        <?php
    }

    function on_create() {
        $this->save_module_data();
        parent::additional_module_actions();
    }

    function save_module_data() {
        global $wpdb, $last_inserted_unit_id;

        if (isset($_POST['module_type'])) {

            $answers = array();
            $checked_answers = array();

            foreach ($_POST[$this->name . '_checkbox_answers'] as $post_answers) {
                $answers[] = $post_answers;
            }

            foreach ($_POST[$this->name . '_checkbox_check'] as $post_checked_answers) {
                $checked_answers[] = $post_checked_answers;
            }

            //cp_write_log($checked_answers);

            foreach (array_keys($_POST['module_type']) as $module_type => $module_value) {

                if ($module_value == $this->name) {
                    $data = new stdClass();
                    $data->ID = '';
                    $data->unit_id = '';
                    $data->title = '';
                    $data->excerpt = '';
                    $data->content = '';
                    $data->metas = array();
                    $data->metas['module_type'] = $this->name;
                    $data->post_type = 'module';

                    foreach ($_POST[$this->name . '_id'] as $key => $value) {

                        $data->ID = $_POST[$this->name . '_id'][$key];
                        $data->unit_id = ((isset($_POST['unit_id']) and isset($_POST['unit']) and $_POST['unit'] != '') ? $_POST['unit_id'] : $last_inserted_unit_id);
                        $data->title = $_POST[$this->name . '_title'][$key];
                        $data->content = $_POST[$this->name . '_content'][$key];
                        $data->metas['module_order'] = $_POST[$this->name . '_module_order'][$key];
                        $data->metas['answers'] = $answers[$key];
                        $data->metas['checked_answers'] = $checked_answers[$key];

                        parent::update_module($data);
                    }
                }
            }
        }

        if (isset($_POST['submit_modules_data'])) {

            foreach ($_POST as $response_name => $response_value) {


                if (preg_match('/' . $this->name . '_front_/', $response_name)) {

                    $response_id = intval(str_replace($this->name . '_front_', '', $response_name));

                    if ($response_value != '') {
                        $data = new stdClass();
                        $data->ID = '';
                        $data->title = '';
                        $data->excerpt = '';
                        $data->content = '';
                        $data->metas = array();
                        $data->metas['user_ID'] = get_current_user_id();
                        $data->post_type = 'module_response';
                        $data->response_id = $response_id;
                        $data->title = ''; //__('Response to '.$response_id.' module (Unit '.$_POST['unit_id'].')');
                        $data->content = '';
                        $data->metas['student_checked_answers'] = $response_value;

                        /* CHECK AND SET THE GRADE AUTOMATICALLY */

                        $chosen_answers = array();

                        foreach ($response_value as $post_response_val) {
                            $chosen_answers[] = $post_response_val;
                        }


                        if (count($chosen_answers) !== 0) {
                            $right_answers = get_post_meta($response_id, 'checked_answers', true);
                            $response_grade = 0;

                            foreach ($chosen_answers as $chosen_answer) {
                                if (in_array($chosen_answer, $right_answers)) {
                                    $response_grade = $response_grade + 100;
                                } else {
                                    //$response_grade = $response_grade + 0;//this line can be empty as well :)
                                }
                            }

                            if (count($chosen_answers) >= count($right_answers)) {
                                $grade_cnt = count($chosen_answers);
                            } else {
                                $grade_cnt = count($right_answers);
                            }

                            $response_grade = round(($response_grade / $grade_cnt), 0);
                            $data->auto_grade = $response_grade;
                        }

                        parent::update_module_response($data);
                    }
                }
            }
        }
    }

}

coursepress_register_module('checkbox_input_module', 'checkbox_input_module', 'students');
?>