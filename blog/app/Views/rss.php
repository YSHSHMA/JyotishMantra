<?= '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<rss version="2.0"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:admin="http://webns.net/mvcb/"
     xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:media="http://search.yahoo.com/mrss/">
<channel>
<title><?= convertToXMLCharacter(xml_convert($feedName ?? '')); ?></title>
<link><?= $feedURL; ?></link>
<description><?= convertToXMLCharacter(xml_convert($pageDescription ?? '')); ?></description>
<dc:language><?= $pageLanguage; ?></dc:language>
<dc:rights><?= convertToXMLCharacter(xml_convert($baseSettings->copyright ?? '')); ?></dc:rights>

<?php $showPostContent = false;
if ($generalSettings->rss_content_type == 'content') {
$showPostContent = true;
}
if (!empty($posts)):
foreach ($posts as $post):
$postBaseUrl = langBaseUrl();
if ($post->lang_id == $generalSettings->site_lang) {
$postBaseUrl = base_url() . '/';
} else {
$postBaseUrl = base_url($post->lang_short_form) . '/';
}
$keywords = '';
if (!empty($post->keywords)) {
$arrayKeywords = explode(',', $post->keywords);
if (!empty($arrayKeywords)) {
$i = 0;
foreach ($arrayKeywords as $str) {
if ($i <= 5) {
$str = trim($str ?? '');
$str = esc($str);
if (!empty($str)) {
if (empty($keywords)) {
$keywords = $str;
} else {
$keywords .= ', ' . $str;
}}}}}}
$post->title_slug = urlencode($post->title_slug);?>
<item>
<title><?= convertToXMLCharacter(xml_convert($post->title ?? '')); ?></title>
<link><?= generatePostURL($post, $postBaseUrl); ?></link>
<guid><?= generatePostURL($post, $postBaseUrl); ?></guid>
<description><![CDATA[ <?= esc($post->summary); ?> ]]></description>
<?php if (!empty($post->image_url)):
$imagePath = $post->image_url;
if (strpos($imagePath, '?') !== false) {
$imagePath = strtok($imagePath, "?");
}
$imagePath = str_replace('https://', 'http://', $imagePath ?? ''); ?>
<enclosure url="<?= $imagePath; ?>" length="49398" type="image/jpeg"/>
<?php else:
$imagePath = base_url($post->image_big);
if ($post->image_storage == 'aws_s3') {
$imagePath = getAWSBaseURL() . $post->image_big;
}
if (!empty($imagePath)) {
$fileSize = @filesize(FCPATH . $post->image_big);
}
$imagePath = str_replace('https://', 'http://', $imagePath ?? '');
if (empty($fileSize) || $fileSize < 1) {
$fileSize = 49398;
}
if (!empty($fileSize)):?>
<enclosure url="<?= $imagePath; ?>" length="<?= $fileSize; ?>" type="image/jpeg"/>
<?php endif;
endif; ?>
<pubDate><?= date('r', strtotime($post->created_at)); ?></pubDate>
<dc:creator><?= convertToXMLCharacter($post->username); ?></dc:creator>
<media:keywords><?= $keywords; ?></media:keywords>
<?php if ($showPostContent): ?>
<content:encoded><?php if (!empty($post->content)): ?><![CDATA[<?= $post->content; ?>]]> <?php endif; ?></content:encoded>
<?php endif; ?>
</item>

<?php endforeach;
endif; ?>
</channel>
</rss>