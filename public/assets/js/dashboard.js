document.addEventListener('DOMContentLoaded', function(){
  const ctx = document.getElementById('loansChart').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Ene','Feb','Mar','Abr','May','Jun'],
      datasets: [{ label: 'Préstamos', data: [5,8,6,4,9,7] }]
    },
    options: {}
  });
});
