<?php $logoMobileHeight = $generalSettings->logo_mobile_height;
if(empty($logoMobileHeight) || $logoMobileHeight < 10 || $logoMobileHeight > 300){
    $logoMobileHeight = 50;
}
if (!empty($adSpaces)):?>
<style>@media (max-width: 992px) {.modal-search {margin-top:10px;top: <?= $logoMobileHeight; ?>px;}.mobile-nav-container {margin-top:10px;min-height: <?= $logoMobileHeight; ?>px;}<?php
foreach ($adSpaces as $item):
if (!empty($item->desktop_width) && !empty($item->desktop_height)):
echo '.bn-ds-' . $item->id . '{width: ' . $item->desktop_width . 'px; height: ' . $item->desktop_height . 'px;}';
echo '.bn-mb-' . $item->id . '{width: ' . $item->mobile_width . 'px; height: ' . $item->mobile_height . 'px;}';
endif;
endforeach;?>
</style><?php endif; ?>
<script>var InfConfig = {baseUrl: '<?= base_url(); ?>', csrfTokenName: '<?= csrf_token() ?>', sysLangId: '<?= $activeLang->id; ?>', isRecaptchaEnabled: '<?= isRecaptchaEnabled($generalSettings) ? 1 : 0; ?>', textOk: "<?= clrQuotes(trans("ok")); ?>", textCancel: "<?= clrQuotes(trans("cancel")); ?>"};</script>