<div class="row">
    <div class="col-md-8 col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans("auto_post_deletion"); ?></h3>
                </div>
            </div>
            <form action="<?= base_url('Post/autoPostDeletionPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("status"); ?></label>
                        <?= formRadio('auto_post_deletion', 1, 0, trans("enable"), trans("disable"), $generalSettings->auto_post_deletion); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("posts"); ?></label>
                        <?= formRadio('auto_post_deletion_delete_all', 1, 0, trans("delete_all_posts"), trans("delete_only_rss_posts"), $generalSettings->auto_post_deletion_delete_all); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans('number_of_days'); ?>&nbsp;<small>(E.g. <?= trans("number_of_days_exp") ?>)</small></label>
                        <input type="number" class="form-control" name="auto_post_deletion_days" value="<?= esc($generalSettings->auto_post_deletion_days); ?>" min="1" max="99999999" required>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans("save_changes"); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>