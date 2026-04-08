<?php if ($pages>1) { ?>
<nav class="pagination is-centered" role="navigation" aria-label="pagination">
    <a href="?page=<?=min(1, $page-1); ?>" class="pagination-previous">předchozí</a>
    <a href="?page=<?=max($pages, $page+1); ?>"class="pagination-next">další</a>
    <ul class="pagination-list">
        <?php for ($pageNo=1; $pageNo <= $pages; $pageNo++) {
            $isCurrent = ($pageNo==$page ? 'is-current' : '');
            echo '<li><a href="?page='.$pageNo.'" class="pagination-link '.$isCurrent.'">'.$pageNo.'</a></li>';
        } ?>
    </ul>
</nav>
<?php } ?>