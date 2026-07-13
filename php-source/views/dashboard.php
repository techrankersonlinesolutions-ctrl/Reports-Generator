<?php
require_once __DIR__ . '/layout/header.php';
?>

<!-- Dashboard Header -->
<div class="row align-items-center mb-5">
    <div class="col-md-8">
        <h1 class="display-6 font-display mb-1">Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
        <p class="text-muted-custom mb-0">Welcome back to the Eagle Reports Generator dashboard. Here is your monthly performance overview.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="index.php?route=generate" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Generate New Report
        </a>
    </div>
</div>

<!-- Stats Widgets -->
<div class="row mb-5">
    <!-- Stat 1: Total Reports -->
    <div class="col-md-4 mb-4 mb-md-0">
        <div class="card bg-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h5 class="card-title text-muted-custom mb-1" style="font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase;">Total Reports</h5>
                    <h2 class="display-font text-white mb-0 mt-1" style="font-size: 32px;"><?php echo $totalReports; ?></h2>
                </div>
                <div class="p-3 rounded-3" style="background-color: rgba(var(--primary-rgb), 0.1); color: var(--primary-color);">
                    <i class="bi bi-file-earmark-bar-graph fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stat 2: Generated This Month -->
    <div class="col-md-4 mb-4 mb-md-0">
        <div class="card bg-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h5 class="card-title text-muted-custom mb-1" style="font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase;">Generated in <?php echo date('F'); ?></h5>
                    <h2 class="display-font text-white mb-0 mt-1" style="font-size: 32px;"><?php echo $reportsThisMonth; ?></h2>
                </div>
                <div class="p-3 rounded-3" style="background-color: rgba(var(--primary-rgb), 0.1); color: var(--primary-color);">
                    <i class="bi bi-calendar-check fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 3: Quick Performance Indicator -->
    <div class="col-md-4">
        <div class="card bg-card h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h5 class="card-title text-muted-custom mb-1" style="font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase;">Internal Operations</h5>
                    <h2 class="display-font text-white mb-0 mt-1" style="font-size: 32px;">Active</h2>
                </div>
                <div class="p-3 rounded-3" style="background-color: rgba(25, 135, 84, 0.1); color: #198754;">
                    <i class="bi bi-shield-check fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Area -->
<div class="row">
    <!-- Recent Reports Table -->
    <div class="col-lg-8 mb-4 mb-lg-0">
        <div class="card bg-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white display-font"><i class="bi bi-clock-history me-2 text-muted-custom"></i>Recent Reports</h5>
                <a href="index.php?route=history" class="text-decoration-none btn-link" style="color: var(--primary-color); font-size: 13px;">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0" style="vertical-align: middle;">
                        <thead>
                            <tr class="text-muted" style="font-size: 11px; text-transform: uppercase; border-bottom: 1px solid var(--card-border);">
                                <th class="px-4 py-3">Business Name</th>
                                <th class="py-3">Report Month</th>
                                <th class="py-3">Report Year</th>
                                <th class="py-3">Created Date</th>
                                <th class="px-4 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentReports)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-file-earmark-plus-fill fs-2 mb-2 d-block text-muted-custom"></i>
                                        No reports created yet. Click "Generate New Report" to start.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentReports as $report): ?>
                                    <tr style="border-bottom: 1px solid var(--card-border);">
                                        <td class="px-4 py-3 font-display font-medium text-white">
                                            <?php echo htmlspecialchars($report['business_name']); ?>
                                        </td>
                                        <td class="py-3 text-muted-custom">
                                            <?php echo htmlspecialchars($report['report_month']); ?>
                                        </td>
                                        <td class="py-3 text-muted-custom">
                                            <?php echo htmlspecialchars($report['report_year']); ?>
                                        </td>
                                        <td class="py-3 text-muted-custom" style="font-family: var(--font-mono); font-size: 12px;">
                                            <?php echo date('Y-m-d', strtotime($report['created_at'])); ?>
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <div class="btn-group">
                                                <a href="index.php?route=pdf&id=<?php echo $report['id']; ?>&action=view" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" title="View PDF">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </a>
                                                <a href="index.php?route=edit&id=<?php echo $report['id']; ?>" class="btn btn-sm btn-outline-light py-1 px-2 border-0" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="index.php?route=duplicate&id=<?php echo $report['id']; ?>" class="btn btn-sm btn-outline-light py-1 px-2 border-0 text-info" title="Duplicate" onclick="return confirm('Duplicate this report? New ranks will default to previous ranks.');">
                                                    <i class="bi bi-files"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Side Card -->
    <div class="col-lg-4">
        <div class="card bg-card mb-4">
            <div class="card-header">
                <h5 class="mb-0 text-white display-font"><i class="bi bi-sliders me-2 text-muted-custom"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="index.php?route=generate" class="btn btn-outline-primary text-start p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="d-block fw-bold text-white font-display mb-1" style="font-size: 14px;">Generate New Report</span>
                            <span class="d-block text-muted-custom" style="font-size: 11px;">Create a brand new client report</span>
                        </div>
                        <i class="bi bi-arrow-right fs-4"></i>
                    </a>
                    
                    <a href="index.php?route=history" class="btn btn-outline-light text-start p-3 d-flex align-items-center justify-content-between border-dark">
                        <div>
                            <span class="d-block fw-bold text-white font-display mb-1" style="font-size: 14px;">Report History</span>
                            <span class="d-block text-muted-custom" style="font-size: 11px;">View and manage existing records</span>
                        </div>
                        <i class="bi bi-arrow-right fs-4 text-muted"></i>
                    </a>

                    <a href="index.php?route=profile" class="btn btn-outline-light text-start p-3 d-flex align-items-center justify-content-between border-dark">
                        <div>
                            <span class="d-block fw-bold text-white font-display mb-1" style="font-size: 14px;">Profile & Agency</span>
                            <span class="d-block text-muted-custom" style="font-size: 11px;">Update company details & branding</span>
                        </div>
                        <i class="bi bi-arrow-right fs-4 text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/layout/footer.php';
?>
