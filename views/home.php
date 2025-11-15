<div class="row">
  <div class="col-md-8">
    <h3>Catálogo</h3>
    <div class="mb-3">
      <input id="search" class="form-control search-input" placeholder="Buscar por título, autor o ISBN">
    </div>
    <div id="results" class="row gy-3">
      <?php foreach ($books as $b): ?>
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?= e($b['title']) ?></h5>
              <p class="card-text"><?= e($b['authors']) ?></p>
              <p class="card-text"><small>Disponibles: <?= e($b['copies_available']) ?></small></p>
              <form method="post" action="/loans/create" class="d-inline loan-form">
                <input type="hidden" name="_csrf" value="<?= e(\App\Core\Csrf::generate()) ?>">
                <input type="hidden" name="book_id" value="<?= e($b['id']) ?>">
                <input type="hidden" name="user_id" value="1">
                <button class="btn btn-sm btn-primary" <?= $b['copies_available']<=0 ? 'disabled' : ''; ?>>Prestar</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="col-md-4">
    <h4>Panel</h4>
    <canvas id="loansChart" style="max-width:100%"></canvas>
  </div>
</div>
<script src="/assets/js/search.js"></script>
<script src="/assets/js/dashboard.js"></script>
