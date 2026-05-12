<div class="row">
    <div class="col-lg-7 col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= trans('cache_system'); ?></h3>
            </div>
            <form action="<?= base_url('Admin/cacheSystemPost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label><?= trans("cache_system"); ?></label>
                        <?= formRadio('cache_system', 1, 0, trans("enable"), trans("disable"), $generalSettings->cache_system); ?>
                    </div>
                    <div class="form-group">
                        <label><?= trans("refresh_cache_database_changes"); ?></label>
                        <?= formRadio('refresh_cache_database_changes', 1, 0, trans("yes"), trans("no"), $generalSettings->refresh_cache_database_changes); ?>
                    </div>
                    <div class="form-group">
                        <label class="control-label"><?= trans('cache_refresh_time'); ?></label>&nbsp;
                        <small>(<?= trans("cache_refresh_time_exp"); ?>)</small>
                        <input type="number" class="form-control" name="cache_refresh_time" placeholder="<?= trans('cache_refresh_time'); ?>" value="<?= $generalSettings->cache_refresh_time / 60; ?>">
                    </div>
                    <div class="box-footer" style="padding-left: 0; padding-right: 0;">
                        <button type="submit" name="action" value="save" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                        <button type="submit" name="action" value="reset" class="btn btn-warning pull-right m-r-10"><?= trans('reset_cache'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>