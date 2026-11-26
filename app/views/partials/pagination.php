<?php
/**
 * Pemakaian: $paginator = Paginator::fromRequest($total);
 * lalu di view: require APP_PATH . '/views/partials/pagination.php';
 * @var \App\Core\Paginator $paginator
 */
if ($paginator->lastPage() > 1) :
    $query = $_GET;
    ?>
<nav aria-label="Paginasi">
  <ul class="pagination pagination-sm">
    <?php for ($i = 1; $i <= $paginator->lastPage(); $i++) : ?>
        <?php $query['page'] = $i; ?>
      <li class="page-item<?= $i === $paginator->page ? ' active' : '' ?>">
        <a class="page-link" href="?<?= e(http_build_query($query)) ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>
<?php endif; ?>
