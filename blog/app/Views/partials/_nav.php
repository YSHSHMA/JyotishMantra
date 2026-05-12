<?php $menuLimit = $generalSettings->menu_limit;
$activePage = uri_string();
if ($generalSettings->site_lang != $activeLang->id):
$activePage = getSegmentValue(2);
endif;
$activePage = trim($activePage ?? '', '/'); ?>
<div class="nav-desktop">
<div class="row">
<div class="collapse navbar-collapse">
<ul class="nav navbar-nav navbar-left">
<li class="li-navbar-brand">
<a class="navbar-brand" href="https://mahakal.com/">
<img src="<?= $darkMode == 1 ? getMobileLogo($generalSettings) : getLogo($generalSettings); ?>" alt="logo" width="<?= $generalSettings->logo_desktop_width; ?>" height="<?= $generalSettings->logo_desktop_height; ?>">
</a>
</li>
<li class="<?= $activePage == 'index' || $activePage == "" ? 'active' : ''; ?>"><a href="<?= langBaseUrl(); ?>"><?= trans("home"); ?></a></li>
<?php $totalItem = 1;
$i = 1;
if (!empty($menuLinks)):
foreach ($menuLinks as $menuItem):
if ($menuItem->item_location == "header" && $menuItem->item_parent_id == "0"):
if ($i < $menuLimit):
$subLinks = getSubMenuLinks($menuLinks, $menuItem->item_id, $menuItem->item_type);
if (!empty($subLinks)): ?>
<li class="dropdown <?= $activePage == $menuItem->item_slug ? 'active' : ''; ?>">
<a class="dropdown-toggle disabled" data-toggle="dropdown" href="<?= generateMenuItemUrl($menuItem); ?>"><?= esc($menuItem->item_name); ?><span class="caret"></span></a>
<ul class="dropdown-menu top-dropdown">
<?php foreach ($subLinks as $subItem): ?>
<li><a role="menuitem" href="<?= generateMenuItemUrl($subItem); ?>"><?= esc($subItem->item_name); ?></a></li>
<?php endforeach; ?>
</ul>
</li>
<?php else: ?>
<li class="<?= (!empty($activePage) && $activePage == $menuItem->item_slug) ? 'active' : ''; ?>"><a href="<?= generateMenuItemUrl($menuItem); ?>"><?= esc($menuItem->item_name); ?></a></li>
<?php endif;
$i++;
endif;
$totalItem++;
endif;
endforeach;
endif;
if ($totalItem > $menuLimit): ?>
<li class="dropdown">
<a class="dropdown-toggle dropdown-more" data-toggle="dropdown" href="#"><i class="icon-ellipsis-h more-sign"></i></a>
<ul class="dropdown-menu top-dropdown">
<?php
$i = 1;
if (!empty($menuLinks)):
foreach ($menuLinks as $menuItem):
if ($menuItem->item_location == "header" && $menuItem->item_parent_id == "0"):
if ($i >= $menuLimit):
$subLinks = getSubMenuLinks($menuLinks, $menuItem->item_id, $menuItem->item_type);
if (!empty($subLinks)): ?>
<li class="li-sub-dropdown">
<a class="dropdown-toggle disabled" data-toggle="dropdown" href="<?= generateMenuItemUrl($menuItem); ?>"><?= esc($menuItem->item_name); ?>&nbsp;<span class="caret"></span></a>
<ul class="dropdown-menu sub-dropdown">
<?php foreach ($subLinks as $subItem): ?>
<li><a role="menuitem" href="<?= generateMenuItemUrl($subItem); ?>"><?= esc($subItem->item_name); ?></a></li>
<?php endforeach; ?>
</ul>
</li>
<?php else: ?>
<li><a href="<?= generateMenuItemUrl($menuItem); ?>"><?= esc($menuItem->item_name); ?></a></li>
<?php endif;
endif;
$i++;
endif;
endforeach;
endif; ?>
</ul>
</li>
<?php endif; ?>
<li class="li-search"><button type="button" id="search_button" class="search-icon button-link" aria-label="search"><i class="icon-search"></i></button></li>
</ul>
</div>
</div>
</div>