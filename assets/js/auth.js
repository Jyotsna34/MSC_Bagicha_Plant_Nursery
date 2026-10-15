/**
 * Bagicha 2.0 - Auth JS Handler with Role Redirection
 */

document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');
  const logoutBtn = document.getElementById('logoutBtn');

  if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const email = document.getElementById('loginEmail').value;
      const password = document.getElementById('loginPassword').value;
      const errorDiv = document.getElementById('loginError');

      if (errorDiv) errorDiv.classList.add('d-none');

      try {
        const res = await axios.post('/auth/login.php', { email, password });
        if (res.data.status === 'success') {
          const { token, user } = res.data.data;
          
          App.setToken(token);
          App.setUser(user);
          document.cookie = `bagicha_token=${token}; path=/; max-age=604800`;
          axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

          const modal = bootstrap.Modal.getInstance(document.getElementById('authModal'));
          if (modal) modal.hide();

          if (user.role === 'admin') {
            App.showToast('Welcome Admin! Redirecting to Admin Panel...', 'success');
            setTimeout(() => {
              window.location.href = App.getBaseUrl() + '/admin/index.php';
            }, 600);
          } else {
            App.showToast('Welcome back! Opening your dashboard…');
            setTimeout(() => {
              window.location.href = App.getBaseUrl() + '/pages/profile.php';
            }, 600);
          }
        }
      } catch (err) {
        const msg = err.response?.data?.message || 'Login failed. Please check credentials.';
        if (errorDiv) {
          errorDiv.textContent = msg;
          errorDiv.classList.remove('d-none');
        } else {
          App.showToast(msg, 'error');
        }
      }
    });
  }

  if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fullName = document.getElementById('regName').value;
      const email = document.getElementById('regEmail').value;
      const phone = document.getElementById('regPhone').value;
      const password = document.getElementById('regPassword').value;
      const errorDiv = document.getElementById('registerError');

      if (errorDiv) errorDiv.classList.add('d-none');

      try {
        const res = await axios.post('/auth/register.php', {
          full_name: fullName,
          email: email,
          phone: phone,
          password: password
        });

        if (res.data.status === 'success') {
          const { token, user } = res.data.data;
          App.setToken(token);
          App.setUser(user);
          document.cookie = `bagicha_token=${token}; path=/; max-age=604800`;
          axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
          App.showToast('Account created successfully! Opening your dashboard…');
          
          const modal = bootstrap.Modal.getInstance(document.getElementById('authModal'));
          if (modal) modal.hide();

          setTimeout(() => {
            window.location.href = App.getBaseUrl() + '/pages/profile.php';
          }, 600);
        }
      } catch (err) {
        const msg = err.response?.data?.message || 'Registration failed.';
        if (errorDiv) {
          errorDiv.textContent = msg;
          errorDiv.classList.remove('d-none');
        } else {
          App.showToast(msg, 'error');
        }
      }
    });
  }

  if (logoutBtn) {
    logoutBtn.addEventListener('click', async (e) => {
      e.preventDefault();
      try {
        await axios.post('/auth/logout.php');
      } catch (err) {}
      App.removeToken();
      document.cookie = 'bagicha_token=; path=/; max-age=0';
      delete axios.defaults.headers.common['Authorization'];
      App.showToast('Logged out successfully');
      setTimeout(() => {
        window.location.href = App.getBaseUrl() + '/pages/login.php?logged_out=1';
      }, 400);
    });
  }
});

window.handleChangePassword = async function(e) {
  e.preventDefault();
  const current = document.getElementById('custCurrentPassword')?.value;
  const next = document.getElementById('custNewPassword')?.value;
  const confirm = document.getElementById('custConfirmPassword')?.value;

  if (next !== confirm) {
    App.showToast('New passwords do not match.', 'error');
    return;
  }

  try {
    const res = await axios.post('/auth/change-password.php', {
      current_password: current,
      new_password: next,
      confirm_password: confirm
    });
    if (res.data.status === 'success') {
      App.showToast(res.data.message || 'Password updated successfully!');
      document.getElementById('changePasswordForm')?.reset();
      const modalEl = document.getElementById('changePasswordModal');
      if (modalEl && typeof bootstrap !== 'undefined') {
        bootstrap.Modal.getInstance(modalEl)?.hide();
      }
    }
  } catch (err) {
    App.showToast(err.response?.data?.message || 'Failed to update password', 'error');
  }
};
