<?php if ($pager->getPageCount() > 1) : ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">

            <!-- PREVIOUS -->
            <?php if ($pager->hasPreviousPage()) : ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $pager->getPreviousPage() ?>" aria-label="Previous">
                        &laquo;
                    </a>
                </li>
            <?php endif ?>

            <!-- PAGE LINKS -->
            <?php foreach ($pager->links() as $link) : ?>
                <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $link['uri'] ?>">
                        <?= $link['title'] ?>
                    </a>
                </li>
            <?php endforeach ?>

            <!-- NEXT -->
            <?php if ($pager->hasNextPage()) : ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $pager->getNextPage() ?>" aria-label="Next">
                        &raquo;
                    </a>
                </li>
            <?php endif ?>

        </ul>
    </nav>
<?php endif ?>