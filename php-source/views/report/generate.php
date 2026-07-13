<?php
require_once __DIR__ . '/../layout/header.php';

$isEdit = !empty($report['id']);
$formAction = "index.php?route=save";
?>

<!-- Form Container -->
<div class="card bg-card mb-5">
    <div class="card-header py-4 border-bottom border-dark">
        <h3 class="mb-1 display-font text-white">
            <?php echo $isEdit ? 'Edit Report' : 'Generate New Performance Report'; ?>
        </h3>
        <p class="text-muted-custom mb-0">Follow the 8-step wizard below to configure all metrics, charts, rank grids, and custom link lists.</p>
    </div>
    
    <div class="card-body">
        
        <!-- Step Indicator Nodes -->
        <div class="step-indicator" id="stepIndicator">
            <div class="step-node active" onclick="goToStep(1)">
                <div class="step-circle">1</div>
                <div class="step-label">Basic Info</div>
            </div>
            <div class="step-node" onclick="goToStep(2)">
                <div class="step-circle">2</div>
                <div class="step-label">Performance</div>
            </div>
            <div class="step-node" onclick="goToStep(3)">
                <div class="step-circle">3</div>
                <div class="step-label">Keywords</div>
            </div>
            <div class="step-node" onclick="goToStep(4)">
                <div class="step-circle">4</div>
                <div class="step-label">Rank Grid</div>
            </div>
            <div class="step-node" onclick="goToStep(5)">
                <div class="step-circle">5</div>
                <div class="step-label">Backlinks</div>
            </div>
            <div class="step-node" onclick="goToStep(6)">
                <div class="step-circle">6</div>
                <div class="step-label">Geo Fence</div>
            </div>
            <div class="step-node" onclick="goToStep(7)">
                <div class="step-circle">7</div>
                <div class="step-label">Action Plan</div>
            </div>
            <div class="step-node" onclick="goToStep(8)">
                <div class="step-circle">8</div>
                <div class="step-label">Company</div>
            </div>
        </div>

        <!-- Master Form -->
        <form action="<?php echo $formAction; ?>" method="POST" enctype="multipart/form-data" id="reportForm">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <!-- Report DB ID (if edit) -->
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?php echo $report['id']; ?>">
                <input type="hidden" name="existing_business_logo" value="<?php echo htmlspecialchars($report['business_logo']); ?>">
                <input type="hidden" name="existing_cover_image" value="<?php echo htmlspecialchars($report['cover_image']); ?>">
                <input type="hidden" name="existing_heatmap_image" value="<?php echo htmlspecialchars($report['heatmap_image']); ?>">
            <?php endif; ?>

            <!-- ================= STEP 1: BASIC INFO ================= -->
            <div class="wizard-step" id="step-1">
                <h4 class="mb-4 display-font text-white border-bottom border-dark pb-2 text-uppercase" style="font-size: 14px; letter-spacing: 0.05em; color: var(--primary-color) !important;">Step 1: Basic Information</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="business_name" class="form-label">Business Name *</label>
                        <input type="text" class="form-control" id="business_name" name="business_name" required value="<?php echo htmlspecialchars($report['business_name']); ?>" placeholder="e.g. Apex Gym & Fitness">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="report_month" class="form-label">Report Month *</label>
                        <select class="form-select" id="report_month" name="report_month" required>
                            <?php 
                            $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                            foreach ($months as $m) {
                                $selected = ($report['report_month'] === $m) ? 'selected' : '';
                                echo "<option value='$m' $selected>$m</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="report_year" class="form-label">Report Year *</label>
                        <select class="form-select" id="report_year" name="report_year" required>
                            <?php 
                            $currYear = intval(date('Y'));
                            for ($y = $currYear - 5; $y <= $currYear + 5; $y++) {
                                $selected = (strval($report['report_year']) === strval($y)) ? 'selected' : '';
                                echo "<option value='$y' $selected>$y</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4 mb-3">
                        <label for="generated_date" class="form-label">Generated Date *</label>
                        <input type="date" class="form-control" id="generated_date" name="generated_date" required value="<?php echo htmlspecialchars($report['generated_date']); ?>">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="business_logo" class="form-label">Business Logo</label>
                        <input type="file" class="form-control" id="business_logo" name="business_logo" accept="image/*">
                        <?php if (!empty($report['business_logo'])): ?>
                            <div class="mt-2 text-success" style="font-size: 11px;">
                                <i class="bi bi-image-fill me-1"></i> Current Logo: <?php echo htmlspecialchars($report['business_logo']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="cover_image" class="form-label">Custom Cover Image</label>
                        <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                        <?php if (!empty($report['cover_image'])): ?>
                            <div class="mt-2 text-success" style="font-size: 11px;">
                                <i class="bi bi-image-fill me-1"></i> Current Cover: <?php echo htmlspecialchars($report['cover_image']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ================= STEP 2: PERFORMANCE SUMMARY ================= -->
            <div class="wizard-step d-none" id="step-2">
                <h4 class="mb-4 display-font text-white border-bottom border-dark pb-2 text-uppercase" style="font-size: 14px; letter-spacing: 0.05em; color: var(--primary-color) !important;">Step 2: GMB Performance Summary</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="people_viewed" class="form-label">People Viewed Business Profile</label>
                        <input type="number" class="form-control" id="people_viewed" name="people_viewed" value="<?php echo intval($report['people_viewed']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="search_direct" class="form-label">Direct Search Queries</label>
                        <input type="number" class="form-control" id="search_direct" name="search_direct" value="<?php echo intval($report['search_direct'] ?? 0); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="search_discovery" class="form-label">Discovery Search Queries</label>
                        <input type="number" class="form-control" id="search_discovery" name="search_discovery" value="<?php echo intval($report['search_discovery'] ?? 0); ?>">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4 mb-3">
                        <label for="profile_interactions" class="form-label">Business Profile Interactions</label>
                        <input type="number" class="form-control" id="profile_interactions" name="profile_interactions" value="<?php echo intval($report['profile_interactions']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="reviews_count" class="form-label">Total Reviews Received</label>
                        <input type="number" class="form-control" id="reviews_count" name="reviews_count" value="<?php echo intval($report['reviews_count']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="rating_average" class="form-label">Average Profile Rating (e.g. 4.8)</label>
                        <input type="number" step="0.1" max="5" class="form-control" id="rating_average" name="rating_average" value="<?php echo floatval($report['rating_average']); ?>">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <label for="views_maps" class="form-label">Views on Google Maps</label>
                        <input type="number" class="form-control" id="views_maps" name="views_maps" value="<?php echo intval($report['views_maps']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="views_search" class="form-label">Views on Google Search</label>
                        <input type="number" class="form-control" id="views_search" name="views_search" value="<?php echo intval($report['views_search']); ?>">
                    </div>
                </div>
            </div>

            <!-- ================= STEP 3: KEYWORD RANKING ================= -->
            <div class="wizard-step d-none" id="step-3">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-dark pb-2">
                    <h4 class="mb-0 display-font text-white text-uppercase" style="font-size: 14px; letter-spacing: 0.05em; color: var(--primary-color) !important;">Step 3: Keyword Rankings</h4>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addKeywordRow()">
                        <i class="bi bi-plus-circle me-1"></i> Add Keyword Row
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-hover border border-dark" id="keywordsTable">
                        <thead>
                            <tr class="text-muted" style="font-size: 11px; text-transform: uppercase;">
                                <th style="width: 50%;">Keyword Target *</th>
                                <th style="width: 20%;" class="text-center">Previous Rank *</th>
                                <th style="width: 20%;" class="text-center">Current Rank *</th>
                                <th style="width: 10%;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="keywordsContainer">
                            <?php if (empty($report['keywords'])): ?>
                                <!-- Starter empty row -->
                                <tr>
                                    <td><input type="text" class="form-control form-control-sm" name="keywords_kw[]" placeholder="e.g. dentist near me" required></td>
                                    <td><input type="number" class="form-control form-control-sm text-center" name="keywords_prev[]" min="1" value="10" required></td>
                                    <td><input type="number" class="form-control form-control-sm text-center" name="keywords_curr[]" min="1" value="7" required></td>
                                    <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRow(this)"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($report['keywords'] as $kw): ?>
                                    <tr>
                                        <td><input type="text" class="form-control form-control-sm" name="keywords_kw[]" value="<?php echo htmlspecialchars($kw['keyword']); ?>" required></td>
                                        <td><input type="number" class="form-control form-control-sm text-center" name="keywords_prev[]" min="1" value="<?php echo intval($kw['prev_rank']); ?>" required></td>
                                        <td><input type="number" class="form-control form-control-sm text-center" name="keywords_curr[]" min="1" value="<?php echo intval($kw['curr_rank']); ?>" required></td>
                                        <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRow(this)"><i class="bi bi-trash"></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ================= STEP 4: LOCAL RANKING GRID ================= -->
            <div class="wizard-step d-none" id="step-4">
                <h4 class="mb-4 display-font text-white border-bottom border-dark pb-2 text-uppercase" style="font-size: 14px; letter-spacing: 0.05em; color: var(--primary-color) !important;">Step 4: Local Ranking Heatmap Grid</h4>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="heatmap_image" class="form-label">Upload Heatmap Screenshot (Grid 7x7 or 3x3)</label>
                        <input type="file" class="form-control" id="heatmap_image" name="heatmap_image" accept="image/*">
                        <?php if (!empty($report['heatmap_image'])): ?>
                            <div class="mt-2 text-success" style="font-size: 11px;">
                                <i class="bi bi-image-fill me-1"></i> Current Grid: <?php echo htmlspecialchars($report['heatmap_image']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="avg_rank" class="form-label">Average Rank (e.g. 2.40)</label>
                        <input type="number" step="0.01" class="form-control" id="avg_rank" name="avg_rank" value="<?php echo floatval($report['avg_rank']); ?>">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <label for="top_3_percentage" class="form-label">Top 3 Rank Percentage (%)</label>
                        <input type="number" step="0.1" class="form-control" id="top_3_percentage" name="top_3_percentage" value="<?php echo floatval($report['top_3_percentage']); ?>" placeholder="e.g. 85.0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="points_tracked" class="form-label">Grid Points Tracked</label>
                        <input type="number" class="form-control" id="points_tracked" name="points_tracked" value="<?php echo intval($report['points_tracked'] ?: 49); ?>">
                    </div>
                </div>

                <div class="mt-3">
                    <label for="insight_text" class="form-label">Local Ranking Grid Insights</label>
                    <textarea class="form-control" id="insight_text" name="insight_text" rows="4" placeholder="Explain the heatmap improvements or notes for this client..."><?php echo htmlspecialchars($report['insight_text']); ?></textarea>
                </div>
            </div>

            <!-- ================= STEP 5: BACKLINK REPORT ================= -->
            <div class="wizard-step d-none" id="step-5">
                <h4 class="mb-2 display-font text-white border-bottom border-dark pb-2 text-uppercase" style="font-size: 14px; letter-spacing: 0.05em; color: var(--primary-color) !important;">Step 5: Dynamic Link Building Reports</h4>
                <p class="text-muted-custom mb-4" style="font-size: 12px;">Create dynamic lists of URLs built for each backlink catalog. Leave empty if a category is not included this month.</p>

                <?php
                // Sub-category mapping
                $backlink_categories = [
                    'business_listings' => 'Business Listings (Citations)',
                    'profile_creations' => 'Profile Creations',
                    'web_2' => 'Web 2.0 Links',
                    'blogs' => 'Blogs',
                    'google_stacking' => 'Google Stacking Folders',
                    'stacking_properties' => 'Google Stacking Properties',
                    'guest_posting' => 'Guest Posting'
                ];

                foreach ($backlink_categories as $key => $label):
                    // Extract links belonging to this category
                    $catLinks = array_filter($report['backlinks'], function($bl) use ($key) {
                        return $bl['category'] === $key;
                    });
                ?>
                    <div class="border border-dark p-3 rounded-3 mb-4" style="background-color: #1a1a1a;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 text-white font-display" style="font-size: 13px;"><i class="bi bi-link-45deg me-1 text-primary"></i> <?php echo $label; ?></h5>
                            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" onclick="addBacklinkRow('<?php echo $key; ?>')">
                                <i class="bi bi-plus"></i> Add Row
                            </button>
                        </div>

                        <table class="table table-dark table-sm border border-dark mb-0">
                            <thead>
                                <tr style="font-size: 10px; text-transform: uppercase;" class="text-muted">
                                    <th style="width: 70%;">URL Address</th>
                                    <th style="width: 20%;" class="text-center">Status</th>
                                    <th style="width: 10%;" class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="backlinks-container-<?php echo $key; ?>">
                                <?php if (empty($catLinks)): ?>
                                    <!-- No row, but standard empty first row for quick usability -->
                                    <tr class="empty-placeholder-row">
                                        <td colspan="3" class="text-center py-2 text-muted-custom" style="font-size: 11px;">No URLs listed. Click "Add Row" to populate list.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($catLinks as $link): ?>
                                        <tr>
                                            <td>
                                                <input type="url" class="form-control form-control-sm" name="<?php echo $key; ?>_url[]" value="<?php echo htmlspecialchars($link['url']); ?>" required placeholder="https://example.com/listing">
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm text-center" name="<?php echo $key; ?>_status[]">
                                                    <option value="Active" <?php echo $link['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                                                    <option value="Pending" <?php echo $link['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                </select>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRow(this)"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- ================= STEP 6: GEO FENCE ================= -->
            <div class="wizard-step d-none" id="step-6">
                <h4 class="mb-4 display-font text-white border-bottom border-dark pb-2 text-uppercase" style="font-size: 14px; letter-spacing: 0.05em; color: var(--primary-color) !important;">Step 6: Geo Fence Mapping</h4>
                <div class="mb-3">
                    <label for="geofence_map_url" class="form-label">Google My Maps / Geo Fence Share URL or Embed Iframe Code</label>
                    <textarea class="form-control font-mono" style="font-size: 12px;" id="geofence_map_url" name="geofence_map_url" rows="4" placeholder="Paste standard URL (https://maps.google.com/...) or raw <iframe src='...'></iframe> code..."><?php echo htmlspecialchars($report['geofence_map_url']); ?></textarea>
                    <p class="text-muted-custom mt-2" style="font-size: 11px;">We automatically parse the iframe target or standard URL. This geo fence grid highlights localized coordinate citations.</p>
                </div>
            </div>

            <!-- ================= STEP 7: ACTION PLAN ================= -->
            <div class="wizard-step d-none" id="step-7">
                <h4 class="mb-4 display-font text-white border-bottom border-dark pb-2 text-uppercase" style="font-size: 14px; letter-spacing: 0.05em; color: var(--primary-color) !important;">Step 7: Next Month Action Plan</h4>
                <div class="mb-3">
                    <label for="next_month_plan" class="form-label">Plan Details (Supports HTML formatting)</label>
                    <textarea class="form-control font-mono" id="next_month_plan" name="next_month_plan" rows="10" placeholder="<h3>Strategy Plan</h3><ul><li>Build citations</li></ul>"><?php echo htmlspecialchars($report['next_month_plan']); ?></textarea>
                    <p class="text-muted-custom mt-2" style="font-size: 11px;">Enter standard HTML or plain text. In production, this can connect to simple WYSIWYG editors (like Summernote or TinyMCE).</p>
                </div>
            </div>

            <!-- ================= STEP 8: THANK YOU PAGE ================= -->
            <div class="wizard-step d-none" id="step-8">
                <h4 class="mb-4 display-font text-white border-bottom border-dark pb-2 text-uppercase" style="font-size: 14px; letter-spacing: 0.05em; color: var(--primary-color) !important;">Step 8: Thank You Page & Contacts</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="company_email" class="form-label">Agency Email</label>
                        <input type="email" class="form-control" id="company_email" name="company_email" value="<?php echo htmlspecialchars($report['company_email']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="company_phone" class="form-label">Agency Phone</label>
                        <input type="text" class="form-control" id="company_phone" name="company_phone" value="<?php echo htmlspecialchars($report['company_phone']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="company_website" class="form-label">Agency Website</label>
                        <input type="text" class="form-control" id="company_website" name="company_website" value="<?php echo htmlspecialchars($report['company_website']); ?>">
                    </div>
                </div>

                <div class="mt-3">
                    <label for="footer_notes" class="form-label">Footer Report Note</label>
                    <input type="text" class="form-control" id="footer_notes" name="footer_notes" value="<?php echo htmlspecialchars($report['footer_notes']); ?>" placeholder="Confidential report details...">
                </div>
            </div>

            <!-- ================= FOOTER CONTROL BUTTONS ================= -->
            <div class="d-flex justify-content-between mt-5 pt-4 border-top border-dark">
                <button type="button" class="btn btn-outline-light d-none" id="prevBtn" onclick="navigateStep(-1)">
                    <i class="bi bi-chevron-left me-1"></i> Back
                </button>
                <div class="ms-auto">
                    <a href="index.php?route=history" class="btn btn-outline-danger me-2">Cancel</a>
                    <button type="button" class="btn btn-primary" id="nextBtn" onclick="navigateStep(1)">
                        Next <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                    <button type="submit" class="btn btn-primary d-none" id="submitBtn">
                        <i class="bi bi-check-circle-fill me-2"></i> Save & Generate PDF
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<!-- Step Navigation Script -->
<script>
    let currentStep = 1;
    const totalSteps = 8;

    function showStep(stepNum) {
        // Toggle Step Views
        for (let i = 1; i <= totalSteps; i++) {
            const stepEl = document.getElementById('step-' + i);
            if (stepEl) {
                if (i === stepNum) {
                    stepEl.classList.remove('d-none');
                } else {
                    stepEl.classList.add('d-none');
                }
            }
        }

        // Toggle Indicators
        const nodes = document.querySelectorAll('.step-node');
        nodes.forEach((node, index) => {
            const nodeStepNum = index + 1;
            if (nodeStepNum === stepNum) {
                node.classList.add('active');
                node.classList.remove('completed');
            } else if (nodeStepNum < stepNum) {
                node.classList.add('completed');
                node.classList.remove('active');
            } else {
                node.classList.remove('active', 'completed');
            }
        });

        // Toggle Control Buttons
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        if (stepNum === 1) {
            prevBtn.classList.add('d-none');
        } else {
            prevBtn.classList.remove('d-none');
        }

        if (stepNum === totalSteps) {
            nextBtn.classList.add('d-none');
            submitBtn.classList.remove('d-none');
        } else {
            nextBtn.classList.remove('d-none');
            submitBtn.classList.add('d-none');
        }

        currentStep = stepNum;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function navigateStep(direction) {
        const nextStep = currentStep + direction;
        
        // Basic Form Validation on Next Navigation
        if (direction === 1) {
            const currentStepEl = document.getElementById('step-' + currentStep);
            const requiredFields = currentStepEl.querySelectorAll('[required]');
            let isValid = true;
            requiredFields.forEach(f => {
                if (!f.value.trim()) {
                    f.classList.add('is-invalid');
                    isValid = false;
                } else {
                    f.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                alert('Please fill out all required fields before moving forward.');
                return;
            }
        }

        if (nextStep >= 1 && nextStep <= totalSteps) {
            showStep(nextStep);
        }
    }

    function goToStep(stepNum) {
        // Only allow clicking steps we can navigate to safely
        showStep(stepNum);
    }

    // Keyword Table Dynamic Row Appenders
    function addKeywordRow() {
        const container = document.getElementById('keywordsContainer');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" class="form-control form-control-sm" name="keywords_kw[]" placeholder="Keyword..." required></td>
            <td><input type="number" class="form-control form-control-sm text-center" name="keywords_prev[]" min="1" value="10" required></td>
            <td><input type="number" class="form-control form-control-sm text-center" name="keywords_curr[]" min="1" value="7" required></td>
            <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRow(this)"><i class="bi bi-trash"></i></button></td>
        `;
        container.appendChild(row);
    }

    // Backlink Tables Dynamic Row Appenders
    function addBacklinkRow(category) {
        const container = document.getElementById('backlinks-container-' + category);
        
        // Remove empty placeholder row if exists
        const placeholder = container.querySelector('.empty-placeholder-row');
        if (placeholder) {
            container.removeChild(placeholder);
        }

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="url" class="form-control form-control-sm" name="${category}_url[]" required placeholder="https://example.com/listing-url">
            </td>
            <td>
                <select class="form-select form-select-sm text-center" name="${category}_status[]">
                    <option value="Active">Active</option>
                    <option value="Pending">Pending</option>
                </select>
            </td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRow(this)"><i class="bi bi-trash"></i></button>
            </td>
        `;
        container.appendChild(row);
    }

    // General delete row helper
    function deleteRow(button) {
        const row = button.closest('tr');
        const tbody = row.parentNode;
        tbody.removeChild(row);

        // If tbody becomes empty, add back empty placeholder if it's a backlink container
        if (tbody.children.length === 0 && tbody.id.startsWith('backlinks-container-')) {
            const tr = document.createElement('tr');
            tr.className = 'empty-placeholder-row';
            tr.innerHTML = `<td colspan="3" class="text-center py-2 text-muted-custom" style="font-size: 11px;">No URLs listed. Click "Add Row" to populate list.</td>`;
            tbody.appendChild(tr);
        }
    }

    // Run first layout trigger
    document.addEventListener('DOMContentLoaded', () => {
        showStep(1);
    });
</script>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>
