<div class="row">
    <div class="col-sm-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="left">
                    <h3 class="box-title"><?= esc($title); ?></h3>
                </div>
            </div>
            <form action="<?= base_url('Admin/editUserPost'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" value="<?= $user->id; ?>">
                <div class="box-body">
                    <div class="form-group">
                        <?php if (!empty($role)):
                            $roleName = @parseSerializedNameArray($role->role_name, $activeLang->id, true);
                            if ($role->is_admin): ?>
                                <label class="label bg-olive"><?= esc($roleName); ?></label>
                            <?php elseif ($role->is_author): ?>
                                <label class="label label-warning"><?= esc($roleName); ?></label>
                            <?php else: ?>
                                <label class="label label-default"><?= esc($roleName); ?></label>
                            <?php endif;
                        endif; ?>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12 col-profile">
                                <img src="<?= getUserAvatar($user); ?>" alt="" class="thumbnail img-responsive img-update" style="max-width: 210px;">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-profile">
                                <p>
                                    <a class="btn btn-success btn-sm btn-file-upload">
                                        <?= trans('change_avatar'); ?>
                                        <input name="file" size="40" accept=".png, .jpg, .webp, .jpeg" onchange="$('#upload-file-info').html($(this).val().replace(/.*[\/\\]/, ''));" type="file">
                                    </a>
                                </p>
                                <p class='label label-info' id="upload-file-info"></p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?= trans('email'); ?></label>
                        <input type="email" class="form-control form-input" name="email" placeholder="<?= trans('email'); ?>" value="<?= esc($user->email); ?>">
                    </div>

                    <div class="form-group">
                        <label><?= trans('username'); ?></label>
                        <input type="text" class="form-control form-input" name="username" placeholder="<?= trans('username'); ?>" value="<?= esc($user->username); ?>">
                    </div>

                    <div class="form-group">
                        <label><?= trans('slug'); ?></label>
                        <input type="text" class="form-control form-input" name="slug" placeholder="<?= trans('slug'); ?>" value="<?= esc($user->slug); ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label"><?= trans('about_me'); ?></label>
                        <textarea class="form-control text-area" name="about_me" placeholder="<?= trans('about_me'); ?>"><?= esc($user->about_me); ?></textarea>
                    </div>
                    <h5><strong style="font-weight: 600;"><?= trans('social_accounts'); ?></strong></h5>
                    <?php $socialArray = getSocialLinksArray($user);
                    foreach ($socialArray as $item):?>
                        <div class="form-group">
                            <input type="text" class="form-control" name="<?= $item['inputName']; ?>" placeholder="<?= $item['text'] . ' ' . trans('url'); ?>" value="<?= esc($item['value']); ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right"><?= trans('save_changes'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>