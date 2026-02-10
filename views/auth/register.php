<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <h1>Register</h1>
            <form method="POST" action="<?php echo Helper::url('register'); ?>">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
            <p class="auth-link">Already have an account? <a href="<?php echo Helper::url('login'); ?>">Login here</a></p>
        </div>
    </div>
</div>

