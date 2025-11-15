<?php
use App\Core\Session;
use App\Core\Csrf;

Session::start(); 
$user = Session::get('user');
$csrfToken = Csrf::token('loan_return');
?>

<div class="container py-4">
  <!-- Encabezado -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary">
      📖 Mis préstamos
    </h2>
    <div>
      <span class="text-muted me-3">
        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['role']) ?>)
      </span>
      <form method="POST" action="<?= $base ?>/auth/logout" class="d-inline">
      </form>
    </div>
  </div>
    <div class="text-center mb-4">
    <p class="text-muted mb-1">Consulta tus préstamos, fechas de devolución y estado actual.</p>
    </div>

  <!-- Tabla de préstamos -->
  <div class="card shadow-sm border-0 rounded-3">
    <div class="card-body">
      <?php if (!empty($myLoans)): ?>
        <div class="table-responsive">
          <table class="table align-middle table-hover">
            <thead class="table-primary">
              <tr class="text-center">
                <th>#</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Fecha de préstamo</th>
                <th>Fecha de devolución</th>
                <th>Multa (S/)</th>
                <th>Estado</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($myLoans as $i => $loan): ?>
                <?php
                  // Calcular estado dinámicamente
                  $estado = $loan['returned_at']
                    ? 'Devuelto'
                    : (strtotime($loan['return_date']) < time() ? 'Atrasado' : 'En préstamo');
                ?>
                <tr class="text-center">
                  <td><?= $i + 1 ?></td>
                  <td class="text-start"><?= htmlspecialchars($loan['title']) ?></td>
                  <td><?= htmlspecialchars($loan['authors']) ?></td>
                  <td><?= date('d/m/Y', strtotime($loan['loan_date'])) ?></td>
                  <td><?= date('d/m/Y', strtotime($loan['return_date'])) ?></td>
                  <td><?= number_format($loan['fine'], 2) ?></td>
                  <td>
                    <span class="badge 
                      <?= $estado === 'Devuelto' ? 'bg-success' : 
                         ($estado === 'Atrasado' ? 'bg-danger' : 'bg-warning text-dark') ?>">
                      <?= $estado ?>
                    </span>
                  </td>
                  <td>
                    <?php if ($estado !== 'Devuelto'): ?>
                      <form method="POST" action="<?= $base ?>/loans/return" class="returnForm d-inline-block">
                        <input type="hidden" name="_csrf" value="<?= $csrfToken ?>">
                        <input type="hidden" name="loan_id" value="<?= $loan['id'] ?? 0 ?>">
                        <button type="submit" class="btn btn-sm btn-outline-success">
                          <i class="bi bi-arrow-counterclockwise"></i> Devolver
                        </button>
                      </form>
                    <?php else: ?>
                      <button class="btn btn-sm btn-outline-secondary" disabled>
                        <i class="bi bi-check2-circle"></i> Devuelto
                      </button>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-center text-muted my-4">
          No tienes préstamos registrados actualmente.
        </p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Script AJAX -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.returnForm').forEach(form => {
    form.addEventListener('submit', async e => {
      e.preventDefault();
      if (!confirm('¿Deseas devolver este libro?')) return;

      const data = new FormData(form);
      try {
        const res = await fetch(form.action, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: new URLSearchParams(data)
        });

        const json = await res.json();
        if (json.ok) {
          alert('✅ Libro devuelto con éxito.');
          location.reload();
        } else {
          alert('⚠️ ' + (json.msg || 'Error al devolver el libro.'));
        }
      } catch {
        alert('❌ Error de conexión con el servidor.');
      }
    });
  });
});
</script>
