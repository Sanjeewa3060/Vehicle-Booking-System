checkAuth();

// Load user's bookings
async function loadBookings() {
  try {
    const result = await apiCall("/bookings/user-bookings.php");

    if (result.success) {
      displayBookings(result.data);
    }
  } catch (error) {
    console.error("Failed to load bookings:", error);
  }
}

// Display bookings in table
function displayBookings(bookings) {
  const tbody = document.getElementById("bookingsTableBody");
  tbody.innerHTML = "";

  if (bookings.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="6" class="text-center">No bookings found</td></tr>';
    return;
  }

  bookings.forEach((booking) => {
    const row = document.createElement("tr");

    let statusClass = "status-available";
    if (booking.status === "Completed") statusClass = "status-badge";
    if (booking.status === "Cancelled") statusClass = "status-unavailable";

    const actionButton =
      booking.status === "Active"
        ? `<button class="btn btn-danger" onclick="cancelBooking(${booking.id})">Cancel</button>`
        : "-";

    row.innerHTML = `
            <td>#${booking.id}</td>
            <td>${booking.vehicle_name}</td>
            <td>${booking.start_date}</td>
            <td>${booking.end_date}</td>
            <td><span class="status-badge ${statusClass}">${booking.status}</span></td>
            <td>${actionButton}</td>
        `;

    tbody.appendChild(row);
  });
}

// Cancel booking
async function cancelBooking(bookingId) {
  if (!confirm("Are you sure you want to cancel this booking?")) {
    return;
  }

  try {
    const result = await apiCall("/bookings/cancel.php", "POST", {
      booking_id: bookingId,
    });

    if (result.success) {
      alert("Booking cancelled successfully");
      loadBookings(); // Reload bookings
    } else {
      alert(result.message || "Failed to cancel booking");
    }
  } catch (error) {
    alert("Failed to cancel booking");
  }
}

// Load bookings on page load
window.addEventListener("DOMContentLoaded", loadBookings);
