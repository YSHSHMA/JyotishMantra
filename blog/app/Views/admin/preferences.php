<div class="row m-b-15">
    <div class="col-sm-12">
        <h3 class="page-main-title"><?= trans('preferences'); ?></h3>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans("general"); ?></h3>
            </div>
            <form action="<?= base_url('Admin/preferencesPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("multilingual_system"); ?></label>
                        <?= formRadio('multilingual_system', 1, 0, trans("enable"), trans("disable"), $generalSettings->multilingual_system); ?>
                    </div>
                    <div class="form-group">
                        <label><?= trans("registration_system"); ?></label>
                        <?= formRadio('registration_system', 1, 0, trans("enable"), trans("disable"), $generalSettings->registration_system); ?>
                    </div>
                    <div class="form-group">
                        <label><?= trans("approve_posts_before_publishing"); ?></label>
                        <?= formRadio('approve_posts_before_publishing', 1, 0, trans("enable"), trans("disable"), $generalSettings->approve_posts_before_publishing); ?>
                    </div>
                    <div class="form-group">
                        <label><?= trans("comment_system"); ?></label>
                        <?= formRadio('comment_system', 1, 0, trans("enable"), trans("disable"), $generalSettings->comment_system); ?>
                    </div>
                    <div class="form-group">
                        <label><?= trans("comment_approval_system"); ?></label>
                        <?= formRadio('comment_approval_system', 1, 0, trans("enable"), trans("disable"), $generalSettings->comment_approval_system); ?>
                    </div>
                    <div class="form-group">
                        <label><?= trans("slider"); ?></label>
                        <?= formRadio('slider_active', 1, 0, trans("enable"), trans("disable"), $generalSettings->slider_active); ?>
                    </div>
                    <div class="form-group">
                        <label><?= trans("emoji_reactions"); ?></label>
                        <?= formRadio('emoji_reactions', 1, 0, trans("enable"), trans("disable"), $generalSettings->emoji_reactions); ?>
                    </div>
                    <div class="form-group">
                        <label><?= trans("show_post_view_counts"); ?></label>
                        <?= formRadio('show_pageviews', 1, 0, trans("enable"), trans("disable"), $generalSettings->show_pageviews); ?>
                    </div>
                    <div class="form-group">
                        <label><?= trans("rss"); ?></label>
                        <?= formRadio('show_rss', 1, 0, trans("enable"), trans("disable"), $generalSettings->show_rss); ?>
                    </div>
                    <div class="form-group">
                        <label><?= trans("rss_content"); ?></label>
                        <?= formRadio('rss_content_type', 'summary', 'content', trans("distribute_only_post_summary"), trans("distribute_post_content"), $generalSettings->rss_content_type); ?>
                    </div>
                    <div class="form-group">
                        <label><?= trans("show_categories_sidebar"); ?></label>
                        <?= formRadio('sidebar_categories', 1, 0, trans("yes"), trans("no"), $generalSettings->sidebar_categories); ?>
                    </div>
                    <div class="form-group">
                        <label><?= trans("file_manager"); ?></label>
                        <?= formRadio('file_manager_show_all_files', 1, 0, trans("show_all_files"), trans("show_only_own_files"), $generalSettings->file_manager_show_all_files); ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("default_role_members"); ?></label>
                        <select name="default_role_id" class="form-control" required>
                            <option value=""><?= trans("select"); ?></option>
                            <?php if (!empty($roles)):
                                foreach ($roles as $item):
                                    $roleName = @parseSerializedNameArray($item->role_name, $activeLang->id, true); ?>
                                    <option value="<?= $item->id; ?>" <?= $item->id == $generalSettings->default_role_id ? 'selected' : ''; ?>><?= esc($roleName); ?></option>
                                <?php endforeach;
                            endif; ?>
                        </select>
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
                <h3 class="box-title"><?= trans("file_upload") ?></h3>
            </div>
            <form action="<?= base_url('Admin/fileUploadSettingsPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <label><?= trans('image_file_format'); ?></label>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="image_file_format" value="JPG" id="image_file_format_1" class="custom-control-input" <?= $generalSettings->image_file_format == 'JPG' ? 'checked' : ''; ?>>
                                    <label for="image_file_format_1" class="custom-control-label">JPG</label>
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="image_file_format" value="WEBP" id="image_file_format_2" class="custom-control-input" <?= $generalSettings->image_file_format == 'WEBP' ? 'checked' : ''; ?>>
                                    <label for="image_file_format_2" class="custom-control-label">WEBP</label>
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-12 col-option">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="image_file_format" value="PNG" id="image_file_format_3" class="custom-control-input" <?= $generalSettings->image_file_format == 'PNG' ? 'checked' : ''; ?>>
                                    <label for="image_file_format_3" class="custom-control-label">PNG</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans("allowed_file_extensions"); ?>&nbsp;(<?= trans('file_manager'); ?>)</label>
                        <div class="row">
                            <div class="col-sm-12">
                                <input id="input_allowed_file_extensions" type="text" name="allowed_file_extensions" value="<?= str_replace('"', '', $generalSettings->allowed_file_extensions ?? ''); ?>" class="form-control tags"/>
                                <small>(<?= trans('type_extension'); ?>&nbsp;E.g. zip, jpg, doc, pdf..)</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" name="submit" value="post_deletion" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>