</div> </div> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  
    const chartElement = document.getElementById('myChart');
    if (chartElement) {
        const ctx = chartElement.getContext('2d');
        new Chart(ctx, {
            type: 'bar', 
            data: {
                labels: ['Total Pemasukan', 'Total Pengeluaran'],
                datasets: [{
                    label: 'Jumlah Rupiah',
                    data: [<?= $total_masuk ?>, <?= $total_keluar ?>],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.7)', // Hijau (Success)
                        'rgba(220, 53, 69, 0.7)'  // Merah (Danger)
                    ],
                    borderColor: ['#198754', '#dc3545'],
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
</script>
</body>
</html>