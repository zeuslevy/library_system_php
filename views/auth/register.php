<?php 
use App\Core\Csrf;
use App\Core\Session;

// Inicia sesión para manejar mensajes temporales
Session::start();
$csrfToken = Csrf::token('register');
?>



<div class="d-flex justify-content-center align-items-center" style="min-height:80vh;">
  <div class="card shadow-sm p-4" style="max-width:480px; width:100%;">
    <h4 class="text-center mb-3 text-success fw-bold">Crear nueva cuenta</h4>

    <?php if (!empty($_SESSION['register_error'])): ?>
      <div class="alert alert-danger text-center py-2">
        <?= htmlspecialchars($_SESSION['register_error']) ?>
      </div>
      <?php unset($_SESSION['register_error']); ?>
    <?php endif; ?>

    <form id="registerForm" method="POST" action="<?= $base ?>/auth/register" novalidate>
      <input type="hidden" name="_csrf" value="<?= $csrfToken ?>">

      <div class="mb-3">
        <label for="name" class="form-label">Nombre completo</label>
        <input type="text" name="name" id="name" class="form-control" required placeholder="Ej: Zeus Levy">
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" name="email" id="email" class="form-control" required placeholder="usuario@ejemplo.com">
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" name="password" id="password" class="form-control" required minlength="6" placeholder="********">
      </div>

      <div class="mb-3">
        <label for="role" class="form-label">Tipo de usuario</label>
        <select name="role" id="role" class="form-select" required>
          <option value="">Selecciona...</option>
          <option value="student">Estudiante</option>
          <option value="librarian">Bibliotecario</option>
          <option value="admin">Administrador</option>
        </select>
      </div>

      <button type="submit" class="btn btn-success w-100" id="btnRegister">Registrar cuenta</button>

      <div id="msg" class="text-center mt-3 text-muted small"></div>
    </form>

    <div class="text-center mt-3">
      <small class="text-muted">¿Ya tienes una cuenta?</small><br>
      <a href="<?= $base ?>/auth/login" class="link-primary text-decoration-none fw-semibold">Iniciar sesión</a>
    </div>
  </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', async (e) => {
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
      msg.innerText = '✅ Usuario creado con éxito. Redirigiendo...';
      msg.className = 'text-success text-center mt-3 small';
      setTimeout(() => window.location.href = '<?= $base ?>/auth/login', 1200);
    } else {
      msg.innerText = json.msg || '❌ Error al crear la cuenta';
      msg.className = 'text-danger text-center mt-3 small';
    }
  } catch (err) {
    msg.innerText = '⚠️ Error de conexión con el servidor.';
    msg.className = 'text-danger text-center mt-3 small';
  }
});
</script>
