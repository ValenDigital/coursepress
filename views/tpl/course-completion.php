<script type="text/template" id="coursepress-course-completion-tpl">
    <div class="cp-box-heading">
        <h2 class="box-heading-title"><?php esc_html_e( 'Course Completion', 'cp' ); ?></h2>
    </div>

    <div class="cp-box-content cp-odd">
        <h3 class="label"><?php esc_html_e( 'Pick completion page to customize', 'cp' ); ?></h3>
        <ul class="cp-input-group cp-select-list">
            <li class="active" data-page="pre_completion"><?php esc_html_e( 'Pre Completion', 'cp' ); ?></li>
            <li data-page="course_completion"><?php esc_html_e( 'Successful Completion', 'cp' ); ?></li>
            <li data-page="course_failed"><?php esc_html_e( 'Failed', 'cp' ); ?></li>
        </ul>
    </div>

    <div class="cp-box-content">
        <h3 id="completion-title" class="cp-box-header"><?php echo esc_html( $completion_pages['pre_completion']['title'] ); ?></h3>
        <p id="completion-description" class="description"><?php echo esc_html( $completion_pages['pre_completion']['description'] ); ?></p>
    </div>

    <div class="cp-box-content">
        <div class="cp-box">
            <label class="label" for="page-completion-title"><?php esc_html_e( 'Page Title', 'cp' ); ?></label>
            <input type="text" id="page-completion-title" name="meta_pre_completion_title" class="widefat" value="{{pre_completion_title}}" />
        </div>
        <div class="cp-box">
            <label class="label" for="page-completion-content"><?php esc_html_e( 'Content', 'cp' ); ?></label>
            <div class="cp-alert cp-alert-info">
                <?php echo esc_html( $completion_pages['tokens'] ); ?>
            </div>
            <div class="cp-completion-content"></div>
        </div>
    </div>

    <div class="cp-box-content">
        <div class="box-label-area">
            <label class="label"><?php esc_html_e( 'Custom Certificate', 'cp' ); ?></label>
        </div>

        <div class="box-inner-content">
            <div class="cp-box cp-toggle-box">
                <label>
                    <input type="checkbox" name="meta_basic_certificate" value="1" class="cp-toggle-input" autocomplete="off" {{_.checked(true,basic_certificate)}} /> <span class="cp-toggle-btn"></span>
                    <span class="label"><?php esc_html_e( 'Enable custom certificate for this course', 'cp' ); ?></span>
                </label>
                <p class="description"><?php esc_html_e( 'Creates custom certificate for this course that overrides default certificate settings.', 'cp' ); ?></p>
            </div>
        </div>
    </div>
    <div id="custom-certificate-setting" class="{{!basic_certificate?'hidden':''}}">
        <div class="cp-box-content">
            <h3 class="label"><?php esc_html_e( 'Certificate Content', 'cp' ); ?></h3>
            <div class="cp-alert cp-alert-info">
                <?php echo esc_html( $completion_pages['cert_tokens'] ); ?>
            </div>
            <div class="cp-certificate-layout"></div>
        </div>
        <div class="cp-box-content">
            <div class="box-label-area">
                <h3 class="label"><?php esc_html_e( 'Background Image', 'cp' ); ?></h3>
            </div>
            <div class="box-inner-content">
                <input type="text" class="cp-add-image-input" name="meta_certificate_background" value="{{certificate_background}}" data-thumbnail="20" data-size="medium" data-title="<?php esc_html_e( 'Select Background Image', 'cp' ); ?>" />
            </div>
        </div>
        <div class="cp-box-content">
            <div class="box-label-area">
                <h3 class="label"><?php esc_html_e( 'Content Margin', 'cp' ); ?></h3>
            </div>
            <div class="box-inner-content">
                <div class="cp-flex">
                    <div class="cp-pad-right">
                        <h3 class="label"><?php esc_html_e( 'Top', 'cp' ); ?></h3>
                        <input type="number" name="meta_cert_margin.top" value="{{cert_margin.top}}" />
                    </div>
                    <div class="cp-pad-right">
                        <h3 class="label"><?php esc_html_e( 'Left', 'cp' ); ?></h3>
                        <input type="number" name="meta_cert_margin.left" value="{{cert_margin.left}}" />
                    </div>
                    <div class="cp-pad-right">
                        <h3 class="label"><?php esc_html_e( 'Right', 'cp' ); ?></h3>
                        <input type="number" name="meta_cert_margin.right" value="{{cert_margin.right}}" />
                    </div>
                </div>
            </div>
        </div>
        <div class="cp-box-content">
            <div class="box-label-area">
                <h3 class="label"><?php esc_html_e( 'Logo', 'cp' ); ?></h3>
            </div>
            <div class="box-inner-content">
                <input type="text" class="cp-add-image-input" name="meta_certificate_logo" value="{{certificate_logo}}" data-thumbnail="20" data-size="medium" data-title="<?php esc_html_e( 'Logo', 'cp' ); ?>" />
            </div>
        </div>
        <div class="cp-box-content">
            <div class="box-label-area">
                <h3 class="label"><?php esc_html_e( 'Logo Position', 'cp' ); ?></h3>
            </div>
            <div class="box-inner-content">
                <div class="cp-flex">
                    <div class="cp-pad-right">
                        <h3 class="label"><?php esc_html_e( 'X', 'cp' ); ?></h3>
                        <input type="number" name="meta_certificate_logo_position.x" value="{{certificate_logo_position.x}}" />
                    </div>
                    <div class="cp-pad-right">
                        <h3 class="label"><?php esc_html_e( 'Y', 'cp' ); ?></h3>
                        <input type="number" name="meta_certificate_logo_position.y" value="{{certificate_logo_position.y}}" />
                    </div>
                    <div class="cp-pad-right">
                        <h3 class="label"><?php esc_html_e( 'Width', 'cp' ); ?></h3>
                        <input type="number" name="meta_certificate_logo_position.w" value="{{certificate_logo_position.w}}" />
                    </div>
                </div>
            </div>
        </div>
        <div class="cp-box-content">
            <div class="box-label-area">
                <h3 class="label"><?php esc_html_e( 'Page Orientation', 'cp' ); ?></h3>
            </div>
            <div class="box-inner-content">
                <select class="widefat" name="meta_page_orientation">
                    <option value="L" {{_.selected('L', page_orientation)}}><?php esc_html_e( 'Landscape', 'cp' ); ?></option>
                    <option value="P" {{_.selected('P', page_orientation)}}><?php esc_html_e( 'Portrait', 'cp' ); ?></option>
                </select>
            </div>
        </div>
        <div class="cp-box-content">
            <div class="box-label-area">
                <h3 class="label"><?php esc_html_e( 'Text Color', 'cp' ); ?></h3>
            </div>
            <div class="box-inner-content">
                <span><input type="text" name="meta_cert_text_color" class="iris-input" value="{{cert_text_color}}" /></span>
            </div>
        </div>
        <div class="cp-box-content cp-cert-preview">
            <button type="button" class="cp-btn cp-btn-default cp-preview-cert">
                <span class="dashicons dashicons-visibility"></span>
                <?php esc_html_e( 'Preview Certificate', 'cp' ); ?>
            </button>
        </div>
    </div>
</script>

<script type="text/template" id="coursepress-cert-preview">
    <button type="button" class="cp-btn cp-btn-active"><?php esc_html_e( 'Close Preview', 'cp' ); ?></button>
    <h2><?php esc_html_e( 'Course Certificate Preview', 'cp' ); ?></h2>
    <iframe id="coursepress-cert-frame" src="{{pdf}}"></iframe>
</script>
