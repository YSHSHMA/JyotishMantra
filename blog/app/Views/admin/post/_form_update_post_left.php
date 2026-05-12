<div class="box">
    <div class="box-header with-border">
        <div class="left">
            <h3 class="box-title"><?= trans('post_details'); ?></h3>
        </div>
    </div>

    <div class="box-body">
        <?php if (!empty(helperGetSession('msg_error'))): ?>
            <div class="m-b-15">
                <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <h4>
                        <i class="icon fa fa-times"></i>
                        <?= helperGetSession('msg_error');
                        helperDeleteSession('msg_error'); ?>
                    </h4>
                </div>
            </div>
        <?php endif;
        if (!empty(helperGetSession('msg_success'))): ?>
            <div class="m-b-15">
                <div class="alert alert-success alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <h4>
                        <i class="icon fa fa-check"></i>
                        <?= helperGetSession('msg_success');
                        helperDeleteSession('msg_success') ?>
                    </h4>
                </div>
            </div>
        <?php endif; ?>

        <input type="hidden" name="id" value="<?= esc($post->id); ?>">
        <input type="hidden" name="referrer" class="form-control" value="">

        <div class="form-group">
            <label class="control-label"><?= trans('title'); ?></label>
            <input type="text" class="form-control" name="title" placeholder="<?= trans('title'); ?>" value="<?= esc($post->title); ?>" required>
        </div>

        <div class="form-group">
            <label class="control-label"><?= trans('slug'); ?>
                <small>(<?= trans('slug_exp'); ?>)</small>
            </label>
            <input type="text" class="form-control" name="title_slug" placeholder="<?= trans('slug'); ?>" value="<?= esc($post->title_slug); ?>">
        </div>

        <div class="form-group">
            <label class="control-label"><?= trans('summary'); ?> & <?= trans("description"); ?> (<?= trans('meta_tag'); ?>)</label>
            <textarea class="form-control text-area" name="summary" placeholder="<?= trans('summary'); ?> & <?= trans("description"); ?> (<?= trans('meta_tag'); ?>)"><?= esc($post->summary); ?></textarea>
        </div>

        <div class="form-group">
            <label class="control-label"><?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)</label>
            <input type="text" class="form-control" name="keywords" placeholder="<?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)" value="<?= esc($post->keywords); ?>">
        </div>

        <?php if (isAdmin()): ?>
            <div class="form-group">
                <label><?= trans("visibility"); ?></label>
                <?= formRadio('visibility', 1, 0, trans("show"), trans("hide"), $post->visibility, 'col-md-3'); ?>
            </div>
        <?php else: ?>
            <input type="hidden" name="visibility" value="0">
        <?php endif; ?>

        <?php if (isAdmin()): ?>
            <div class="form-group">
                <?= formCheckbox('is_slider', 1, trans("add_slider"), $post->is_slider); ?>
            </div>
        <?php else: ?>
            <input type="hidden" name="is_slider" value="<?= $post->is_slider; ?>">
        <?php endif; ?>

        <?php if (isAdmin()): ?>
            <div class="form-group">
                <?= formCheckbox('is_picked', 1, trans("add_picked"), $post->is_picked); ?>
            </div>
        <?php else: ?>
            <input type="hidden" name="is_picked" value="<?= $post->is_picked; ?>">
        <?php endif; ?>

        <div class="form-group">
            <?= formCheckbox('need_auth', 1, trans("show_only_registered"), $post->need_auth); ?>
        </div>

        <div class="form-group">
            <label class="control-label"><?= trans('tags'); ?></label>
            <input id="tags_1" type="text" name="tags" class="form-control tags" value="<?= esc($tags); ?>"/>
            <small>(<?= trans('type_tag'); ?>)</small>
        </div>

        <div class="form-group">
            <label class="control-label"><?= trans('optional_url'); ?></label>
            <input type="text" class="form-control" name="optional_url" placeholder="<?= trans('optional_url'); ?>" value="<?= esc($post->optional_url); ?>">
        </div>

    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <label class="control-label"><?= trans('content'); ?></label>
        <div class="row">
            <div class="col-sm-12 editor-buttons">
                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#image_file_manager" data-image-type="editor"><i class="fa fa-image"></i>&nbsp;&nbsp;&nbsp;<?= trans("add_image"); ?></button>
            </div>
        </div>
        <textarea class="tinyMCE form-control" name="content"><?= $post->content; ?></textarea>
    </div>
</div>
