<?php
use App\Core\Session;

Session::start();
$user = Session::get('user') ?? ['name' => 'Administrador'];
?>
<div class="container py-5">
  <!-- 🧭 Encabezado -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary mb-0">
      📊 Panel de control
    </h2>
    <div>
      <span class="text-muted me-3">
        <i class="bi bi-person-circle"></i> <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['role']) ?>)
      </span>
      <form method="POST" action="<?= $base ?>/auth/logout" class="d-inline">
      </form>
    </div>
  </div>

  <!-- 📦 Tarjetas resumen -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center p-3">
        <h6 class="text-muted">Libros registrados</h6>
        <h3 class="fw-bold text-primary"><?= $totalBooks ?></h3>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center p-3">
        <h6 class="text-muted">Usuarios</h6>
        <h3 class="fw-bold text-success"><?= $totalUsers ?></h3>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center p-3">
        <h6 class="text-muted">Préstamos totales</h6>
        <h3 class="fw-bold text-info"><?= $totalLoans ?></h3>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0 text-center p-3">
        <h6 class="text-muted">Préstamos activos</h6>
        <h3 class="fw-bold text-warning"><?= $activeLoans ?></h3>
      </div>
    </div>
  </div>

  <!-- 📈 Gráfico de préstamos -->
  <div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white">
      <i class="bi bi-graph-up"></i> Préstamos mensuales (últimos 6 meses)
    </div>
    <div class="card-body">
      <canvas id="loansChart" height="120"></canvas>
    </div>
  </div>

  <!-- 📋 Sección de notas -->
  <div class="mt-4 text-center text-muted small">
    <i class="bi bi-info-circle"></i> Datos actualizados automáticamente desde la base de datos.
  </div>
</div>

<!-- 🧩 Bootstrap Icons + Chart.js -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<!-- 📊 Script del gráfico -->
<script>
const ctx = document.getElementById('loansChart').getContext('2d');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: <?= json_encode($chartLabels ?? []) ?>,
    datasets: [{
      label: 'Préstamos registrados',
      data: <?= json_encode($chartValues ?? []) ?>,
      borderColor: '#0d6efd',
      backgroundColor: 'rgba(13,110,253,0.1)',
      fill: true,
      tension: 0.3,
      pointRadius: 5,
      pointHoverRadius: 6,
      borderWidth: 2
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: { mode: 'index', intersect: false }
    },
    scales: {
      y: { beginAtZero: true, ticks: { precision: 0 } },
      x: { ticks: { color: '#555' } }
    }
  }
});
</script>
