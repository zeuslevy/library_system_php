<div class="mb-4">
  <h2 class="fw-bold">Panel de Administración</h2>
  <p class="text-muted">Resumen general del sistema</p>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card shadow-sm text-center p-3">
      <h5><?= $stats['books'] ?></h5>
      <p class="text-muted mb-0">Libros</p>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card shadow-sm text-center p-3">
      <h5><?= $stats['users'] ?></h5>
      <p class="text-muted mb-0">Usuarios</p>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card shadow-sm text-center p-3">
      <h5><?= $stats['loans'] ?></h5>
      <p class="text-muted mb-0">Préstamos activos</p>
    </div>
  </div>

  <div class="col-md-3">
    <div class="card shadow-sm text-center p-3">
      <h5><?= $stats['fines'] ?></h5>
      <p class="text-muted mb-0">Multas</p>
    </div>
  </div>
</div>

<!-- Gráfico de ejemplo -->
<canvas id="chart1" height="120"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const ctx = document.getElementById('chart1').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Libros', 'Usuarios', 'Préstamos', 'Multas'],
      datasets: [{
        label: 'Resumen',
        data: [<?= $stats['books'] ?>, <?= $stats['users'] ?>, <?= $stats['loans'] ?>, <?= $stats['fines'] ?>],
        borderWidth: 1
      }]
    },
    options: { scales: { y: { beginAtZero: true } } }
  });
});
</script>
