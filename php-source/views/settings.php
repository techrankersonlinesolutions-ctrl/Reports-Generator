<?php
require_once __DIR__ . '/layout/header.php';
?>

<!-- Header -->
<div class="row align-items-center mb-5">
    <div class="col-md-8">
        <h1 class="display-6 font-display mb-1">Global Settings</h1>
        <p class="text-muted-custom mb-0">Configure default values pre-filled into new reports, color themes, and printable PDF margin widths.</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <form action="index.php?route=settings" method="POST">
            <!-- CSRF -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="row">
                <!-- Card 1: Default Client Metadata -->
                <div class="col-md-6 mb-4">
                    <div class="card bg-card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-white font-display"><i class="bi bi-file-earmark-diff me-2 text-muted-custom"></i>New Report Pre-fills</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="default_company_name" class="form-label">Default Agency Name</label>
                                <input type="text" class="form-control" id="default_company_name" name="default_company_name" value="<?php echo htmlspecialchars($settings['default_company_name'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="default_email" class="form-label">Default Corporate Email</label>
                                <input type="email" class="form-control" id="default_email" name="default_email" value="<?php echo htmlspecialchars($settings['default_email'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="default_phone" class="form-label">Default Support Telephone</label>
                                <input type="text" class="form-control" id="default_phone" name="default_phone" value="<?php echo htmlspecialchars($settings['default_phone'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="default_website" class="form-label">Default Agency Website</label>
                                <input type="text" class="form-control" id="default_website" name="default_website" value="<?php echo htmlspecialchars($settings['default_website'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="default_footer" class="form-label">Default PDF Footer Disclaimer</label>
                                <textarea class="form-control" id="default_footer" name="default_footer" rows="3" required><?php echo htmlspecialchars($settings['default_footer'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Margins & Themes -->
                <div class="col-md-6 mb-4">
                    <div class="card bg-card h-100">
                        <div class="card-header">
                            <h5 class="mb-0 text-white font-display"><i class="bi bi-palette me-2 text-muted-custom"></i>PDF Printable Styling</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted-custom mb-4" style="font-size: 11px;">Configure PDF margins (in millimeters) and theme color hex values matching your branding.</p>
                            
                            <h6 class="text-uppercase text-white border-bottom border-dark pb-2 mb-3" style="font-size: 11px; letter-spacing: 0.1em;">Print Margins (mm)</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label for="pdf_margin_top" class="form-label">Top Margin</label>
                                    <input type="number" class="form-control" id="pdf_margin_top" name="pdf_margin_top" value="<?php echo intval($settings['pdf_margin_top'] ?? 15); ?>" required>
                                </div>
                                <div class="col-6">
                                    <label for="pdf_margin_bottom" class="form-label">Bottom Margin</label>
                                    <input type="number" class="form-control" id="pdf_margin_bottom" name="pdf_margin_bottom" value="<?php echo intval($settings['pdf_margin_bottom'] ?? 15); ?>" required>
                                </div>
                                <div class="col-6">
                                    <label for="pdf_margin_left" class="form-label">Left Margin</label>
                                    <input type="number" class="form-control" id="pdf_margin_left" name="pdf_margin_left" value="<?php echo intval($settings['pdf_margin_left'] ?? 15); ?>" required>
                                </div>
                                <div class="col-6">
                                    <label for="pdf_margin_right" class="form-label">Right Margin</label>
                                    <input type="number" class="form-control" id="pdf_margin_right" name="pdf_margin_right" value="<?php echo intval($settings['pdf_margin_right'] ?? 15); ?>" required>
                                </div>
                            </div>

                            <h6 class="text-uppercase text-white border-bottom border-dark pb-2 mb-3" style="font-size: 11px; letter-spacing: 0.1em;">Color Palette Presets</h6>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label for="primary_color" class="form-label">Primary Color Accent</label>
                                    <div class="d-flex gap-2">
                                        <input type="color" class="form-control-color border-0 rounded" id="primary_color_picker" value="<?php echo htmlspecialchars($settings['primary_color'] ?? '#CFFE1C'); ?>" oninput="document.getElementById('primary_color').value = this.value">
                                        <input type="text" class="form-control font-mono text-center" id="primary_color" name="primary_color" value="<?php echo htmlspecialchars($settings['primary_color'] ?? '#CFFE1C'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="secondary_color" class="form-label">Secondary Color Dark</label>
                                    <div class="d-flex gap-2">
                                        <input type="color" class="form-control-color border-0 rounded" id="secondary_color_picker" value="<?php echo htmlspecialchars($settings['secondary_color'] ?? '#141414'); ?>" oninput="document.getElementById('secondary_color').value = this.value">
                                        <input type="text" class="form-control font-mono text-center" id="secondary_color" name="secondary_color" value="<?php echo htmlspecialchars($settings['secondary_color'] ?? '#141414'); ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action footer -->
            <div class="d-flex justify-content-end mt-4">
                <a href="index.php?route=dashboard" class="btn btn-outline-light me-3">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save-fill me-2"></i>Save Global Settings</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>
