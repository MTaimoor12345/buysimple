<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <h1>Login</h1>
            <form method="POST" action="<?php echo Helper::url('login'); ?>">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            <p class="auth-link">Don't have an account? <a href="<?php echo Helper::url('register'); ?>">Register here</a></p>
        </div>
    </div>
</div>

