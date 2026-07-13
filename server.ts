import express from 'express';
import path from 'path';
import fs from 'fs';
import { createServer as createViteServer } from 'vite';

const app = express();
const PORT = 3000;

app.use(express.json({ limit: '10mb' }));

// 1. Storage State (Mock Database that persists in-memory)
let staffUser = {
  id: 1,
  name: 'Marcus Sterling',
  email: 'admin@eagle.com',
  company_name: 'Eagle Digital Agency',
  company_phone: '+1 (555) 019-2831',
  company_email: 'reports@eagledigital.com',
  company_website: 'www.eagledigital.com',
  company_footer: 'Eagle Digital Agency © 2026. Confidential SEO Performance Report.'
};

let settings = {
  default_company_name: 'Eagle Digital Agency',
  default_email: 'reports@eagledigital.com',
  default_phone: '+1 (555) 019-2831',
  default_website: 'www.eagledigital.com',
  default_footer: 'Eagle Digital Agency © 2026. Confidential SEO Performance Report.',
  pdf_margin_top: 15,
  pdf_margin_bottom: 15,
  pdf_margin_left: 15,
  pdf_margin_right: 15,
  primary_color: '#CFFE1C',
  secondary_color: '#141414'
};

// Seed initial reports with high-fidelity realistic data
let reports = [
  {
    id: 'rep-9831',
    business_name: 'Apex Orthodontics & Dental Care',
    report_month: 'June',
    report_year: '2026',
    generated_date: '2026-06-30',
    business_logo: '',
    cover_image: '',
    people_viewed: 8430,
    search_direct: 2190,
    search_discovery: 6240,
    profile_interactions: 412,
    reviews_count: 18,
    rating_average: 4.9,
    views_maps: 5120,
    views_search: 3310,
    keyword_ranking: [
      { keyword: 'emergency dentist near me', prev_rank: 9, curr_rank: 3 },
      { keyword: 'invisalign dentist city center', prev_rank: 14, curr_rank: 5 },
      { keyword: 'dental implant crown quotes', prev_rank: 22, curr_rank: 11 },
      { keyword: 'family pediatric dentist reviews', prev_rank: 6, curr_rank: 6 }
    ],
    heatmap_image: '',
    avg_rank: 1.95,
    top_3_percentage: 91.2,
    points_tracked: 49,
    insight_text: 'Map citation stacks and optimization pushes corrected our green coordinates in the north-east sectors.',
    backlinks: {
      business_listings: [
        { category: 'business_listings', url: 'https://www.yelp.com/biz/apex-orthodontics-chicago', status: 'Active' },
        { category: 'business_listings', url: 'https://www.yellowpages.com/chicago/apex-orthodontics', status: 'Active' },
        { category: 'business_listings', url: 'https://www.mapquest.com/us/illinois/apex-orthodontics-98124', status: 'Active' }
      ],
      profile_creations: [
        { category: 'profile_creations', url: 'https://twitter.com/apex_ortho', status: 'Active' },
        { category: 'profile_creations', url: 'https://pinterest.com/apex_orthodontics', status: 'Active' }
      ],
      web_2: [
        { category: 'web_2', url: 'https://apexortho.wordpress.com/emergency-dental-tips', status: 'Active' },
        { category: 'web_2', url: 'https://apex-ortho.blogspot.com/2026/06/dental-wellness.html', status: 'Active' }
      ],
      blogs: [],
      google_stacking: [
        { category: 'google_stacking', url: 'https://drive.google.com/drive/folders/1aBzX981zLo1qWz', status: 'Active' }
      ],
      google_stacking_properties: [],
      guest_posting: []
    },
    geofence_map_url: '',
    next_month_plan: `<h3>July 2026 Strategy Roadmap</h3>
<ul>
  <li><strong>Authority Citations:</strong> Establish 15 additional local maps citations to protect regional dominance.</li>
  <li><strong>Web 2.0 Stacks:</strong> Expand the WordPress and Blogspot networks with high-value pediatric dentistry articles.</li>
  <li><strong>Profile Media:</strong> Tag and upload 10 additional geo-coded storefront photos to maps.</li>
</ul>`,
    company_email: 'reports@eagledigital.com',
    company_phone: '+1 (555) 019-2831',
    company_website: 'www.eagledigital.com',
    footer_notes: 'Eagle Digital Agency © 2026. Confidential SEO Performance Report.'
  }
];

// ================= API ENDPOINTS =================

// Auth login API
app.post('/api/auth/login', (req, res) => {
  const { email, password } = req.body;
  if (email === 'admin@eagle.com' && password === 'password123') {
    return res.json({ success: true, user: staffUser });
  }
  return res.status(401).json({ success: false, message: 'Invalid staff login credentials' });
});

// Update Profile API
app.post('/api/profile/update', (req, res) => {
  const { name, email, new_password, company_name, company_phone, company_email, company_website, company_footer } = req.body;
  
  staffUser = {
    ...staffUser,
    name,
    email,
    company_name,
    company_phone,
    company_email,
    company_website,
    company_footer
  };

  return res.json({ success: true, user: staffUser });
});

