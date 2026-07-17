function getBaseUrl() {
  const path = window.location.pathname.replace(/\/+$/, "");
  const lastSlash = path.lastIndexOf("/");
  return lastSlash > 0 ? path.substring(0, lastSlash) : "";
}

const BASE_URL = getBaseUrl();

function checkAuth() {
  const token = localStorage.getItem("auth_token");
  if (!token) {
    window.location.href = "login.html";
  }
}

function buildApiUrl(endpoint) {
  const normalizedBase = BASE_URL.replace(/\/+$/, "");
  const normalizedEndpoint = endpoint.replace(/^\/+/, "");
  return encodeURI(`${normalizedBase}/api/${normalizedEndpoint}`);
}

async function apiCall(endpoint, method = "GET", data = null) {
  const url = buildApiUrl(endpoint);

  const options = {
    method,
    headers: {
      "Content-Type": "application/json",
      Authorization: `Bearer ${localStorage.getItem("auth_token") || ""}`,
    },
  };

  if (data && method !== "GET") {
    options.body = JSON.stringify(data);
  }

  const response = await fetch(url, options);
  const responseText = await response.text();
  let result = {};

  try {
    result = responseText ? JSON.parse(responseText) : {};
  } catch (error) {
    result = { message: responseText || "Unexpected server response" };
  }

  if (!response.ok) {
    throw new Error(result.message || `HTTP ${response.status}`);
  }

  return result;
}

// Login Form Handler
document.getElementById("loginForm")?.addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);
  const data = {
    email: formData.get("email"),
    password: formData.get("password"),
  };

  try {
    const result = await apiCall("auth/login.php", "POST", data);
    if (result.success) {
      localStorage.setItem("auth_token", result.token);
      localStorage.setItem("user_data", JSON.stringify(result.user));

      // Redirect based on role
      window.location.href =
        result.user.role === "admin"
          ? "admin-dashboard.html"
          : "user-dashboard.html";
    } else {
      showAlert(result.message, "error");
    }
  } catch (error) {
    showAlert("Login failed. Check your connection.", "error");
  }
});

// Register Form Handler
document
  .getElementById("registerForm")
  ?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const password = formData.get("password");
    const confirmPassword = formData.get("confirm_password");

    if (password !== confirmPassword) {
      showAlert("Passwords do not match!", "error");
      return;
    }

    const data = {
      name: formData.get("name"),
      email: formData.get("email"),
      password: password,
    };

    try {
      const result = await apiCall("auth/register.php", "POST", data);
      if (result.success) {
        showAlert("Registration successful! Redirecting...", "success");
        setTimeout(() => {
          window.location.href = "login.html";
        }, 2000);
      } else {
        showAlert(result.message, "error");
      }
    } catch (error) {
      showAlert("Registration failed. Please try again.", "error");
    }
  });

function showAlert(message, type) {
  // Remove existing alerts first to prevent stacking
  document.querySelector(".alert")?.remove();

  const alertDiv = document.createElement("div");
  alertDiv.className = `alert alert-${type}`;
  alertDiv.textContent = message;

  const form = document.querySelector("form");
  if (form) {
    form.insertBefore(alertDiv, form.firstChild);
    setTimeout(() => alertDiv.remove(), 5000);
  }
}
