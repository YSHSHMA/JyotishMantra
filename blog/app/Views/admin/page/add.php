<div class="row">
    <div class="col-sm-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= trans('add_page'); ?></h3>
                </div>
                <div class="right">
                    <a href="<?= adminUrl('pages'); ?>" class="btn btn-success btn-add-new">
                        <i class="fa fa-bars"></i>
                        <?= trans('pages'); ?>
                    </a>
                </div>
            </div>
            <form action="<?= base_url('Admin/addPagePost'); ?>" method="post">
                <?= csrf_field(); ?>
                <div class="box-body">
                    <div class="form-group">
                        <label class="control-label"><?= trans('title'); ?></label>
                        <input type="text" class="form-control" name="title" placeholder="<?= trans('title'); ?>" value="<?= old('title'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans("slug"); ?>
                            <small>(<?= trans("slug_exp"); ?>)</small>
                        </label>
                        <input type="text" class="form-control" name="slug" placeholder="<?= trans("slug"); ?>" value="<?= old('slug'); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans("description"); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="page_description" placeholder="<?= trans("description"); ?> (<?= trans('meta_tag'); ?>)" value="<?= old('page_description'); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)</label>
                        <input type="text" class="form-control" name="page_keywords" placeholder="<?= trans('keywords'); ?> (<?= trans('meta_tag'); ?>)" value="<?= old('page_keywords'); ?>">
                    </div>

                    <div class="form-group">
                        <label><?= trans("language"); ?></label>
                        <select name="lang_id" class="form-control" onchange="getMenuLinksByLang(this.value);" style="max-width: 600px;">
                            <?php foreach ($languages as $language): ?>
                                <option value="<?= $language->id; ?>" <?= $activeLang->id == $language->id ? 'selected' : ''; ?>><?= $language->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('parent_link'); ?></label>
                        <select id="parent_links" name="parent_id" class="form-control" style="max-width: 600px;">
                            <option value="0"><?= trans('none'); ?></option>
                            <?php foreach ($menuItems as $menuItem): ?>
                                <?php if ($menuItem->item_type != "category" && $menuItem->item_location == "header" && $menuItem->item_parent_id == "0"): ?>
                                    <option value="<?= $menuItem->item_id; ?>"><?= $menuItem->item_name; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= trans('order'); ?></label>
                        <input type="number" class="form-control" name="page_order" placeholder="<?= trans('order'); ?>" value="1" min="1" style="width: 300px;max-width: 100%;">
                    </div>

                    <div class="form-group">
                        <label><?= trans("location"); ?></label>
                        <div class="row">
                            <div class="col-sm-4 col-xs-12 ">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="location" value="top" id="rd_location_1" class="custom-control-input" checked>
                                    <label for="rd_location_1" class="custom-control-label"><?= trans("top_menu"); ?></label>
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-12">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="location" value="header" id="rd_location_2" class="custom-control-input">
                                    <label for="rd_location_2" class="custom-control-label"><?= trans("main_menu"); ?></label>
                                </div>
                            </div>
                            <div class="col-sm-4 col-xs-12">
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="location" value="footer" id="rd_location_3" class="custom-control-input">
                                    <label for="rd_location_3" class="custom-control-label"><?= trans("footer"); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= trans("visibility"); ?></label>
                        <?= formRadio('page_active', 1, 0, trans("show"), trans("hide"), 1, 'col-md-4'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("show_only_registered"); ?></label>
                        <?= formRadio('need_auth', 1, 0, trans("yes"), trans("no"), 0, 'col-md-4'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("show_title"); ?></label>
                        <?= formRadio('title_active', 1, 0, trans("yes"), trans("no"), 1, 'col-md-4'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("show_breadcrumb"); ?></label>
                        <?= formRadio('breadcrumb_active', 1, 0, trans("yes"), trans("no"), 1, 'col-md-4'); ?>
                    </div>

                    <div class="form-group">
                        <label><?= trans("show_right_column"); ?></label>
                        <?= formRadio('right_column_active', 1, 0, trans("yes"), trans("no"), 1, 'col-md-4'); ?>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label"><?= trans('content'); ?></label>
                                <div class="row">
                                    <div class="col-sm-12 editor-buttons">
                                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#image_file_manager" data-image-type="editor"><i class="fa fa-image"></i>&nbsp;&nbsp;&nbsp;<?= trans("add_image"); ?></button>
                                    </div>
                                </div>
                                <textarea class="tinyMCE form-control" name="page_content"><?= old('page_content'); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('add_page'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= view('admin/file-manager/_load_file_manager', ['loadImages' => true, 'loadFiles' => false]); ?>