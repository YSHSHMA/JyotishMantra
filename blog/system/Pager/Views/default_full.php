<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
if (itemCount($pager->links()) > 1): ?>
    <nav aria-label="<?= lang('Pager.pageNavigation') ?>">
        <ul class="pagination">
            <?php if ($pager->hasPrevious()) : ?>
                <li>
                    <a href="<?= $pager->getFirst() ?>" aria-label="first">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <li>
                    <a href="<?= $pager->getPreviousPage() ?>" aria-label="previous">
                        <span aria-hidden="true">&lsaquo;</span>
                    </a>
                </li>
            <?php endif ?>

            <?php foreach ($pager->links() as $link) : ?>
                <li <?= $link['active'] ? 'class="active"' : '' ?>>
                    <a href="<?= $link['uri'] ?>">
                        <?= $link['title'] ?>
                    </a>
                </li>
            <?php endforeach ?>

            <?php if ($pager->hasNext()) : ?>
                <li>
                    <a href="<?= $pager->getNextPage() ?>" aria-label="next">
                        <span aria-hidden="true">&rsaquo;</span>
                    </a>
                </li>
                <li>
                    <a href="<?= $pager->getLast() ?>" aria-label="last">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif ?>
        </ul>
    </nav>
<?php endif; ?>