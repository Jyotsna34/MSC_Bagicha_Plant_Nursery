/**
 * Bagicha 2.0 - Admin Chart.js Analytics Controller
 */

const AdminCharts = {
  instances: {},

  isDarkMode() {
    return document.documentElement.getAttribute('data-bs-theme') !== 'light';
  },

  renderRevenueChart(canvasId, monthlyData) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    if (this.instances[canvasId]) {
      this.instances[canvasId].destroy();
    }

    const isDark = this.isDarkMode();
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
    const textColor = isDark ? '#94A3B8' : '#64748B';

    const labels = monthlyData && monthlyData.length > 0 ? monthlyData.map(d => d.month) : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
    const values = monthlyData && monthlyData.length > 0 ? monthlyData.map(d => parseFloat(d.revenue)) : [12000, 19000, 15000, 28000, 24000, 35000, 42000];

    this.instances[canvasId] = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Monthly Revenue (₹)',
          data: values,
          borderColor: '#10B981',
          backgroundColor: 'rgba(16, 185, 129, 0.12)',
          fill: true,
          tension: 0.4,
          borderWidth: 3,
          pointBackgroundColor: '#10B981',
          pointRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: (context) => ' Revenue: ₹' + (typeof context.raw === 'number' ? context.raw.toLocaleString() : context.raw)
            }
          }
        },
        scales: {
          x: { grid: { color: gridColor }, ticks: { color: textColor } },
          y: { grid: { color: gridColor }, ticks: { color: textColor, callback: (v) => '₹' + v } }
        }
      }
    });
  },

  renderCategoryChart(canvasId, categoryData) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    if (this.instances[canvasId]) {
      this.instances[canvasId].destroy();
    }

    const isDark = this.isDarkMode();
    const textColor = isDark ? '#94A3B8' : '#64748B';

    const labels = categoryData && categoryData.length > 0 ? categoryData.map(c => c.category_name) : ['Indoor Plants', 'Outdoor Plants', 'Succulents', 'Tools', 'Seeds'];
    const values = categoryData && categoryData.length > 0 ? categoryData.map(c => parseFloat(c.category_revenue)) : [45, 25, 15, 10, 5];

    this.instances[canvasId] = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: values,
          backgroundColor: ['#10B981', '#3B82F6', '#F97316', '#A855F7', '#EC4899'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: { color: textColor, padding: 15 }
          }
        },
        cutout: '70%'
      }
    });
  }
};
