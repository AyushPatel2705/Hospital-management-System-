// Live search for patients table
function liveSearch(inputId, tableId) {
  const input = document.getElementById(inputId);
  if (!input) return;
  input.addEventListener('keyup', function () {
    const val = this.value.toLowerCase();
    const rows = document.querySelectorAll('#' + tableId + ' tbody tr');
    rows.forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(val) ? '' : 'none';
    });
  });
}

// Confirm before delete
function confirmDelete(msg) {
  return confirm(msg || 'Are you sure you want to delete this record?');
}

// Auto-close alerts after 4 seconds
document.addEventListener('DOMContentLoaded', function () {
  liveSearch('searchInput', 'dataTable');

  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(a => setTimeout(() => a.style.display = 'none', 4000));

  // Set today as min date for appointment booking
  const dateInputs = document.querySelectorAll('input[type="date"]');
  dateInputs.forEach(d => {
    if (!d.value) d.min = new Date().toISOString().split('T')[0];
  });
});
