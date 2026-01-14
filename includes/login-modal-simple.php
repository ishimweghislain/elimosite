<?php
// This file now only contains the modal HTML and JavaScript
// Login logic is handled by login-handler.php
?>
<div class="modal fade" id="login-register-modal" tabindex="-1" role="dialog" aria-labelledby="login-register-modal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mxw-571" role="document">
    <div class="modal-content">
      <div class="modal-header border-0 p-0">
        <div class="nav nav-tabs row w-100 no-gutters" id="myTab" role="tablist">
          <div class="nav-item col-sm-12 ml-0 d-flex align-items-center justify-content-between">
            <h5 class="modal-title fs-18 pl-9">Sign In</h5>
            <button type="button" class="close m-0 fs-23" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        </div>
      </div>
      <div class="modal-body p-4 py-sm-7 px-sm-8">
        <div id="login-message"></div>
        <form id="login-form">
          <div class="form-group mb-4">
            <label for="username" class="sr-only">Username</label>
            <div class="input-group input-group-lg">
              <div class="input-group-prepend">
                <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                  <i class="far fa-user"></i>
                </span>
              </div>
              <input type="text" class="form-control border-0 shadow-none fs-13" id="username" name="username" required placeholder="Username / Your email">
            </div>
          </div>
          <div class="form-group mb-4">
            <label for="password" class="sr-only">Password</label>
            <div class="input-group input-group-lg">
              <div class="input-group-prepend">
                <span class="input-group-text bg-gray-01 border-0 text-muted fs-18">
                  <i class="far fa-lock"></i>
                </span>
              </div>
              <input type="password" class="form-control border-0 shadow-none fs-13" id="password" name="password" required placeholder="Password">
              <div class="input-group-append">
                <span class="input-group-text bg-gray-01 border-0 text-body fs-18">
                  <i class="far fa-eye-slash"></i>
                </span>
              </div>
            </div>
          </div>
          <div class="d-flex mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="" id="remember-me" name="remember-me">
              <label class="form-check-label" for="remember-me">Remember me</label>
            </div>
            <a href="#" class="d-inline-block ml-auto text-orange fs-15">Lost password?</a>
          </div>
          <button type="submit" class="btn btn-primary btn-lg btn-block" id="login-btn">Sign In</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('login-form');
    const loginBtn = document.getElementById('login-btn');
    const loginMessage = document.getElementById('login-message');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const originalText = loginBtn.textContent;
            loginBtn.disabled = true;
            loginBtn.textContent = 'Signing in...';
            loginMessage.innerHTML = '';
            
            // Get form data
            const formData = new FormData(loginForm);
            formData.append('ajax_login', '1');
            
            // Send AJAX request to separate endpoint
            fetch('login-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text().then(text => {
                    console.log('Response text:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        throw new Error('Invalid response from server');
                    }
                });
            })
            .then(data => {
                console.log('Parsed data:', data);
                if (data.success) {
                    // Show success message
                    loginMessage.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                    
                    // Redirect after short delay
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    // Show error message
                    loginMessage.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                    
                    // Reset button
                    loginBtn.disabled = false;
                    loginBtn.textContent = originalText;
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                loginMessage.innerHTML = '<div class="alert alert-danger">Network error: ' + error.message + '</div>';
                loginBtn.disabled = false;
                loginBtn.textContent = originalText;
            });
        });
    }
});
</script>
