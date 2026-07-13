<?php
require_once __DIR__ . '/../layout/header.php';
?>

<!-- Header -->
<div class="row align-items-center mb-5">
    <div class="col-md-8">
        <h1 class="display-6 font-display mb-1">Report History</h1>
        <p class="text-muted-custom mb-0">Manage all internal marketing and performance reports generated on the platform.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <a href="index.php?route=generate" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Generate New Report
        </a>
    </div>
</div>

<!-- History Card Table -->
<div class="card bg-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-white display-font"><i class="bi bi-file-earmark-bar-graph me-2 text-muted-custom"></i>All Generated Reports</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0" style="vertical-align: middle;">
                <thead>
                    <tr class="text-muted" style="font-size: 11px; text-transform: uppercase; border-bottom: 1px solid var(--card-border);">
                        <th class="px-4 py-3">Business Name</th>
                        <th class="py-3">Month</th>
                        <th class="py-3">Year</th>
                        <th class="py-3">Generated Date</th>
                        <th class="py-3">Database ID</th>
                        <th class="px-4 py-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reports)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-bar-graph-fill fs-2 mb-2 d-block text-muted-custom"></i>
                                No reports found. Click "Generate New Report" to create your first report.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reports as $r): ?>
                            <tr style="border-bottom: 1px solid var(--card-border);">
                                <td class="px-4 py-3 font-display fw-bold text-white">
                                    <?php echo htmlspecialchars($r['business_name']); ?>
                                </td>
                                <td class="py-3 text-muted-custom">
                                    <?php echo htmlspecialchars($r['report_month']); ?>
                                </td>
                                <td class="py-3 text-muted-custom">
                                    <?php echo htmlspecialchars($r['report_year']); ?>
                                </td>
                                <td class="py-3 text-muted-custom" style="font-family: var(--font-mono); font-size: 12px;">
                                    <?php echo htmlspecialchars($r['generated_date']); ?>
                                </td>
                                <td class="py-3 text-muted-custom" style="font-family: var(--font-mono); font-size: 11px;">
                                    ID-<?php echo $r['id']; ?>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="btn-group">
                                        <!-- View PDF Inline -->
                                        <a href="index.php?route=pdf&id=<?php echo $r['id']; ?>&action=view" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-3 d-flex align-items-center" title="View PDF">
                                            <i class="bi bi-file-earmark-pdf-fill me-1"></i> View
                                        </a>
                                        <!-- Download PDF -->
                                        <a href="index.php?route=pdf&id=<?php echo $r['id']; ?>&action=download" class="btn btn-sm btn-outline-light py-1 px-3 d-flex align-items-center" title="Download PDF">
                                            <i class="bi bi-download me-1"></i> PDF
                                        </a>
                                        <!-- Edit Report -->
                                        <a href="index.php?route=edit&id=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-light py-1 px-3 d-flex align-items-center" title="Edit Data">
                                            <i class="bi bi-pencil-fill me-1"></i> Edit
                                        </a>
                                        <!-- One-Click Duplicate -->
                                        <a href="index.php?route=duplicate&id=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-info py-1 px-3 d-flex align-items-center" title="Duplicate (Roll Over Month)" onclick="return confirm('Duplicate report for next month? Standard ranking variables will roll over.');">
                                            <i class="bi bi-files me-1"></i> Duplicate
                                        </a>
                                        <!-- Delete -->
                                        <a href="index.php?route=delete&id=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-danger py-1 px-3 d-flex align-items-center" title="Delete" onclick="return confirm('Are you absolutely sure you want to permanently delete this report? This cannot be undone.');">
                                            <i class="bi bi-trash-fill"></i>
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

<?php
require_once __DIR__ . '/../layout/footer.php';
?>
