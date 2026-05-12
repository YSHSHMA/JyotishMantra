<?php $activeTab = inputGet('tab');
if (empty($activeTab)):
    $activeTab = '1';
endif; ?>
<div class="row">
    <div class="col-md-12">
        <form action="<?= base_url('Admin/settingsPost'); ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field(); ?>
            <input type="hidden" name="active_tab" id="input_active_tab" value="<?= cleanNumber($activeTab); ?>">
            <div class="form-group">
                <label><?= trans("settings_language"); ?></label>
                <select name="lang_id" class="form-control max-400" onchange="window.location.href = '<?= adminUrl(); ?>'+'/settings?lang='+this.value;">
                    <?php foreach ($languages as $language): ?>
                        <option value="<?= $language->id; ?>" <?= $settingsLangId == $language->id ? 'selected' : ''; ?>><?= $language->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" name="logo_path" value="<?= esc($generalSettings->logo_path); ?>">
            <input type="hidden" name="favicon_path" value="<?= esc($generalSettings->favicon_path); ?>">

            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="<?= $activeTab == '1' ? ' active' : ''; ?>"><a href="#tab_1" data-toggle="tab" onclick="$('#input_active_tab').val('1');"><?= trans('general_settings'); ?></a></li>
                    <li class="<?= $activeTab == '2' ? ' active' : ''; ?>"><a href="#tab_2" data-toggle="tab" onclick="$('#input_active_tab').val('2');"><?= trans('visual_settings'); ?></a></li>
                    <li class="<?= $activeTab == '3' ? ' active' : ''; ?>"><a href="#tab_3" data-toggle="tab" onclick="$('#input_active_tab').val('3');"><?= trans('contact_settings'); ?></a></li>
                    <li class="<?= $activeTab == '4' ? ' active' : ''; ?>"><a href="#tab_4" data-toggle="tab" onclick="$('#input_active_tab').val('4');"><?= trans('social_media_settings'); ?></a></li>
                    <li class="<?= $activeTab == '5' ? ' active' : ''; ?>"><a href="#tab_5" data-toggle="tab" onclick="$('#input_active_tab').val('5');"><?= trans('facebook_comments'); ?></a></li>
                    <li class="<?= $activeTab == '6' ? ' active' : ''; ?>"><a href="#tab_6" data-toggle="tab" onclick="$('#input_active_tab').val('6');"><?= trans('cookies_warning'); ?></a></li>
                    <li class="<?= $activeTab == '7' ? ' active' : ''; ?>"><a href="#tab_7" data-toggle="tab" onclick="$('#input_active_tab').val('7');"><?= trans('custom_header_codes'); ?></a></li>
                    <li class="<?= $activeTab == '8' ? ' active' : ''; ?>"><a href="#tab_8" data-toggle="tab" onclick="$('#input_active_tab').val('8');"><?= trans('custom_footer_codes'); ?></a></li>
                </ul>
                <div class="tab-content settings-tab-content">

                    <div class="tab-pane<?= $activeTab == '1' ? ' active' : ''; ?>" id="tab_1">
                        <div class="form-group">
                            <label class="control-label"><?= trans('timezone'); ?></label>
                            <select name="timezone" class="form-control max-600">
                                <?php $timezones = timezone_identifiers_list();
                                if (!empty($timezones)):
                                    foreach ($timezones as $timezone):?>
                                        <option value="<?= $timezone; ?>" <?= $timezone == $generalSettings->timezone ? 'selected' : ''; ?>><?= $timezone; ?></option>
                                    <?php endforeach;
                                endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('app_name'); ?></label>
                            <input type="text" class="form-control max-600" name="application_name" placeholder="<?= trans('app_name'); ?>" value="<?= esc($formSettings->application_name); ?>">
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('admin_panel_link'); ?></label>
                            <input type="text" class="form-control max-600" name="admin_route" placeholder="<?= trans('admin_panel_link'); ?>" value="<?= $generalSettings->admin_route; ?>">
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('pagination_number_posts'); ?></label>
                            <input type="number" class="form-control" name="pagination_per_page" value="<?= esc($generalSettings->pagination_per_page); ?>" required style="max-width: 300px;">
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('optional_url_name'); ?></label>
                            <input type="text" class="form-control" name="optional_url_button_name" placeholder="<?= trans('optional_url_name'); ?>" value="<?= esc($formSettings->optional_url_button_name); ?>">
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('footer_about_section'); ?></label>
                            <textarea class="form-control text-area" name="about_footer" placeholder="<?= trans('footer_about_section'); ?>" style="min-height: 70px;"><?= esc($formSettings->about_footer); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('copyright'); ?></label>
                            <input type="text" class="form-control" name="copyright" placeholder="<?= trans('copyright'); ?>" value="<?= esc($formSettings->copyright); ?>">
                        </div>
                    </div>

                    <div class="tab-pane<?= $activeTab == '2' ? ' active' : ''; ?>" id="tab_2">
                        <div class="form-group">
                            <label><?= trans("site_color"); ?></label>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="clrpicker" class="color-picker">
                                        <input type="text" name="site_color" value="<?= $generalSettings->site_color; ?>" class="form-control" style="width: 150px;" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <br>
                        <div class="form-group" style="margin-bottom: 45px;">
                            <div class="row">
                                <div class="col-md-4 col-sm-12" style="margin-bottom: 15px;">
                                    <label class="control-label"><?= trans('logo'); ?></label>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?php if (!empty($generalSettings->logo_path)): ?>
                                                <img src="<?= base_url($generalSettings->logo_path); ?>" alt="" width="<?= $generalSettings->logo_desktop_width; ?>" height="<?= $generalSettings->logo_desktop_height; ?>" style="max-width: 500px;">
                                            <?php else: ?>
                                                <img src="<?= base_url('assets/img/logo.png'); ?>" alt="" width="<?= $generalSettings->logo_desktop_width; ?>" height="<?= $generalSettings->logo_desktop_height; ?>" style="max-width: 500px;">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 5px;">
                                        <div class="col-sm-12">
                                            <a class='btn btn-success btn-sm btn-file-upload'>
                                                <?= trans('change_logo'); ?>
                                                <input type="file" name="logo" size="40" accept=".png, .jpg, .webp, .jpeg, .gif" onchange="$('#upload-file-info1').html($(this).val().replace(/.*[\/\\]/, ''));">
                                            </a>
                                        </div>
                                    </div>
                                    <span class='label label-info' id="upload-file-info1"></span>
                                </div>

                                <div class="col-md-4 col-sm-12">
                                    <label class="control-label"><?= trans('logo'); ?> (<?= trans("dark_mode"); ?>)</label>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <?php if (!empty($generalSettings->logo_darkmode_path)): ?>
                                                <img src="<?= base_url($generalSettings->logo_darkmode_path); ?>" alt="" width="<?= $generalSettings->logo_desktop_width; ?>" height="<?= $generalSettings->logo_desktop_height; ?>" style="max-width: 500px; background-color: #f7f7f7;">
                                            <?php else: ?>
                                                <img src="<?= base_url('assets/img/logo-mobile.png'); ?>" alt="" width="<?= $generalSettings->logo_desktop_width; ?>" height="<?= $generalSettings->logo_desktop_height; ?>" style="max-width: 500px; background-color: #f7f7f7;">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-top: 5px;">
                                        <div class="col-sm-12">
                                            <a class='btn btn-success btn-sm btn-file-upload'>
                                                <?= trans('change_logo'); ?>
                                                <input type="file" name="logo_darkmode" size="40" accept=".png, .jpg, .webp, .jpeg, .gif" onchange="$('#upload-file-info2').html($(this).val().replace(/.*[\/\\]/, ''));">
                                            </a>
                                        </div>
                                    </div>
                                    <span class='label label-info' id="upload-file-info2"></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4 col-sm-12">
                                    <label class="m-b-10"><?= trans("logo_size"); ?>&nbsp;(<?= trans("desktop"); ?>)</label>
                                    <div class="row" style="max-width: 400px; margin-bottom: 15px;">
                                        <div class="col-sm-12 col-md-6">
                                            <span><?= trans("width"); ?>(px)</span>
                                            <input type="number" name="logo_desktop_width" class="form-control" value="<?= $generalSettings->logo_desktop_width; ?>" min="10" max="300">
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <span><?= trans("height"); ?>(px)</span>
                                            <input type="number" name="logo_desktop_height" class="form-control" value="<?= $generalSettings->logo_desktop_height; ?>" min="10" max="300">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <label class="m-b-10"><?= trans("logo_size"); ?>&nbsp;(<?= trans("mobile"); ?>)</label>
                                    <div class="row" style="max-width: 400px; margin-bottom: 15px;">
                                        <div class="col-sm-12 col-md-6">
                                            <span><?= trans("width"); ?>(px)</span>
                                            <input type="number" name="logo_mobile_width" class="form-control" value="<?= $generalSettings->logo_mobile_width; ?>" min="10" max="300">
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <span><?= trans("height"); ?>(px)</span>
                                            <input type="number" name="logo_mobile_height" class="form-control" value="<?= $generalSettings->logo_mobile_height; ?>" min="10" max="300">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <p class="text-success"><i class="fa fa-info-circle"></i>&nbsp;<?= trans("logo_size_exp"); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 15px;">
                            <label class="control-label" style="margin-top: 10px;"><?= trans('favicon'); ?></label>
                            <div class="row">
                                <div class="col-sm-3">
                                    <?php if (!empty($generalSettings->favicon_path)): ?>
                                        <img src="<?= base_url($generalSettings->favicon_path); ?>" alt="" style="max-width: 200px;">
                                    <?php else: ?>
                                        <img src="<?= base_url('assets/img/favicon.png'); ?>" alt="" style="max-width: 200px;">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 5px;">
                                <div class="col-sm-12">
                                    <a class='btn btn-success btn-sm btn-file-upload'>
                                        <?= trans('change_favicon'); ?>
                                        <input type="file" name="favicon" size="40" accept=".png, .jpg, .webp, .jpeg, .gif" onchange="$('#upload-file-info3').html($(this).val());">
                                    </a>
                                </div>
                            </div>
                            <span class='label label-info' id="upload-file-info3"></span>
                        </div>
                    </div>

                    <div class="tab-pane<?= $activeTab == '3' ? ' active' : ''; ?>" id="tab_3">
                        <div class="form-group">
                            <label class="control-label"><?= trans('address'); ?></label>
                            <input type="text" class="form-control" name="contact_address" placeholder="<?= trans('address'); ?>" value="<?= esc($formSettings->contact_address); ?>">
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('email'); ?></label>
                            <input type="text" class="form-control" name="contact_email" placeholder="<?= trans('email'); ?>" value="<?= esc($formSettings->contact_email); ?>">
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('phone'); ?></label>
                            <input type="text" class="form-control" name="contact_phone" placeholder="<?= trans('phone'); ?>" value="<?= esc($formSettings->contact_phone); ?>">
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('contact_text'); ?></label>
                            <textarea class="tinyMCE form-control" name="contact_text"><?= $formSettings->contact_text; ?></textarea>
                        </div>
                    </div>

                    <div class="tab-pane<?= $activeTab == '4' ? ' active' : ''; ?>" id="tab_4">
                        <?php $socialArray = getSocialLinksArray($formSettings);
                        foreach ($socialArray as $item):?>
                            <div class="form-group">
                                <label class="control-label"><?= $item['text'] . ' ' . trans('url'); ?></label>
                                <input type="text" class="form-control" name="<?= $item['inputName']; ?>" placeholder="<?= $item['text'] . ' ' . trans('url'); ?>" value="<?= esc($item['value']); ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="tab-pane<?= $activeTab == '5' ? ' active' : ''; ?>" id="tab_5">
                        <div class="form-group">
                            <label class="control-label"><?= trans('facebook_comments_code'); ?></label>
                            <textarea class="form-control text-area" name="facebook_comment" placeholder="<?= trans('facebook_comments_code'); ?>" style="min-height: 140px;"><?= esc($generalSettings->facebook_comment); ?></textarea>
                        </div>
                    </div>

                    <div class="tab-pane<?= $activeTab == '6' ? ' active' : ''; ?>" id="tab_6">
                        <div class="form-group">
                            <label><?= trans("show_cookies_warning"); ?></label>
                            <?= formRadio('cookies_warning', 1, 0, trans("yes"), trans("no"), $formSettings->cookies_warning, 'col-md-4'); ?>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= trans('cookies_warning_text'); ?></label>
                            <textarea class="tinyMCE form-control" name="cookies_warning_text"><?= $formSettings->cookies_warning_text; ?></textarea>
                        </div>
                    </div>

                    <div class="tab-pane<?= $activeTab == '7' ? ' active' : ''; ?>" id="tab_7">
                        <div class="form-group">
                            <label class="control-label"><?= trans('custom_header_codes'); ?></label>&nbsp;<small class="small-title-inline">(<?= trans("custom_header_codes_exp"); ?>)</small>
                            <textarea class="form-control text-area" name="custom_header_codes" placeholder="<?= trans('custom_header_codes'); ?>" style="height: 200px;"><?= $generalSettings->custom_header_codes; ?></textarea>
                        </div>
                        E.g. <?= esc("<style> body {background-color: #00a65a;} </style>"); ?>
                    </div>

                    <div class="tab-pane<?= $activeTab == '8' ? ' active' : ''; ?>" id="tab_8">
                        <div class="form-group">
                            <label class="control-label"><?= trans('custom_footer_codes'); ?></label>&nbsp;<small class="small-title-inline">(<?= trans("custom_footer_codes_exp"); ?>)</small>
                            <textarea class="form-control text-area" name="custom_footer_codes" placeholder="<?= trans('custom_footer_codes'); ?>" style="height: 200px;"><?= $generalSettings->custom_footer_codes; ?></textarea>
                        </div>
                        E.g. <?= esc("<script> alert('Hello!'); </script>"); ?>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('google_recaptcha'); ?></h3>
            </div>
            <form action="<?= base_url('Admin/recaptchaSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('site_key'); ?></label>
                        <input type="text" class="form-control" name="recaptcha_site_key" placeholder="<?= trans('site_key'); ?>" value="<?= $generalSettings->recaptcha_site_key; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('secret_key'); ?></label>
                        <input type="text" class="form-control" name="recaptcha_secret_key" placeholder="<?= trans('secret_key'); ?>" value="<?= $generalSettings->recaptcha_secret_key; ?>">
                    </div>

                    <div class="box-footer" style="padding-left: 0; padding-right: 0;">
                        <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('maintenance_mode'); ?></h3>
            </div>
            <form action="<?= base_url('Admin/maintenanceModePost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('title'); ?></label>
                        <input type="text" class="form-control" name="maintenance_mode_title" placeholder="<?= trans('title'); ?>" value="<?= $generalSettings->maintenance_mode_title; ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('description'); ?></label>
                        <textarea class="form-control text-area" name="maintenance_mode_description" placeholder="<?= trans('description'); ?>" style="min-height: 100px;"><?= esc($generalSettings->maintenance_mode_description); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label><?= trans("status"); ?></label>
                        <?= formRadio('maintenance_mode_status', 1, 0, trans("enable"), trans("disable"), $generalSettings->maintenance_mode_status); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans('image'); ?></label>: assets/img/maintenance_bg.jpg
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .tox-tinymce {
        height: 340px !important;
    }
</style>
<script>
    $(function () {
        $('#clrpicker').colorpicker({
            popover: false,
            inline: true,
            container: '#clrpicker',
            format: 'hex'
        });
        $('#clrpicker-block').colorpicker({
            popover: false,
            inline: true,
            container: '#clrpicker-block',
            format: 'hex'
        });
    });
</script>

