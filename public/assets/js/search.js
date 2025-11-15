document.addEventListener('DOMContentLoaded', function(){
  const input = document.getElementById('search');
  const results = document.getElementById('results');
  let timeout = null;
  input.addEventListener('input', function(){
    clearTimeout(timeout);
    timeout = setTimeout(()=> {
      const q = input.value.trim();
      fetch('/proyecto_final/library_system_php_fixed/public/books/search?q='+encodeURIComponent(q))
        .then(r=>r.json())
        .then(data=>{
          if (!Array.isArray(data)) data = [];
          results.innerHTML = '<p class="text-center text-muted">No se encontraron resultados.</p>';
          data.forEach(b=>{
            const div = document.createElement('div');
            div.className='col-md-6';
            div.innerHTML = `<div class="card"><div class="card-body"><h5 class="card-title">${escapeHtml(b.title)}</h5><p class="card-text">${escapeHtml(b.authors||'')}</p><p class="card-text"><small>Disponibles: ${b.copies_available}</small></p></div></div>`;
            results.appendChild(div);
          });
        }).catch(err=>{
          console.error(err);
        });
    }, 300);
  });
  function escapeHtml(s){ return String(s).replace(/[&<>\"']/g, function(m){return {'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;',"'":'&#039;'}[m];});}
});
