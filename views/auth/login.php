<?php 
use App\Core\Csrf;
use App\Core\Session;

// Inicia sesión para manejar mensajes temporales
Session::start();
$csrfToken = Csrf::token('login');
?>


<div class="d-flex justify-content-center align-items-center" style="min-height:75vh;">
  <div class="card shadow-sm p-4" style="max-width:420px; width:100%;">
    <h4 class="text-center mb-3 text-primary fw-bold">Iniciar sesión</h4>

    <?php if (!empty($_SESSION['login_error'])): ?>
      <div class="alert alert-danger text-center py-2">
        <?= htmlspecialchars($_SESSION['login_error']) ?>
      </div>
      <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>

    <!-- Formulario clásico -->
    <form id="loginForm" method="POST" action="<?= $base ?>/auth/login" novalidate>
      <input type="hidden" name="_csrf" value="<?= $csrfToken ?>">

      <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" name="email" id="email" class="form-control" required placeholder="usuario@ejemplo.com">
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" name="password" id="password" class="form-control" required placeholder="********">
      </div>

      <button type="submit" class="btn btn-primary w-100" id="btnLogin">Ingresar</button>

      <div id="msg" class="text-center mt-3 text-muted small"></div>
    </form>

    <div class="text-center mt-3">
      <small class="text-muted">¿No tienes cuenta?</small><br>
      <a href="<?= $base ?>/auth/register" class="link-success text-decoration-none fw-semibold">Crear una nueva cuenta</a>
    </div>
  </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async (e) => {
  e.preventDefault();
  const form = e.target;
  const msg = document.getElementById('msg');
  msg.innerText = '';

  const formData = new FormData(form);
  const data = Object.fromEntries(formData.entries());

  try {
    const res = await fetch(form.action, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: new URLSearchParams(data)
    });

    const text = await res.text();
    let json;

    try { json = JSON.parse(text); }
    catch { throw new Error(text || 'Respuesta inválida del servidor'); }

    if (json.ok) {
      msg.innerText = '✅ Acceso correcto. Redirigiendo...';
      msg.className = 'text-success text-center mt-3 small';
      setTimeout(() => window.location.href = '<?= $base ?>/dashboard', 1000);
    } else {
      msg.innerText = json.msg || '❌ Credenciales incorrectas';
      msg.className = 'text-danger text-center mt-3 small';
    }
  } catch (err) {
    msg.innerText = '⚠️ Error de conexión con el servidor.';
    msg.className = 'text-danger text-center mt-3 small';
  }
});
</script>

