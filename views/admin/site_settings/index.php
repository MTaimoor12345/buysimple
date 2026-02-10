<?php $pageTitle = 'Site Settings'; ?>

<div class="admin-form-container">
    <form method="POST" action="<?php echo Helper::url('admin/site-settings'); ?>" class="admin-form">
        <div class="form-group">
            <label for="whatsapp_number">WhatsApp Number <span class="required">*</span></label>
            <input 
                type="text" 
                id="whatsapp_number" 
                name="whatsapp_number" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['whatsapp_number'] ?? '923055666185'); ?>" 
                placeholder="923055666185"
                required
            >
            <small class="form-text">Enter WhatsApp number without + sign (e.g., 923055666185)</small>
        </div>

        <div class="form-group">
            <label for="contact_email">Contact Email <span class="required">*</span></label>
            <input 
                type="email" 
                id="contact_email" 
                name="contact_email" 
                class="form-control" 
                value="<?php echo htmlspecialchars($settings['contact_email'] ?? 'info@itservices.com'); ?>" 
                placeholder="info@example.com"
                required
            >
            <small class="form-text">This email will be displayed in the footer</small>
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input 
                    type="checkbox" 
                    name="whatsapp_icon_enabled" 
                    value="1"
                    <?php echo (isset($settings['whatsapp_icon_enabled']) && $settings['whatsapp_icon_enabled'] == '1') ? 'checked' : ''; ?>
                >
                <span>Enable Floating WhatsApp Icon</span>
            </label>
            <small class="form-text">Show/hide the floating WhatsApp button on the website</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Settings
            </button>
            <a href="<?php echo Helper::url('admin/dashboard'); ?>" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

