window.addEventListener("DOMContentLoaded", () => {
  checkAuth();
  loadDashboardStats();
});

// Load dashboard stats
async function loadDashboardStats() {
  try {
    const result = await apiCall("/user/dashboard-stats.php");

    if (result.success) {
      document.getElementById("totalBookings").textContent =
        result.data.total_bookings;
      document.getElementById("activeBookings").textContent =
        result.data.active_bookings;
    }
  } catch (error) {
    console.error("Failed to load dashboard stats:", error);
  }
}

// Toggle sidebar for mobile
function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  sidebar.classList.toggle("active");
}

// Load stats on page load
window.addEventListener("DOMContentLoaded", loadDashboardStats);
