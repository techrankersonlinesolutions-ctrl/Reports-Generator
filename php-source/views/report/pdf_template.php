<?php
/**
 * Eagle Reports - PDF Print Template Layout
 */

$primary_color = $settings['primary_color'] ?? '#CFFE1C';
$secondary_color = $settings['secondary_color'] ?? '#141414';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SEO Performance Report - <?php echo htmlspecialchars($report['business_name']); ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 13px;
            line-height: 1.5;
        }

        /* Cover Page Styling */
        .cover-page {
            page-break-after: always;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: <?php echo $secondary_color; ?>;
            color: #ffffff;
            padding: 60px;
            border-bottom: 8px solid <?php echo $primary_color; ?>;
        }

        .cover-header {
            text-align: right;
        }

        .cover-logo {
            max-height: 60px;
            max-width: 200px;
        }

        .cover-title-group {
            margin-top: 150px;
        }

        .cover-subtitle {
            font-size: 16px;
            color: <?php echo $primary_color; ?>;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .cover-title {
            font-size: 38px;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1.1;
            margin: 0 0 15px 0;
            text-transform: uppercase;
        }

        .cover-client {
            font-size: 24px;
            font-weight: 500;
            color: #ffffff;
            margin-bottom: 5px;
        }

        .cover-meta {
            font-size: 14px;
            color: #a0a0a0;
            margin-top: 5px;
        }

        .cover-footer {
            margin-top: auto;
            border-top: 1px solid #333333;
            padding-top: 20px;
            font-size: 12px;
            color: #a0a0a0;
            display: flex;
            justify-content: space-between;
        }

        /* Page Content Structure */
        .page {
            page-break-after: always;
            padding: 40px;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: <?php echo $secondary_color; ?>;
            border-bottom: 2px solid <?php echo $primary_color; ?>;
            padding-bottom: 8px;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Stat Grid */
        .stat-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-collapse: separate;
            border-spacing: 15px;
        }

        .stat-card {
            display: table-cell;
            background-color: #fcfcfc;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
            width: 33.33%;
        }

        .stat-label {
            font-size: 10px;
            color: #777777;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .stat-val {
            font-size: 24px;
            font-weight: bold;
            color: <?php echo $secondary_color; ?>;
        }

        /* Tables */
        .table-report {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .table-report th {
            background-color: #f5f5f5;
            color: #333;
            text-align: left;
            font-weight: bold;
            padding: 10px;
            border-bottom: 2px solid #ddd;
            font-size: 11px;
            text-transform: uppercase;
        }

        .table-report td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .table-report tr:nth-child(even) {
            background-color: #fafafa;
        }

        /* Ranking Grid Visual */
        .grid-visual-box {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .grid-visual-cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .grid-img {
            max-width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Alerts & Insights */
        .insight-box {
            background-color: #f7f9fa;
            border-left: 4px solid <?php echo $secondary_color; ?>;
            padding: 15px;
            margin-bottom: 25px;
            font-style: italic;
        }

        /* Plan Text formatting */
        .plan-content {
            background: #fafafa;
            border: 1px solid #eee;
            padding: 20px;
            border-radius: 6px;
        }

        .plan-content h3 {
            color: <?php echo $secondary_color; ?>;
            margin-top: 0;
            font-size: 15px;
        }

        .plan-content ul {
            padding-left: 20px;
            margin-bottom: 0;
        }

        .plan-content li {
            margin-bottom: 8px;
        }

        /* Footer Notes */
        .report-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .badge-active {
            background-color: #e6f4ea;
            color: #137333;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- ================= COVER PAGE ================= -->
    <div class="cover-page">
        <div class="cover-header">
            <!-- If logo is uploaded, show it here -->
            <?php if (!empty($report['business_logo']) && file_exists(__DIR__ . '/../../' . $report['business_logo'])): ?>
                <img src="<?php echo $report['business_logo']; ?>" class="cover-logo" alt="Logo">
            <?php endif; ?>
        </div>
        
        <div class="cover-title-group">
            <div class="cover-subtitle">Performance Evaluation</div>
            <h1 class="cover-title">SEO & Google Profile Report</h1>
            <div class="cover-client"><?php echo htmlspecialchars($report['business_name']); ?></div>
            <div class="cover-meta">Reporting Month: <?php echo htmlspecialchars($report['report_month'] . ' ' . $report['report_year']); ?></div>
            <div class="cover-meta">Generated On: <?php echo htmlspecialchars($report['generated_date']); ?></div>
        </div>

        <div class="cover-footer">
            <span>Prepared By: <?php echo htmlspecialchars($settings['default_company_name'] ?? 'Eagle Digital Agency'); ?></span>
            <span>Confidential Report</span>
        </div>
    </div>

    <!-- ================= PAGE 1: PROFILE PERFORMANCE ================= -->
    <div class="page">
        <div class="section-title">GMB Profile Performance</div>
        
        <p>This section provides a summary of interactions, clicks, and discoverability keywords for your Google Business Profile over the reporting period.</p>
        
        <!-- Stats Row -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-label">People Viewed Profile</div>
                <div class="stat-val"><?php echo number_format($report['people_viewed']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Profile Interactions</div>
                <div class="stat-val"><?php echo number_format($report['profile_interactions']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Average Rating</div>
                <div class="stat-val"><?php echo number_format($report['rating_average'], 1); ?> ★</div>
            </div>
        </div>

        <!-- Queries Table -->
        <h3 style="margin-top: 30px;">Search Discovery Analytics</h3>
        <table class="table-report">
            <thead>
                <tr>
                    <th>Search Query Category</th>
                    <th style="text-align: right;">Impressions / Volume</th>
                    <th style="text-align: right;">Share Percentage</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalSearch = $report['search_direct'] + $report['search_discovery'];
                $directPct = $totalSearch > 0 ? round(($report['search_direct'] / $totalSearch) * 100, 1) : 0;
                $discoveryPct = $totalSearch > 0 ? round(($report['search_discovery'] / $totalSearch) * 100, 1) : 0;
                ?>
                <tr>
                    <td><strong>Direct Queries:</strong> Customers who find your GMB listing by searching your business name directly.</td>
                    <td style="text-align: right;"><?php echo number_format($report['search_direct']); ?></td>
                    <td style="text-align: right;"><?php echo $directPct; ?>%</td>
                </tr>
                <tr>
                    <td><strong>Discovery Queries:</strong> Customers who find your listing searching for a generic category, product, or service (high intent).</td>
                    <td style="text-align: right;"><?php echo number_format($report['search_discovery']); ?></td>
                    <td style="text-align: right;"><?php echo $discoveryPct; ?>%</td>
                </tr>
                <tr style="font-weight: bold; background-color: #f5f5f5;">
                    <td>Total Profile Search Clicks</td>
                    <td style="text-align: right;"><?php echo number_format($totalSearch); ?></td>
                    <td style="text-align: right;">100%</td>
                </tr>
            </tbody>
        </table>

        <!-- Platform Views breakdown -->
        <h3>Google Maps vs Search Views</h3>
        <table class="table-report">
            <thead>
                <tr>
                    <th>Platform Views</th>
                    <th style="text-align: right;">Views</th>
                    <th style="text-align: right;">Platform Share</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalViews = $report['views_maps'] + $report['views_search'];
                $mapsPct = $totalViews > 0 ? round(($report['views_maps'] / $totalViews) * 100, 1) : 0;
                $searchPct = $totalViews > 0 ? round(($report['views_search'] / $totalViews) * 100, 1) : 0;
                ?>
                <tr>
                    <td>Google Maps App & Desktop Views</td>
                    <td style="text-align: right;"><?php echo number_format($report['views_maps']); ?></td>
                    <td style="text-align: right;"><?php echo $mapsPct; ?>%</td>
                </tr>
                <tr>
                    <td>Google Organic Web Search Views</td>
                    <td style="text-align: right;"><?php echo number_format($report['views_search']); ?></td>
                    <td style="text-align: right;"><?php echo $searchPct; ?>%</td>
                </tr>
                <tr style="font-weight: bold; background-color: #f5f5f5;">
                    <td>Total Google Views</td>
                    <td style="text-align: right;"><?php echo number_format($totalViews); ?></td>
                    <td style="text-align: right;">100%</td>
                </tr>
            </tbody>
        </table>

        <div class="report-footer">
            Page 1 &bull; <?php echo htmlspecialchars($report['footer_notes']); ?>
        </div>
    </div>

    <!-- ================= PAGE 2: KEYWORD RANKINGS & GRID ================= -->
    <div class="page">
        <div class="section-title">Organic & Local Map Rankings</div>
        
        <h3>Local Geolocation Ranking Grid</h3>
        <p>Our 7x7 rank grid tracks real-world positions from various coordinates within a 3-mile radius of your store front.</p>

        <div class="grid-visual-box">
            <div class="grid-visual-cell" style="width: 45%;">
                <?php if (!empty($report['heatmap_image']) && file_exists(__DIR__ . '/../../' . $report['heatmap_image'])): ?>
                    <img src="<?php echo $report['heatmap_image']; ?>" class="grid-img" alt="Local Heatmap">
                <?php else: ?>
                    <div style="background:#f0f0f0;border:1px solid #ccc;height:240px;display:flex;align-items:center;justify-content:center;text-align:center;color:#777;border-radius:4px;">
                        [Heatmap Screenshot Image]
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="grid-visual-cell" style="padding-left: 30px; width: 55%;">
                <div style="background-color: #fafafa; border: 1px solid #eee; padding: 15px; border-radius: 4px; margin-bottom: 15px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: #777;">Average Position</div>
                    <div style="font-size: 28px; font-weight: bold; color: <?php echo $secondary_color; ?>;"><?php echo number_format($report['avg_rank'], 2); ?></div>
                </div>
                
                <div style="background-color: #fafafa; border: 1px solid #eee; padding: 15px; border-radius: 4px; margin-bottom: 15px;">
                    <div style="font-size: 11px; text-transform: uppercase; color: #777;">Top 3 Green Node Share</div>
                    <div style="font-size: 28px; font-weight: bold; color: <?php echo $secondary_color; ?>;"><?php echo number_format($report['top_3_percentage'], 1); ?>%</div>
                </div>

                <div style="font-size: 11px; color: #555;">
                    <strong>Tracked Points:</strong> <?php echo intval($report['points_tracked']); ?> Coordinates<br>
                    <strong>Methodology:</strong> Standardized localized searches querying near-match customer devices.
                </div>
            </div>
        </div>

        <?php if (!empty($report['insight_text'])): ?>
            <div class="insight-box">
                <strong>Heatmap Grid Insight:</strong> "<?php echo htmlspecialchars($report['insight_text']); ?>"
            </div>
        <?php endif; ?>

        <h3>Target Keyword Movements</h3>
        <table class="table-report">
            <thead>
                <tr>
                    <th>Keyword Term</th>
                    <th style="text-align: center;">Previous Rank</th>
                    <th style="text-align: center;">Current Rank</th>
                    <th style="text-align: right;">Movement Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report['keywords'] as $kw): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($kw['keyword']); ?></strong></td>
                        <td style="text-align: center;"><?php echo intval($kw['prev_rank']); ?></td>
                        <td style="text-align: center;"><?php echo intval($kw['curr_rank']); ?></td>
                        <td style="text-align: right; font-weight: bold;">
                            <?php 
                            $diff = intval($kw['prev_rank']) - intval($kw['curr_rank']);
                            if ($diff > 0) {
                                echo "<span style='color: #137333;'>↑ Improved by " . $diff . "</span>";
                            } elseif ($diff < 0) {
                                echo "<span style='color: #c5221f;'>↓ Dropped by " . abs($diff) . "</span>";
                            } else {
                                echo "<span style='color: #777;'>● Stable</span>";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="report-footer">
            Page 2 &bull; <?php echo htmlspecialchars($report['footer_notes']); ?>
        </div>
    </div>

    <!-- ================= PAGE 3: BACKLINK REPORTS ================= -->
    <div class="page">
        <div class="section-title">Link Building & SEO Authority</div>
        
        <p>A breakdown of authority, listings, and indexing URLs generated over the monthly campaign to expand organic and local authority.</p>

        <?php 
        $backlinks_by_cat = [];
        foreach ($report['backlinks'] as $bl) {
            $backlinks_by_cat[$bl['category']][] = $bl;
        }

        $categories = [
            'business_listings' => 'Business Directory Listings (Citations)',
            'profile_creations' => 'Social Profile Creations',
            'web_2' => 'Web 2.0 Editorial Links',
            'blogs' => 'Blog Placements & Articles',
            'google_stacking' => 'Google Folder Stacking Links',
            'google_stacking_properties' => 'Google Property Optimizations',
            'guest_posting' => 'Guest Posting'
        ];

        $hasBacklinks = false;
        foreach ($categories as $key => $label):
            $links = $backlinks_by_cat[$key] ?? [];
            if (empty($links)) continue;
            $hasBacklinks = true;
        ?>
            <h4 style="margin-top: 15px; margin-bottom: 8px; color: <?php echo $secondary_color; ?>; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px;"><?php echo $label; ?></h4>
            <table class="table-report" style="margin-bottom: 15px;">
                <thead>
                    <tr>
                        <th style="width: 80%;">Destination Backlink URL</th>
                        <th style="width: 20%; text-align: right;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($links as $link): ?>
                        <tr>
                            <td style="font-size: 11px; word-break: break-all; font-family: Courier, monospace; color: #555;"><?php echo htmlspecialchars($link['url']); ?></td>
                            <td style="text-align: right;"><span class="badge-active"><?php echo htmlspecialchars($link['status']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>

        <?php if (!$hasBacklinks): ?>
            <p style="font-style: italic; color: #777;">No links built or registered during this month's campaign cycle.</p>
        <?php endif; ?>

        <div class="report-footer">
            Page 3 &bull; <?php echo htmlspecialchars($report['footer_notes']); ?>
        </div>
    </div>

    <!-- ================= PAGE 4: FUTURE OUTLOOK & CONTACTS ================= -->
    <div class="page">
        <div class="section-title">Next Month Strategy Action Plan</div>
        
        <p>Our proposed operational strategies to maintain and scale your organic rankings in the upcoming cycle.</p>

        <div class="plan-content" style="margin-bottom: 40px;">
            <?php 
            // Render rich next month plan safely or convert simple linebreaks
            if (strpos($report['next_month_plan'], '<') !== false) {
                echo $report['next_month_plan']; // Render HTML safely since it came from internal admin rich text
            } else {
                echo nl2br(htmlspecialchars($report['next_month_plan']));
            }
            ?>
        </div>

        <div class="section-title" style="margin-top: 50px;">Thank You for Your Partnership</div>
        <p>We look forward to accelerating your digital growth. If you have any questions regarding GMB or organic rank modifications, feel free to reach our analytics division.</p>
        
        <div style="background-color: #fdfdfd; border: 1px dashed #cccccc; padding: 25px; border-radius: 4px; margin-top: 20px;">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 50%; font-size: 12px; line-height: 1.6; border: none; background: none;">
                        <strong><?php echo htmlspecialchars($settings['default_company_name'] ?? 'Eagle Digital Agency'); ?></strong><br>
                        Support Email: <?php echo htmlspecialchars($report['company_email']); ?><br>
                        Support Phone: <?php echo htmlspecialchars($report['company_phone']); ?><br>
                        Corporate Site: <?php echo htmlspecialchars($report['company_website']); ?>
                    </td>
                    <td style="width: 50%; text-align: right; border: none; background: none; vertical-align: bottom; font-style: italic; color: #777; font-size: 11px;">
                        Report verified & compiled securely.<br>
                        All rights reserved &copy; <?php echo date('Y'); ?>
                    </td>
                </tr>
            </table>
        </div>

        <div class="report-footer">
            Page 4 &bull; <?php echo htmlspecialchars($report['footer_notes']); ?>
        </div>
    </div>

</body>
</html>
