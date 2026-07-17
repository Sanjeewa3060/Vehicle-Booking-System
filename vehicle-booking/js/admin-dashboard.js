checkAuth();

// Check if user is admin
function checkAdminAccess() {
  const userData = localStorage.getItem("user_data");
  if (!userData) {
    window.location.href = "login.html";
    return false;
  }

  const user = JSON.parse(userData);
  if (user.role !== "admin") {
    alert("Access denied: Admin only");
    window.location.href = "user-dashboard.html";
    return false;
  }
  return true;
}

if (!checkAdminAccess()) {
  throw new Error("Unauthorized access");
}

// Load admin stats
async function loadAdminStats() {
  try {
    const result = await apiCall("/admin/dashboard-stats.php");

    if (result.success) {
      document.getElementById("totalVehicles").textContent =
        result.data.total_vehicles;
      document.getElementById("totalBookings").textContent =
        result.data.total_bookings;
      document.getElementById("totalUsers").textContent =
        result.data.total_users;
    }
  } catch (error) {
    console.error("Failed to load admin stats:", error);
  }
}

window.addEventListener("DOMContentLoaded", loadAdminStats);
