<?php if (!empty($postUser)): ?>
    <div class="col-sm-12 col-xs-12">
        <div class="row">
            <div class="about-author">
                <div class="about-author-left">
                    <a href="<?= langBaseUrl('profile/' . esc($postUser->slug)); ?>" class="author-link">
                        <img src="<?= getUserAvatar($postUser); ?>" alt="" class="img-responsive img-author">
                    </a>
                </div>
                <div class="about-author-right">
                    <div class="about-author-row">
                        <p class="p-about-author">
                            <strong>
                                <a href="<?= langBaseUrl('profile/' . esc($postUser->slug)); ?>" class="author-link"> <?= esc($postUser->username); ?> </a>
                            </strong>
                        </p>
                    </div>
                    <div class="about-author-row">
                        <?= esc($postUser->about_me); ?>
                        <div class="author-social-cnt">
                            <ul class="author-social">
                                <?php $socialArray = getSocialLinksArray($postUser);
                                foreach ($socialArray as $item):
                                    if (!empty($item['value'])):?>
                                        <li><a class="<?= $item['name']; ?>" href="<?= esc($item['value']); ?>" target="_blank"><i class="icon-<?= $item['name']; ?>"></i></a></li>
                                    <?php endif;
                                endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
