// resources/js/admin/dashboard.js
import Chart from 'chart.js/auto';

const area = document.getElementById('myAreaChart');
if (area && window.dash) {
  new Chart(area, {
    type: 'line',
    data: {
      labels: window.dash.labels,
      datasets: [{ label: 'Doanh thu (VNĐ)', data: window.dash.data, tension: 0.3 }]
    },
    options: { maintainAspectRatio: false }
  });
}

// (tuỳ bạn thêm) Biểu đồ top phim
const film = document.getElementById('filmRevenue');
if (film && window.dash) {
  new Chart(film, {
    type: 'bar',
    data: {
      labels: window.dash.topFilm.map(i => i.ten),
      datasets: [{ label: 'Doanh thu (VNĐ)', data: window.dash.topFilm.map(i => i.revenue) }]
    },
    options: { maintainAspectRatio: false }
  });
}