// Settings Fetch & Save
app.get('/api/settings', (req, res) => {
  res.json({ success: true, settings });
});

app.post('/api/settings/update', (req, res) => {
  settings = { ...settings, ...req.body };
  res.json({ success: true, settings });
});

// Reports CRUD APIs
app.get('/api/reports', (req, res) => {
  res.json({ success: true, reports });
});

app.post('/api/reports', (req, res) => {
  const newReport = {
    ...req.body,
    id: `rep-${Math.floor(1000 + Math.random() * 9000)}`
  };
  reports.unshift(newReport);
  res.json({ success: true, report: newReport });
});

app.put('/api/reports/:id', (req, res) => {
  const { id } = req.params;
  reports = reports.map(r => r.id === id ? { ...r, ...req.body } : r);
  const updated = reports.find(r => r.id === id);
  res.json({ success: true, report: updated });
});

app.delete('/api/reports/:id', (req, res) => {
  const { id } = req.params;
  reports = reports.filter(r => r.id !== id);
  res.json({ success: true });
});

// Roll-over Duplicate API (rolls ranks from previous month)
app.post('/api/reports/:id/duplicate', (req, res) => {
  const { id } = req.params;
  const original = reports.find(r => r.id === id);
  
  if (!original) {
    return res.status(404).json({ success: false, message: 'Original report not found' });
  }

  // Calculate next month rollover name
  const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
  let currentMonthIdx = months.indexOf(original.report_month);
  let nextMonthIdx = (currentMonthIdx + 1) % 12;
  let nextMonthName = months[nextMonthIdx];
  let nextYearNum = original.report_year;
  
  if (original.report_month === 'December') {
    nextYearNum = String(Number(original.report_year) + 1);
  }

  // Roll ranks: carry current rank over as the new "previous rank", setting placeholder current ranks
  const rolledKeywords = (original.keyword_ranking || []).map(kw => ({
    keyword: kw.keyword,
    prev_rank: kw.curr_rank,
    curr_rank: kw.curr_rank // Start stable, ready for staff adjustments
  }));

  const duplicatedReport = {
    ...original,
    id: `rep-${Math.floor(1000 + Math.random() * 9000)}`,
    report_month: nextMonthName,
    report_year: nextYearNum,
    generated_date: new Date().toISOString().split('T')[0],
    keyword_ranking: rolledKeywords
  };

  reports.unshift(duplicatedReport);
  res.json({ success: true, report: duplicatedReport });
});

// 2. Recursive File scanner to build the PHP Codebase viewer payload
interface CodeNode {
  name: string;
  type: 'file' | 'directory';
  path?: string;
  content?: string;
  children?: CodeNode[];
}

function scanPhpCodebase(dir: string, baseDir: string = ''): CodeNode[] {
  const results: CodeNode[] = [];
  if (!fs.existsSync(dir)) return [];
  
  const files = fs.readdirSync(dir);
  for (const f of files) {
    const fullPath = path.join(dir, f);
    const relPath = baseDir ? `${baseDir}/${f}` : f;
    const stat = fs.statSync(fullPath);

    if (stat.isDirectory()) {
      // Exclude generated files or caches
      if (f === 'uploads' || f === 'pdf' || f === 'vendor') continue;
      results.push({
        name: f,
        type: 'directory',
        children: scanPhpCodebase(fullPath, relPath)
      });
    } else {
      // Only include code, config, database or documentation files
      const ext = path.extname(f);
      if (['.php', '.sql', '.md', '.json', '.js', '.css', '.htaccess'].includes(ext)) {
        const content = fs.readFileSync(fullPath, 'utf8');
        results.push({
          name: f,
          type: 'file',
          path: relPath,
          content
        });
      }
    }
  }

  // Sort: directories first, then files alphabetically
  return results.sort((a, b) => {
    if (a.type !== b.type) {
      return a.type === 'directory' ? -1 : 1;
    }
    return a.name.localeCompare(b.name);
  });
}

// PHP codebase API
app.get('/api/php-codebase', (req, res) => {
  try {
    const phpSourcePath = path.join(process.cwd(), 'php-source');
    const files = scanPhpCodebase(phpSourcePath);
    res.json({ success: true, files });
  } catch (err: any) {
    res.status(500).json({ success: false, message: err.message || 'Failed to scan PHP source' });
  }
});

// ================= ASSETS & COMPRESSION SETUP =================

async function startServer() {
  if (process.env.NODE_ENV !== "production") {
    const vite = await createViteServer({
      server: { middlewareMode: true },
      appType: "spa",
    });
    app.use(vite.middlewares);
  } else {
    const distPath = path.join(process.cwd(), 'dist');
    app.use(express.static(distPath));
    app.get('*', (req, res) => {
      res.sendFile(path.join(distPath, 'index.html'));
    });
  }

  app.listen(PORT, "0.0.0.0", () => {
    console.log(`Express dev server active on http://localhost:${PORT}`);
  });
}

// Only start the server listener if we are NOT on Vercel
if (!process.env.VERCEL) {
  startServer();
}

export default app;
