<?php 
use App\Core\Session;
use App\Core\Csrf;

Session::start();
$user = Session::get('user') ?? null;
$csrfLoan = Csrf::token('loan');
?>

<div class="container py-4">

  <!-- Encabezado -->
  <div class="text-center mb-4">
    <h2 class="fw-bold text-primary">📚 Catálogo de la Biblioteca</h2>
    <p class="text-muted mb-0">Explora los libros disponibles, busca por título, autor o ISBN</p>
  </div>

  <!-- Buscador -->
  <div class="input-group mb-4 shadow-sm">
    <span class="input-group-text bg-primary text-white"><i class="bi bi-search"></i></span>
    <input type="text" id="search" class="form-control form-control-lg" placeholder="Buscar libros...">
  </div>

  <!-- Contenedor de libros -->
  <div id="bookList" class="row g-3">
    <?php if (!empty($books)): ?>
      <?php foreach ($books as $book): ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card h-100 shadow-sm border-0 rounded-3 hover-card">
            <div class="card-body d-flex flex-column justify-content-between">
              
              <!-- Información del libro -->
              <div>
                <h6 class="fw-bold mb-1 text-truncate"><?= htmlspecialchars($book['title']) ?></h6>
                <p class="text-muted mb-1 small"><strong>Autor:</strong> <?= htmlspecialchars($book['authors']) ?></p>
                <p class="text-muted mb-1 small"><strong>ISBN:</strong> <?= htmlspecialchars($book['isbn']) ?></p>
                <p class="text-muted mb-2 small"><strong>Categoría:</strong> <?= htmlspecialchars($book['category'] ?? 'Sin categoría') ?></p>
              </div>

              <!-- Disponibilidad + Botón -->
              <div class="mt-auto text-center">
                <?php if ((int)$book['copies_available'] > 0): ?>
                  <span class="badge bg-success d-block mb-2">
                    Disponible (<?= $book['copies_available'] ?> / <?= $book['copies_total'] ?>)
                  </span>

                  <?php if ($user): ?>
                    <button 
                      type="button" 
                      class="btn btn-sm btn-primary btn-prestar w-100"
                      data-book-id="<?= (int)$book['id'] ?>"
                      data-user-id="<?= (int)$user['id'] ?>"
                      data-csrf="<?= htmlspecialchars($csrfLoan, ENT_QUOTES) ?>">
                      <i class="bi bi-bookmark-plus"></i> Prestar libro
                    </button>
                  <?php else: ?>
                    <a href="<?= $base ?>/auth/login" class="btn btn-sm btn-outline-secondary w-100">
                      Inicia sesión para prestar
                    </a>
                  <?php endif; ?>

                <?php else: ?>
                  <span class="badge bg-danger d-block mb-2">Agotado</span>
                <?php endif; ?>
              </div>

            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12 text-center text-muted">No hay libros registrados en la base de datos.</div>
    <?php endif; ?>
  </div>
</div>

<!-- Estilos -->
<style>
.hover-card {
  transition: transform .2s, box-shadow .2s;
}
.hover-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}
</style>

<!-- Script AJAX -->
<script>
document.addEventListener('click', async function (e) {
  const btn = e.target.closest && e.target.closest('.btn-prestar');
  if (!btn) return;

  e.preventDefault();

  const bookId = btn.dataset.bookId;
  const userId = btn.dataset.userId;
  const csrfToken = btn.dataset.csrf;

  if (!userId || userId === "0") {
    window.location.href = '<?= $base ?>/auth/login';
    return;
  }

  if (!confirm('¿Deseas registrar el préstamo de este libro?')) return;

  btn.disabled = true;
  const originalText = btn.innerHTML;
  btn.innerHTML = '⏳ Registrando...';

  try {
    const body = new URLSearchParams({
      _csrf: csrfToken,
      user_id: userId,
      book_id: bookId
    });

    const res = await fetch('<?= $base ?>/loans/create', {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: body.toString()
    });

    const text = await res.text();
    let json;
    try { json = JSON.parse(text); } catch { throw new Error(text || 'Respuesta inválida'); }

    if (json.ok) {
      alert('✅ Préstamo registrado correctamente');
      window.location.reload();
    } else {
      alert('⚠️ ' + (json.msg || 'Error al registrar el préstamo'));
    }
  } catch (err) {
    console.error(err);
    alert('❌ Error de conexión con el servidor.');
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalText;
  }
});
</script>
