<?php
require_once __DIR__ . '/layout/header.php';
?>

<!-- Header -->
<div class="row align-items-center mb-5">
    <div class="col-md-8">
        <h1 class="display-6 font-display mb-1">Admin Profile</h1>
        <p class="text-muted-custom mb-0">Manage your staff login credentials and custom agency details used across cover pages.</p>
    </div>
</div>

<div class="row">
    <!-- Form Columns -->
    <div class="col-lg-12">
        <form action="index.php?route=profile" method="POST">
            <!-- CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="row">
                <!-- Card 1: User Login Credentials -->
                <div class="col-md-6 mb-4">
                    <div class="card bg-card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-white font-display"><i class="bi bi-shield-lock me-2 text-muted-custom"></i>Staff Login Security</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Work Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>">
                            </div>

                            <hr class="border-dark my-4">
                            <p class="text-muted-custom mb-3" style="font-size: 11px;">Leave password fields empty if you do not wish to update your password right now.</p>

                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Min. 8 characters">
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Repeat password">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Company Info -->
                <div class="col-md-6 mb-4">
                    <div class="card bg-card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-white font-display"><i class="bi bi-building me-2 text-muted-custom"></i>Corporate Agency Branding</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="company_name" class="form-label">Agency Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo htmlspecialchars($user['company_name'] ?? ''); ?>" placeholder="e.g. Eagle Digital Agency">
                            </div>

                            <div class="mb-3">
                                <label for="company_phone" class="form-label">Branding Telephone</label>
                                <input type="text" class="form-control" id="company_phone" name="company_phone" value="<?php echo htmlspecialchars($user['company_phone'] ?? ''); ?>" placeholder="e.g. +1 (555) 019-2831">
                            </div>

                            <div class="mb-3">
                                <label for="company_email" class="form-label">Branding Support Email</label>
                                <input type="email" class="form-control" id="company_email" name="company_email" value="<?php echo htmlspecialchars($user['company_email'] ?? ''); ?>" placeholder="reports@eagledigital.com">
                            </div>

                            <div class="mb-3">
                                <label for="company_website" class="form-label">Branding Corporate Site</label>
                                <input type="text" class="form-control" id="company_website" name="company_website" value="<?php echo htmlspecialchars($user['company_website'] ?? ''); ?>" placeholder="www.eagledigital.com">
                            </div>

                            <div class="mb-3">
                                <label for="company_footer" class="form-label">Default Page Footer Tag</label>
                                <textarea class="form-control" id="company_footer" name="company_footer" rows="3" placeholder="Eagle Digital Agency © 2026. Confidential SEO Performance Report."><?php echo htmlspecialchars($user['company_footer'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Buttons -->
            <div class="d-flex justify-content-end mt-4">
                <a href="index.php?route=dashboard" class="btn btn-outline-light me-3">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save-fill me-2"></i>Save Account Changes</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/layout/footer.php';
?>
