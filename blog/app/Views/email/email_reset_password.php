<?= view("email/_header"); ?>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
        <tr>
            <td>&nbsp;</td>
            <td class="container">
                <div class="content">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
                        <tr>
                            <td style="text-align: center;">
                                <div style="height: 70px;width:100%;text-align: center;margin-bottom: 10px;">
                                    <a href="<?= base_url(); ?>">
                                        <img src="<?= getLogo($generalSettings); ?>" alt="" style="max-width: 180px;max-height: 70px;">
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table role="presentation" class="main">
                        <tr>
                            <td class="wrapper">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <h1 style="text-decoration: none; font-size: 24px;line-height: 28px;font-weight: bold"><?= trans("reset_password"); ?></h1>
                                            <div class="mailcontent" style="line-height: 26px;font-size: 14px;">
                                                <p style='text-align: center'>
                                                    <?= trans("email_reset_password"); ?><br>
                                                </p>
                                                <p style='text-align: center;margin-top: 30px;'>
                                                    <a href="<?= langBaseUrl('reset-password?token=' . $token); ?>" style='font-size: 14px;text-decoration: none;padding: 14px 40px;background-color: #09b1ba;color: #ffffff !important; border-radius: 3px;'>
                                                        <?= trans("reset_password"); ?>
                                                    </a>
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <div class="footer">
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content-block powered-by">
                                    <span class="apple-link"><?= esc($settings->contact_address); ?></span><br>
                                    <?= esc($settings->copyright); ?>
                                </td>
                            </tr>
                        </table>
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="content-block">
                                    <div style="display: inline-block; margin: 0 auto;">
                                        <?php $socialArray = getSocialLinksArray($settings);
                                        foreach ($socialArray as $item):
                                            if (!empty($item['value'])):?>
                                                <a href="<?= esc($item['value']); ?>" target="_blank" style="float: left; margin-right: 8px;display: block;cursor: pointer">
                                                    <img src="<?= base_url('assets/img/social/' . esc($item['name']) . '.png'); ?>" alt="" style="width: 32px; height: 32px;"/>
                                                </a>
                                            <?php endif; endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
<?= view("email/_footer"); ?>