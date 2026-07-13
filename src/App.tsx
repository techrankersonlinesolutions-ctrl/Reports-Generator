import React, { useState, useEffect } from 'react';
import { 
  BarChart3, FileText, Plus, History, Lock, Settings as SettingsIcon, 
  LogOut, PlusCircle, Trash2, Copy, FileSpreadsheet, Map, Check, Eye, ChevronRight, 
  ChevronLeft, LayoutGrid, Calendar, HelpCircle, ArrowRight, ShieldCheck, Download, Code,
  CheckCircle, Globe, Phone, Mail, FileCheck
} from 'lucide-react';
import { Report, Settings, User, Keyword, Backlink } from './types';
import ReportPdfViewer from './components/ReportPdfViewer';
import PhpSourceViewer from './components/PhpSourceViewer';

export default function App() {
  const [activeTab, setActiveTab] = useState<'dashboard' | 'generate' | 'history' | 'profile' | 'settings' | 'php-codebase' | 'pdf-preview'>('dashboard');
  const [reports, setReports] = useState<Report[]>([]);
  const [settings, setSettings] = useState<Settings>({
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
  });
  
  const [user, setUser] = useState<User | null>(null);
  const [loginEmail, setLoginEmail] = useState('');
  const [loginPassword, setLoginPassword] = useState('');
  const [loginError, setLoginError] = useState('');
  const [flashMessage, setFlashMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);

  // Profile Form States
  const [profileName, setProfileName] = useState('');
  const [profileEmail, setProfileEmail] = useState('');
  const [profilePassword, setProfilePassword] = useState('');
  const [profileConfirmPassword, setProfileConfirmPassword] = useState('');
  const [profileCompanyName, setProfileCompanyName] = useState('');
  const [profileCompanyPhone, setProfileCompanyPhone] = useState('');
  const [profileCompanyEmail, setProfileCompanyEmail] = useState('');
  const [profileCompanyWebsite, setProfileCompanyWebsite] = useState('');
  const [profileCompanyFooter, setProfileCompanyFooter] = useState('');

  // Settings Form States
  const [settingsCompanyName, setSettingsCompanyName] = useState('');
  const [settingsEmail, setSettingsEmail] = useState('');
  const [settingsPhone, setSettingsPhone] = useState('');
  const [settingsWebsite, setSettingsWebsite] = useState('');
  const [settingsFooter, setSettingsFooter] = useState('');
  const [settingsMarginTop, setSettingsMarginTop] = useState(15);
  const [settingsMarginBottom, setSettingsMarginBottom] = useState(15);
  const [settingsMarginLeft, setSettingsMarginLeft] = useState(15);
  const [settingsMarginRight, setSettingsMarginRight] = useState(15);
  const [settingsPrimaryColor, setSettingsPrimaryColor] = useState('#CFFE1C');
  const [settingsSecondaryColor, setSettingsSecondaryColor] = useState('#141414');

  // Report Generator Form Wizard State
  const [wizardStep, setWizardStep] = useState(1);
  const [editingReportId, setEditingReportId] = useState<string | null>(null);
  const [selectedReportForPdf, setSelectedReportForPdf] = useState<Report | null>(null);

  // Wizard Fields
  const [fieldBusinessName, setFieldBusinessName] = useState('');
  const [fieldReportMonth, setFieldReportMonth] = useState('July');
  const [fieldReportYear, setFieldReportYear] = useState('2026');
  const [fieldGeneratedDate, setFieldGeneratedDate] = useState(new Date().toISOString().split('T')[0]);
  const [fieldBusinessLogo, setFieldBusinessLogo] = useState('');
  const [fieldCoverImage, setFieldCoverImage] = useState('');
  
  // Step 2: GMB Metrics
  const [fieldPeopleViewed, setFieldPeopleViewed] = useState(1200);
  const [fieldSearchDirect, setFieldSearchDirect] = useState(400);
  const [fieldSearchDiscovery, setFieldSearchDiscovery] = useState(800);
  const [fieldProfileInteractions, setFieldProfileInteractions] = useState(180);
  const [fieldReviewsCount, setFieldReviewsCount] = useState(12);
  const [fieldRatingAverage, setFieldRatingAverage] = useState(4.8);
  const [fieldViewsMaps, setFieldViewsMaps] = useState(750);
  const [fieldViewsSearch, setFieldViewsSearch] = useState(450);

  // Step 3: Keywords
  const [fieldKeywords, setFieldKeywords] = useState<Keyword[]>([
    { keyword: 'dentist near me', prev_rank: 12, curr_rank: 7 },
    { keyword: 'family dental care clinic', prev_rank: 8, curr_rank: 4 }
  ]);

  // Step 4: Map grid
  const [fieldHeatmapImage, setFieldHeatmapImage] = useState('');
  const [fieldAvgRank, setFieldAvgRank] = useState(2.3);
  const [fieldTop3Percentage, setFieldTop3Percentage] = useState(82.5);
  const [fieldPointsTracked, setFieldPointsTracked] = useState(49);
  const [fieldInsightText, setFieldInsightText] = useState('Our citation audit has successfully corrected regional map grid coordinate Green positions.');

  // Step 5: Backlink Catalogs
  const [fieldListings, setFieldListings] = useState<Backlink[]>([]);
  const [fieldProfiles, setFieldProfiles] = useState<Backlink[]>([]);
  const [fieldWeb2, setFieldWeb2] = useState<Backlink[]>([]);
  const [fieldBlogs, setFieldBlogs] = useState<Backlink[]>([]);
  const [fieldStacking, setFieldStacking] = useState<Backlink[]>([]);
  const [fieldStackingProperties, setFieldStackingProperties] = useState<Backlink[]>([]);
  const [fieldGuestPosting, setFieldGuestPosting] = useState<Backlink[]>([]);

  // Step 6: Geo Fence Mapping
  const [fieldGeofenceUrl, setFieldGeofenceUrl] = useState('');

  // Step 7: Next Month Action Strategy
  const [fieldNextPlan, setFieldNextPlan] = useState('');

  // Step 8: Thank You page contacts
  const [fieldCompanyEmail, setFieldCompanyEmail] = useState('');
  const [fieldCompanyPhone, setFieldCompanyPhone] = useState('');
  const [fieldCompanyWebsite, setFieldCompanyWebsite] = useState('');
  const [fieldFooterNotes, setFieldFooterNotes] = useState('');

  // Run on mount to read session & parameters
  useEffect(() => {
    // Check local session
    const cachedUser = localStorage.getItem('eagle_reports_user');
    if (cachedUser) {
      const parsed = JSON.parse(cachedUser);
      setUser(parsed);
      loadProfileData(parsed);
    }

    // Load reports & settings
    fetchReports();
    fetchSettings();
  }, []);

  const loadProfileData = (u: User) => {
    setProfileName(u.name);
    setProfileEmail(u.email);
    setProfileCompanyName(u.company_name || 'Eagle Digital Agency');
    setProfileCompanyPhone(u.company_phone || '+1 (555) 019-2831');
    setProfileCompanyEmail(u.company_email || 'reports@eagledigital.com');
    setProfileCompanyWebsite(u.company_website || 'www.eagledigital.com');
    setProfileCompanyFooter(u.company_footer || 'Eagle Digital Agency © 2026. Confidential SEO Performance Report.');
  };

  const fetchReports = async () => {
    try {
      const res = await fetch('/api/reports');
      const data = await res.json();
      if (data.success) {
        setReports(data.reports);
      }
    } catch (err) {
      console.error('Error fetching reports:', err);
    }
  };

  const fetchSettings = async () => {
    try {
      const res = await fetch('/api/settings');
      const data = await res.json();
      if (data.success) {
        setSettings(data.settings);
        loadSettingsForm(data.settings);
      }
    } catch (err) {
      console.error('Error fetching settings:', err);
    }
  };

  const loadSettingsForm = (s: Settings) => {
    setSettingsCompanyName(s.default_company_name);
    setSettingsEmail(s.default_email);
    setSettingsPhone(s.default_phone);
    setSettingsWebsite(s.default_website);
    setSettingsFooter(s.default_footer);
    setSettingsMarginTop(s.pdf_margin_top);
    setSettingsMarginBottom(s.pdf_margin_bottom);
    setSettingsMarginLeft(s.pdf_margin_left);
    setSettingsMarginRight(s.pdf_margin_right);
    setSettingsPrimaryColor(s.primary_color);
    setSettingsSecondaryColor(s.secondary_color);
  };

  const triggerFlash = (type: 'success' | 'error', text: string) => {
    setFlashMessage({ type, text });
    setTimeout(() => setFlashMessage(null), 5000);
  };

  // Login handler
  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoginError('');

    try {
      const res = await fetch('/api/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: loginEmail, password: loginPassword })
      });
      const data = await res.json();

      if (data.success) {
        setUser(data.user);
        localStorage.setItem('eagle_reports_user', JSON.stringify(data.user));
        loadProfileData(data.user);
        triggerFlash('success', `Welcome back, ${data.user.name}! Accessing staff GMB dashboard...`);
        setActiveTab('dashboard');
      } else {
        setLoginError(data.message || 'Invalid email or password');
      }
    } catch (err) {
      setLoginError('Server connection failed. Verify local environment.');
    }
  };

  // Logout handler
  const handleLogout = () => {
    setUser(null);
    localStorage.removeItem('eagle_reports_user');
    triggerFlash('success', 'Logged out successfully. Staff session terminated.');
    setActiveTab('dashboard');
  };

  // Update Profile Call
  const handleProfileUpdate = async (e: React.FormEvent) => {
    e.preventDefault();

    if (profilePassword && profilePassword !== profileConfirmPassword) {
      triggerFlash('error', 'Passwords do not match');
      return;
    }

    try {
      const res = await fetch('/api/profile/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          name: profileName,
          email: profileEmail,
          new_password: profilePassword,
          company_name: profileCompanyName,
          company_phone: profileCompanyPhone,
          company_email: profileCompanyEmail,
          company_website: profileCompanyWebsite,
          company_footer: profileCompanyFooter
        })
      });
      const data = await res.json();

      if (data.success) {
        setUser(data.user);
        localStorage.setItem('eagle_reports_user', JSON.stringify(data.user));
        setProfilePassword('');
        setProfileConfirmPassword('');
        triggerFlash('success', 'Admin profile and agency branding details updated successfully!');
      } else {
        triggerFlash('error', data.message || 'Failed to update profile');
      }
    } catch (err) {
      triggerFlash('error', 'Profile update failed.');
    }
  };

  // Update Settings Call
  const handleSettingsUpdate = async (e: React.FormEvent) => {
    e.preventDefault();

    try {
      const res = await fetch('/api/settings/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          default_company_name: settingsCompanyName,
          default_email: settingsEmail,
          default_phone: settingsPhone,
          default_website: settingsWebsite,
          default_footer: settingsFooter,
          pdf_margin_top: settingsMarginTop,
          pdf_margin_bottom: settingsMarginBottom,
          pdf_margin_left: settingsMarginLeft,
          pdf_margin_right: settingsMarginRight,
          primary_color: settingsPrimaryColor,
          secondary_color: settingsSecondaryColor
        })
      });
      const data = await res.json();

      if (data.success) {
        setSettings(data.settings);
        triggerFlash('success', 'Global reporting defaults and margins saved successfully!');
      } else {
        triggerFlash('error', 'Failed to update settings');
      }
    } catch (err) {
      triggerFlash('error', 'Settings save operation aborted.');
    }
  };

  // Start creating new report (Prefilled with defaults)
  const initNewReport = () => {
    setEditingReportId(null);
    setWizardStep(1);
    
    // Clear and prefill
    setFieldBusinessName('');
    setFieldReportMonth('July');
    setFieldReportYear('2026');
    setFieldGeneratedDate(new Date().toISOString().split('T')[0]);
    setFieldBusinessLogo('');
    setFieldCoverImage('');
    
    setFieldPeopleViewed(5400);
    setFieldSearchDirect(1200);
    setFieldSearchDiscovery(4200);
    setFieldProfileInteractions(290);
    setFieldReviewsCount(15);
    setFieldRatingAverage(4.9);
    setFieldViewsMaps(3500);
    setFieldViewsSearch(1900);

    setFieldKeywords([
      { keyword: 'best dentist near me', prev_rank: 9, curr_rank: 3 },
      { keyword: 'emergency tooth extraction cost', prev_rank: 15, curr_rank: 8 },
      { keyword: 'cosmetic dental veneer veneers', prev_rank: 21, curr_rank: 11 }
    ]);

    setFieldHeatmapImage('');
    setFieldAvgRank(1.8);
    setFieldTop3Percentage(89.4);
    setFieldPointsTracked(49);
    setFieldInsightText('The local 7x7 grid showed massive improvement following our Map Citation stack pushes.');

    setFieldListings([
      { category: 'business_listings', url: 'https://www.yelp.com/biz/dental-listing', status: 'Active' },
      { category: 'business_listings', url: 'https://www.yellowpages.com/dental-listing', status: 'Active' }
    ]);
    setFieldProfiles([]);
    setFieldWeb2([]);
    setFieldBlogs([]);
    setFieldStacking([]);
    setFieldStackingProperties([]);
    setFieldGuestPosting([]);

    setFieldGeofenceUrl('');
    setFieldNextPlan(`<h3>July 2026 Operational Blueprint</h3>
<ul>
  <li><strong>Map Citation Expansion:</strong> Target 20 health directory sites with high DA indices.</li>
  <li><strong>Social Indexing:</strong> Push Web 2.0 social bookmarks targeting core emergency dental terms.</li>
  <li><strong>GMB Engagement:</strong> Upload 10 geo-tagged optimization photos to profile maps.</li>
</ul>`);

    setFieldCompanyEmail(settings.default_email);
    setFieldCompanyPhone(settings.default_phone);
    setFieldCompanyWebsite(settings.default_website);
    setFieldFooterNotes(settings.default_footer);

    setActiveTab('generate');
  };

  // Edit existing report
  const initEditReport = (rep: Report) => {
    setEditingReportId(rep.id);
    setWizardStep(1);

    setFieldBusinessName(rep.business_name);
    setFieldReportMonth(rep.report_month);
    setFieldReportYear(rep.report_year);
    setFieldGeneratedDate(rep.generated_date);
    setFieldBusinessLogo(rep.business_logo || '');
    setFieldCoverImage(rep.cover_image || '');

    setFieldPeopleViewed(rep.people_viewed);
    setFieldSearchDirect(rep.search_direct);
    setFieldSearchDiscovery(rep.search_discovery);
    setFieldProfileInteractions(rep.profile_interactions);
    setFieldReviewsCount(rep.reviews_count);
    setFieldRatingAverage(rep.rating_average);
    setFieldViewsMaps(rep.views_maps);
    setFieldViewsSearch(rep.views_search);

    setFieldKeywords(rep.keyword_ranking || []);

    setFieldHeatmapImage(rep.heatmap_image || '');
    setFieldAvgRank(rep.avg_rank || 0);
    setFieldTop3Percentage(rep.top_3_percentage || 0);
    setFieldPointsTracked(rep.points_tracked || 49);
    setFieldInsightText(rep.insight_text || '');

    setFieldListings(rep.backlinks?.business_listings || []);
    setFieldProfiles(rep.backlinks?.profile_creations || []);
    setFieldWeb2(rep.backlinks?.web_2 || []);
    setFieldBlogs(rep.backlinks?.blogs || []);
    setFieldStacking(rep.backlinks?.google_stacking || []);
    setFieldStackingProperties(rep.backlinks?.google_stacking_properties || []);
    setFieldGuestPosting(rep.backlinks?.guest_posting || []);

    setFieldGeofenceUrl(rep.geofence_map_url || '');
    setFieldNextPlan(rep.next_month_plan || '');

    setFieldCompanyEmail(rep.company_email || settings.default_email);
    setFieldCompanyPhone(rep.company_phone || settings.default_phone);
    setFieldCompanyWebsite(rep.company_website || settings.default_website);
    setFieldFooterNotes(rep.footer_notes || settings.default_footer);

    setActiveTab('generate');
  };

  // One-click duplicate report (rolls over months automatically)
  const handleDuplicateReport = async (id: string) => {
    try {
      const res = await fetch(`/api/reports/${id}/duplicate`, { method: 'POST' });
      const data = await res.json();
      if (data.success) {
        triggerFlash('success', `Rollover Successful! Duplicated into ${data.report.report_month} ${data.report.report_year} Report. Rank metrics carried over.`);
        fetchReports();
        // Load into editing wizard directly
        initEditReport(data.report);
      } else {
        triggerFlash('error', 'Duplicate operation failed.');
      }
    } catch (err) {
      triggerFlash('error', 'Failed to connect to duplicate endpoint.');
    }
  };

  // Delete Report
  const handleDeleteReport = async (id: string) => {
    if (!window.confirm('Are you sure you want to permanently delete this report data? This action is irreversible.')) {
      return;
    }

    try {
      const res = await fetch(`/api/reports/${id}`, { method: 'DELETE' });
      const data = await res.json();
      if (data.success) {
        triggerFlash('success', 'Report deleted successfully.');
        fetchReports();
      } else {
        triggerFlash('error', 'Failed to delete report.');
      }
    } catch (err) {
      triggerFlash('error', 'Error sending delete payload.');
    }
  };

  // Save/Update report
  const handleSaveReport = async () => {
    const payload = {
      business_name: fieldBusinessName,
      report_month: fieldReportMonth,
      report_year: fieldReportYear,
      generated_date: fieldGeneratedDate,
      business_logo: fieldBusinessLogo,
      cover_image: fieldCoverImage,
      
      people_viewed: Number(fieldPeopleViewed),
      search_direct: Number(fieldSearchDirect),
      search_discovery: Number(fieldSearchDiscovery),
      profile_interactions: Number(fieldProfileInteractions),
      reviews_count: Number(fieldReviewsCount),
      rating_average: Number(fieldRatingAverage),
      views_maps: Number(fieldViewsMaps),
      views_search: Number(fieldViewsSearch),

      keyword_ranking: fieldKeywords,

      heatmap_image: fieldHeatmapImage,
      avg_rank: Number(fieldAvgRank),
      top_3_percentage: Number(fieldTop3Percentage),
      points_tracked: Number(fieldPointsTracked),
      insight_text: fieldInsightText,

      backlinks: {
        business_listings: fieldListings,
        profile_creations: fieldProfiles,
        web_2: fieldWeb2,
        blogs: fieldBlogs,
        google_stacking: fieldStacking,
        google_stacking_properties: fieldStackingProperties,
        guest_posting: fieldGuestPosting
      },

      geofence_map_url: fieldGeofenceUrl,
      next_month_plan: fieldNextPlan,
      company_email: fieldCompanyEmail,
      company_phone: fieldCompanyPhone,
      company_website: fieldCompanyWebsite,
      footer_notes: fieldFooterNotes
    };

    try {
      let res;
      if (editingReportId) {
        res = await fetch(`/api/reports/${editingReportId}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
      } else {
        res = await fetch('/api/reports', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
      }

      const data = await res.json();
      if (data.success) {
        triggerFlash('success', editingReportId ? 'Report modified successfully!' : 'New PDF Report generated and saved into records!');
        fetchReports();
        setActiveTab('history');
      } else {
        triggerFlash('error', 'Failed to save report.');
      }
    } catch (err) {
      triggerFlash('error', 'Payload transmission error.');
    }
  };

  // Image upload simulation inside React (saving as Base64 so it persists on the backend seamlessly)
  const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>, setter: (val: string) => void) => {
    const file = e.target.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onloadend = () => {
      setter(reader.result as string);
    };
    reader.readAsDataURL(file);
  };

  const addKeywordRow = () => {
    setFieldKeywords([...fieldKeywords, { keyword: '', prev_rank: 10, curr_rank: 8 }]);
  };

  const removeKeywordRow = (idx: number) => {
    setFieldKeywords(fieldKeywords.filter((_, i) => i !== idx));
  };

  const addBacklinkRow = (category: string) => {
    const newLink = { category, url: '', status: 'Active' };
    switch (category) {
      case 'business_listings': setFieldListings([...fieldListings, newLink]); break;
      case 'profile_creations': setFieldProfiles([...fieldProfiles, newLink]); break;
      case 'web_2': setFieldWeb2([...fieldWeb2, newLink]); break;
      case 'blogs': setFieldBlogs([...fieldBlogs, newLink]); break;
      case 'google_stacking': setFieldStacking([...fieldStacking, newLink]); break;
      case 'google_stacking_properties': setFieldStackingProperties([...fieldStackingProperties, newLink]); break;
      case 'guest_posting': setFieldGuestPosting([...fieldGuestPosting, newLink]); break;
    }
  };

  const removeBacklinkRow = (category: string, idx: number) => {
    switch (category) {
      case 'business_listings': setFieldListings(fieldListings.filter((_, i) => i !== idx)); break;
      case 'profile_creations': setFieldProfiles(fieldProfiles.filter((_, i) => i !== idx)); break;
      case 'web_2': setFieldWeb2(fieldWeb2.filter((_, i) => i !== idx)); break;
      case 'blogs': setFieldBlogs(fieldBlogs.filter((_, i) => i !== idx)); break;
      case 'google_stacking': setFieldStacking(fieldStacking.filter((_, i) => i !== idx)); break;
      case 'google_stacking_properties': setFieldStackingProperties(fieldStackingProperties.filter((_, i) => i !== idx)); break;
      case 'guest_posting': setFieldGuestPosting(fieldGuestPosting.filter((_, i) => i !== idx)); break;
    }
  };

  const updateBacklinkField = (category: string, idx: number, key: 'url' | 'status', value: string) => {
    const updater = (prev: Backlink[]) => prev.map((item, i) => i === idx ? { ...item, [key]: value } : item);
    switch (category) {
      case 'business_listings': setFieldListings(updater(fieldListings)); break;
      case 'profile_creations': setFieldProfiles(updater(fieldProfiles)); break;
      case 'web_2': setFieldWeb2(updater(fieldWeb2)); break;
      case 'blogs': setFieldBlogs(updater(fieldBlogs)); break;
      case 'google_stacking': setFieldStacking(updater(fieldStacking)); break;
      case 'google_stacking_properties': setFieldStackingProperties(updater(fieldStackingProperties)); break;
      case 'guest_posting': setFieldGuestPosting(updater(fieldGuestPosting)); break;
    }
  };

  // Validate wizard step fields before moving to next screen
  const handleNextStep = () => {
    if (wizardStep === 1) {
      if (!fieldBusinessName.trim()) {
        alert('Please fill out the Business Name before proceeding.');
        return;
      }
    }
    setWizardStep(prev => Math.min(prev + 1, 8));
  };

  // Auth gate check
  if (!user) {
    return (
      <div className="min-h-screen bg-brand-bg flex items-center justify-center p-6 select-none font-sans">
        <div className="w-full max-w-md bg-brand-card border border-brand-border rounded-2xl p-8 md:p-10 shadow-2xl space-y-6">
          <div className="text-center">
            <div className="inline-flex items-center gap-2.5 text-brand-accent mb-2">
              <div className="w-9 h-9 bg-brand-accent rounded-lg flex items-center justify-center shadow-lg shadow-brand-accent/10">
                <FileText className="w-5.5 h-5.5 text-brand-bg stroke-[2.5]" />
              </div>
              <span className="font-display font-black text-2xl tracking-tight text-white">EAGLE<span className="text-brand-accent">.</span></span>
            </div>
            <p className="text-xs text-gray-500 mt-1 font-display uppercase tracking-wider font-semibold">Staff Portal &bull; Report Architect</p>
          </div>

          {loginError && (
            <div className="p-3 bg-red-950/20 border border-red-900/30 text-red-400 text-xs rounded-xl flex items-center gap-2">
              <Lock className="w-4 h-4 shrink-0" />
              <span>{loginError}</span>
            </div>
          )}

          <form onSubmit={handleLogin} className="space-y-4">
            <div>
              <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Work Email Address</label>
              <input 
                type="email" 
                required
                value={loginEmail}
                onChange={e => setLoginEmail(e.target.value)}
                placeholder="admin@eagle.com"
                className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition placeholder:text-gray-600 font-sans"
              />
            </div>
            
            <div>
              <div className="flex justify-between items-center mb-1.5">
                <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Access Password</label>
                <button 
                  type="button" 
                  onClick={() => alert("Default staff credentials:\nEmail: admin@eagle.com\nPassword: password123")} 
                  className="text-[10px] font-bold text-brand-accent hover:underline uppercase tracking-wider"
                >
                  Need password hint?
                </button>
              </div>
              <input 
                type="password" 
                required
                value={loginPassword}
                onChange={e => setLoginPassword(e.target.value)}
                placeholder="password123"
                className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition placeholder:text-gray-600 font-sans"
              />
            </div>

            <div className="flex items-center gap-2 pt-1">
              <input 
                type="checkbox" 
                id="remember" 
                className="rounded border-brand-border bg-brand-bg text-brand-accent focus:ring-0 focus:ring-offset-0 w-4 h-4" 
                defaultChecked
              />
              <label htmlFor="remember" className="text-xs text-gray-400">Remember credentials on this browser</label>
            </div>

            <button 
              type="submit" 
              className="w-full bg-brand-accent hover:brightness-110 font-display font-bold text-brand-bg py-3.5 rounded-xl text-sm transition-all mt-2 flex items-center justify-center gap-2 shadow-lg shadow-brand-accent/15 active:scale-[0.98]"
            >
              <ShieldCheck className="w-4 h-4 stroke-[2.5]" />
              <span>Verify and Sign In</span>
            </button>
          </form>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-brand-bg text-[#f5f5f5] flex flex-col font-sans select-none">
      {/* Global Top Navbar */}
      <header className="bg-brand-bg/80 backdrop-blur-md border-b border-brand-border py-4.5 px-6 shrink-0 sticky top-0 z-40">
        <div className="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div className="flex items-center gap-2.5">
            <div className="w-8 h-8 bg-brand-accent rounded flex items-center justify-center">
              <FileText className="w-5 h-5 text-brand-bg stroke-[2.5]" />
            </div>
            <span className="font-display font-bold text-xl tracking-tight text-white">EAGLE<span className="text-brand-accent">.</span></span>
            <span className="text-[9px] uppercase tracking-widest bg-brand-accent/10 border border-brand-accent/20 text-brand-accent px-2 py-0.5 rounded-md font-bold font-display ml-1">Staff Portal</span>
          </div>

          <div className="flex flex-wrap items-center gap-1.5">
            <button 
              onClick={() => { setActiveTab('dashboard'); setSelectedReportForPdf(null); }}
              className={`flex items-center gap-1.5 text-xs font-semibold px-4 py-2.5 rounded-xl transition-all ${activeTab === 'dashboard' ? 'bg-brand-card text-brand-accent border border-brand-border shadow-md' : 'text-gray-400 hover:text-white hover:bg-brand-card/45 border border-transparent'}`}
            >
              <LayoutGrid className="w-3.5 h-3.5" />
              <span>Dashboard</span>
            </button>
            <button 
              onClick={() => { setActiveTab('history'); setSelectedReportForPdf(null); }}
              className={`flex items-center gap-1.5 text-xs font-semibold px-4 py-2.5 rounded-xl transition-all ${activeTab === 'history' ? 'bg-brand-card text-brand-accent border border-brand-border shadow-md' : 'text-gray-400 hover:text-white hover:bg-brand-card/45 border border-transparent'}`}
            >
              <History className="w-3.5 h-3.5" />
              <span>Report History</span>
            </button>
            <button 
              onClick={initNewReport}
              className={`flex items-center gap-1.5 text-xs font-semibold px-4 py-2.5 rounded-xl transition-all ${activeTab === 'generate' ? 'bg-brand-card text-brand-accent border border-brand-border shadow-md' : 'text-gray-400 hover:text-white hover:bg-brand-card/45 border border-transparent'}`}
            >
              <PlusCircle className="w-3.5 h-3.5" />
              <span>Generate Report</span>
            </button>
            <button 
              onClick={() => { setActiveTab('php-codebase'); setSelectedReportForPdf(null); }}
              className={`flex items-center gap-1.5 text-xs font-semibold px-4 py-2.5 rounded-xl transition-all ${activeTab === 'php-codebase' ? 'bg-brand-card text-brand-accent border border-brand-border shadow-md' : 'text-gray-400 hover:text-white hover:bg-brand-card/45 border border-transparent'}`}
            >
              <Code className="w-3.5 h-3.5" />
              <span>PHP MVC Source</span>
            </button>
            <button 
              onClick={() => { setActiveTab('profile'); setSelectedReportForPdf(null); }}
              className={`flex items-center gap-1.5 text-xs font-semibold px-4 py-2.5 rounded-xl transition-all ${activeTab === 'profile' ? 'bg-brand-card text-brand-accent border border-brand-border shadow-md' : 'text-gray-400 hover:text-white hover:bg-brand-card/45 border border-transparent'}`}
            >
              <Lock className="w-3.5 h-3.5" />
              <span>Profile</span>
            </button>
            <button 
              onClick={() => { setActiveTab('settings'); setSelectedReportForPdf(null); }}
              className={`flex items-center gap-1.5 text-xs font-semibold px-4 py-2.5 rounded-xl transition-all ${activeTab === 'settings' ? 'bg-brand-card text-brand-accent border border-brand-border shadow-md' : 'text-gray-400 hover:text-white hover:bg-brand-card/45 border border-transparent'}`}
            >
              <SettingsIcon className="w-3.5 h-3.5" />
              <span>Settings</span>
            </button>
            
            <span className="w-px h-5 bg-brand-border mx-1.5 hidden md:inline"></span>

            <button 
              onClick={handleLogout}
              className="text-gray-400 hover:text-red-400 p-2 hover:bg-red-950/10 rounded-xl transition"
              title="Logout session"
            >
              <LogOut className="w-4 h-4" />
            </button>
          </div>
        </div>
      </header>

      {/* Main Container */}
      <main className="flex-1 max-w-7xl w-full mx-auto p-6 md:p-8 space-y-6">
        
        {/* Flash Message Alert */}
        {flashMessage && (
          <div className={`p-4 rounded-xl border flex items-center gap-2.5 shadow-lg ${flashMessage.type === 'success' ? 'bg-green-950/20 border-green-800/30 text-green-400' : 'bg-red-950/20 border-red-800/30 text-red-400'}`}>
            <CheckCircle className="w-5 h-5 shrink-0" />
            <span className="text-xs font-medium">{flashMessage.text}</span>
          </div>
        )}

        {/* ================= VIEW: INTERACTIVE PDF PREVIEW ================= */}
        {activeTab === 'pdf-preview' && selectedReportForPdf && (
          <ReportPdfViewer 
            report={selectedReportForPdf} 
            settings={settings} 
            onBack={() => setActiveTab('history')} 
          />
        )}

        {/* ================= VIEW: DASHBOARD ================= */}
        {activeTab === 'dashboard' && (
          <div className="space-y-8">
            {/* Header message */}
            <div>
              <h2 className="text-2xl font-bold font-display text-white">Welcome back, {user.name}</h2>
              <p className="text-xs text-gray-400 mt-1">Eagle Reports Generator active. Analyze campaigns and compile printable search analytics.</p>
            </div>

            {/* Dashboard widgets */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div className="bg-brand-card border border-brand-border rounded-2xl p-6 flex items-center justify-between shadow-lg relative overflow-hidden group">
                <div className="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                  <FileSpreadsheet className="w-16 h-16 text-brand-accent" />
                </div>
                <div className="space-y-1 z-10">
                  <span className="text-[10px] font-bold text-gray-500 uppercase tracking-widest block">Total Reports</span>
                  <span className="text-4xl font-extrabold text-white font-display block mt-1">{reports.length}</span>
                  <span className="text-[10px] text-brand-accent font-semibold block pt-1">Synced and archived offline</span>
                </div>
                <div className="p-3 bg-brand-accent/5 text-brand-accent rounded-xl border border-brand-accent/10 z-10 shrink-0">
                  <FileSpreadsheet className="w-5 h-5 stroke-2" />
                </div>
              </div>

              <div className="bg-brand-card border border-brand-border rounded-2xl p-6 flex items-center justify-between shadow-lg relative overflow-hidden group">
                <div className="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                  <Calendar className="w-16 h-16 text-brand-accent" />
                </div>
                <div className="space-y-1 z-10">
                  <span className="text-[10px] font-bold text-gray-500 uppercase tracking-widest block">July Campaigns</span>
                  <span className="text-4xl font-extrabold text-white font-display block mt-1">
                    {reports.filter(r => r.report_month === 'July').length}
                  </span>
                  <span className="text-[10px] text-gray-400 font-medium block pt-1">In progress or finalized</span>
                </div>
                <div className="p-3 bg-brand-accent/5 text-brand-accent rounded-xl border border-brand-accent/10 z-10 shrink-0">
                  <Calendar className="w-5 h-5 stroke-2" />
                </div>
              </div>

              <div className="bg-brand-card border border-brand-border rounded-2xl p-6 flex items-center justify-between shadow-lg relative overflow-hidden group">
                <div className="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                  <BarChart3 className="w-16 h-16 text-green-400" />
                </div>
                <div className="space-y-1 z-10">
                  <span className="text-[10px] font-bold text-gray-500 uppercase tracking-widest block">System Security</span>
                  <span className="text-4xl font-extrabold text-green-400 font-display block mt-1">Secure</span>
                  <span className="text-[10px] text-gray-400 font-medium block pt-1">TLS 1.3 &bull; Encrypted Session</span>
                </div>
                <div className="p-3 bg-green-950/10 text-green-400 rounded-xl border border-green-800/15 z-10 shrink-0">
                  <ShieldCheck className="w-5 h-5 stroke-2" />
                </div>
              </div>
            </div>

            {/* Recent table & actions split */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
              <div className="lg:col-span-8 bg-brand-card border border-brand-border rounded-2xl overflow-hidden shadow-lg flex flex-col">
                <div className="border-b border-brand-border px-5 py-4.5 bg-brand-card/45 flex justify-between items-center">
                  <h3 className="text-sm font-semibold text-white font-display flex items-center gap-1.5">
                    <History className="w-4 h-4 text-brand-accent" />
                    Recent Reports
                  </h3>
                  <button 
                    onClick={() => setActiveTab('history')}
                    className="text-xs font-bold text-brand-accent hover:underline flex items-center gap-0.5"
                  >
                    <span>View All</span>
                    <ArrowRight className="w-3 h-3" />
                  </button>
                </div>

                <div className="flex-1 overflow-x-auto">
                  <table className="w-full text-xs text-left">
                    <thead>
                      <tr className="text-gray-400 bg-brand-card/25 uppercase text-[10px] font-bold border-b border-brand-border tracking-wider">
                        <th className="py-3.5 px-5">Client Business</th>
                        <th className="py-3.5 px-4">Month</th>
                        <th className="py-3.5 px-4">Year</th>
                        <th className="py-3.5 px-4">Created On</th>
                        <th className="py-3.5 px-5 text-end">Quick Actions</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-brand-border/45">
                      {reports.slice(0, 5).map((rep, idx) => (
                        <tr key={rep.id || idx} className="hover:bg-brand-border/20 transition group">
                          <td className="py-3.5 px-5 font-semibold text-white font-display">{rep.business_name}</td>
                          <td className="py-3.5 px-4 text-gray-400">{rep.report_month}</td>
                          <td className="py-3.5 px-4 text-gray-400">{rep.report_year}</td>
                          <td className="py-3.5 px-4 font-mono text-gray-500">{rep.generated_date}</td>
                          <td className="py-3.5 px-5 text-end">
                            <div className="inline-flex gap-1.5">
                              <button 
                                onClick={() => { setSelectedReportForPdf(rep); setActiveTab('pdf-preview'); }}
                                className="p-1.5 bg-brand-bg text-brand-accent rounded-lg border border-brand-border hover:bg-brand-accent hover:text-black transition-all"
                                title="Interactive PDF Compiler"
                              >
                                <Eye className="w-3.5 h-3.5" />
                              </button>
                              <button 
                                onClick={() => initEditReport(rep)}
                                className="p-1.5 bg-brand-bg text-gray-300 rounded-lg border border-brand-border hover:bg-white hover:text-black transition-all"
                                title="Edit parameters"
                              >
                                <FileText className="w-3.5 h-3.5" />
                              </button>
                            </div>
                          </td>
                        </tr>
                      ))}
                      {reports.length === 0 && (
                        <tr>
                          <td colSpan={5} className="py-12 text-center text-gray-500">
                            No marketing report records found in the localized SQL database.
                          </td>
                        </tr>
                      )}
                    </tbody>
                  </table>
                </div>
              </div>

              {/* Side utilities card */}
              <div className="lg:col-span-4 bg-brand-card border border-brand-border rounded-2xl p-6 shadow-lg flex flex-col justify-between">
                <div className="space-y-4">
                  <h4 className="text-sm font-semibold text-white font-display border-b border-brand-border pb-2">Quick Commands</h4>
                  <div className="space-y-3">
                    <button 
                      onClick={initNewReport}
                      className="w-full flex items-center justify-between text-left p-3.5 bg-brand-bg/50 hover:bg-brand-border/30 border border-brand-border rounded-xl group transition-all"
                    >
                      <div>
                        <span className="block text-xs font-bold text-white font-display">Generate New Report</span>
                        <span className="block text-[10px] text-gray-500 mt-0.5">8-step campaign metrics compiler</span>
                      </div>
                      <Plus className="w-4 h-4 text-brand-accent group-hover:scale-110 transition" />
                    </button>

                    <button 
                      onClick={() => setActiveTab('history')}
                      className="w-full flex items-center justify-between text-left p-3.5 bg-brand-bg/50 hover:bg-brand-border/30 border border-brand-border rounded-xl group transition-all"
                    >
                      <div>
                        <span className="block text-xs font-bold text-white font-display">Review Search Archives</span>
                        <span className="block text-[10px] text-gray-500 mt-0.5">Duplicate, delete or print history</span>
                      </div>
                      <History className="w-4 h-4 text-gray-400 group-hover:scale-110 transition" />
                    </button>
                  </div>
                </div>

                <div className="mt-8 p-3 bg-brand-accent/5 border border-brand-accent/10 rounded-xl text-center text-[10px] text-gray-400">
                  <span>Authorized Staff: <strong>{user.name}</strong></span>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* ================= VIEW: REPORT HISTORY TABLE ================= */}
        {activeTab === 'history' && (
          <div className="bg-brand-card border border-brand-border rounded-2xl overflow-hidden shadow-lg">
            <div className="border-b border-brand-border px-5 py-4.5 bg-brand-card/45 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
              <div>
                <h3 className="text-sm font-semibold text-white font-display flex items-center gap-1.5">
                  <History className="w-4 h-4 text-brand-accent" />
                  Report Archives Database
                </h3>
                <p className="text-xs text-gray-400 mt-0.5">View, edit, or execute one-click rollover duplicates for GMB SEO reports.</p>
              </div>
              <button 
                onClick={initNewReport}
                className="flex items-center gap-1.5 text-xs font-bold bg-brand-accent hover:brightness-110 text-brand-bg px-4 py-2.5 rounded-xl transition shadow-lg shadow-brand-accent/10 active:scale-[0.98]"
              >
                <Plus className="w-4 h-4 stroke-[2.5]" />
                <span>Generate New Report</span>
              </button>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full text-xs text-left">
                <thead>
                  <tr className="text-gray-400 bg-brand-card/25 uppercase text-[10px] font-bold border-b border-brand-border tracking-wider">
                    <th className="py-3.5 px-5">Client Business</th>
                    <th className="py-3.5 px-4">Report Month</th>
                    <th className="py-3.5 px-4">Report Year</th>
                    <th className="py-3.5 px-4">Created Date</th>
                    <th className="py-3.5 px-5 text-end">Available Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-brand-border/45">
                  {reports.map((rep, idx) => (
                    <tr key={rep.id || idx} className="hover:bg-brand-border/20 transition group">
                      <td className="py-4 px-5 font-semibold text-white font-display">{rep.business_name}</td>
                      <td className="py-4 px-4 text-gray-400">{rep.report_month}</td>
                      <td className="py-4 px-4 text-gray-400">{rep.report_year}</td>
                      <td className="py-4 px-4 font-mono text-gray-500">{rep.generated_date}</td>
                      <td className="py-4 px-5 text-end">
                        <div className="inline-flex gap-2">
                          <button 
                            onClick={() => { setSelectedReportForPdf(rep); setActiveTab('pdf-preview'); }}
                            className="flex items-center gap-1 text-[11px] font-semibold bg-brand-bg hover:bg-brand-border/40 border border-brand-border text-brand-accent px-3 py-1.5 rounded-xl transition"
                          >
                            <Eye className="w-3.5 h-3.5" />
                            <span>Preview</span>
                          </button>

                          <button 
                            onClick={() => initEditReport(rep)}
                            className="flex items-center gap-1 text-[11px] font-semibold bg-brand-bg hover:bg-brand-border/40 border border-brand-border text-gray-300 hover:text-white px-3 py-1.5 rounded-xl transition"
                          >
                            <FileText className="w-3.5 h-3.5" />
                            <span>Edit Data</span>
                          </button>

                          <button 
                            onClick={() => handleDuplicateReport(rep.id)}
                            className="flex items-center gap-1 text-[11px] font-semibold bg-brand-bg hover:bg-brand-border/40 border border-brand-border text-brand-accent/90 hover:text-brand-accent px-3 py-1.5 rounded-xl transition"
                            title="Duplicate ranks into next month"
                          >
                            <Copy className="w-3.5 h-3.5" />
                            <span>Duplicate</span>
                          </button>

                          <button 
                            onClick={() => handleDeleteReport(rep.id)}
                            className="p-1.5 text-gray-500 hover:text-red-400 hover:bg-red-950/10 rounded-xl transition"
                            title="Delete permanently"
                          >
                            <Trash2 className="w-4 h-4" />
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))}
                  {reports.length === 0 && (
                    <tr>
                      <td colSpan={5} className="py-16 text-center text-gray-500">
                        No report backups saved in the local database. Click Generate to add one.
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>
        )}

        {/* ================= VIEW: REPORT BUILDER MULTI-STEP WIZARD ================= */}
        {activeTab === 'generate' && (
          <div className="bg-brand-card border border-brand-border rounded-2xl overflow-hidden shadow-lg">
            {/* Header */}
            <div className="border-b border-brand-border px-5 py-4.5 bg-brand-card/45 flex justify-between items-center">
              <div>
                <h3 className="text-sm font-semibold text-white font-display">
                  {editingReportId ? 'Edit Performance Report Parameters' : 'Multi-Step Campaign Report Wizard'}
                </h3>
                <p className="text-xs text-gray-400 mt-0.5">Step {wizardStep} of 8 &bull; Configure SEO grid coordinates, citation uploads, and action plans.</p>
              </div>
              <button 
                onClick={() => setActiveTab('dashboard')}
                className="text-xs text-gray-400 hover:text-white bg-brand-bg/50 hover:bg-brand-border px-3 py-1.5 rounded-xl transition border border-brand-border"
              >
                Abort Wizard
              </button>
            </div>

            {/* Step indicators */}
            <div className="border-b border-brand-border px-6 py-3.5 bg-brand-card/20 flex items-center justify-between overflow-x-auto gap-4 scrollbar-none">
              {[
                'Basic Info', 'Performance', 'Keywords', 'Rank Grid', 
                'Backlinks', 'Geo Fence', 'Action Plan', 'Thank You'
              ].map((label, index) => {
                const stepNum = index + 1;
                const isActive = wizardStep === stepNum;
                const isCompleted = wizardStep > stepNum;
                return (
                  <button 
                    key={label}
                    onClick={() => setWizardStep(stepNum)}
                    className="flex items-center gap-1.5 text-left shrink-0 py-1"
                  >
                    <div className={`w-5.5 h-5.5 rounded-full flex items-center justify-center text-[10px] font-bold border transition ${
                      isActive ? 'bg-brand-accent border-brand-accent text-[#141414]' :
                      isCompleted ? 'bg-brand-accent/15 border-brand-accent/30 text-brand-accent' :
                      'border-brand-border text-gray-500'
                    }`}>
                      {stepNum}
                    </div>
                    <span className={`text-[10px] font-bold tracking-widest uppercase ${isActive ? 'text-brand-accent' : 'text-gray-500'}`}>{label}</span>
                  </button>
                );
              })}
            </div>

            {/* Wizard Body Panels */}
            <div className="p-6 md:p-8 space-y-6">

              {/* ================= STEP 1: BASIC INFO ================= */}
              {wizardStep === 1 && (
                <div className="space-y-4">
                  <h4 className="text-xs font-bold text-brand-accent uppercase tracking-widest mb-2">Step 1: Client Metadata &amp; Logo Uploads</h4>
                  <div className="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div className="md:col-span-6">
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Business Name *</label>
                      <input 
                        type="text" 
                        required
                        value={fieldBusinessName}
                        onChange={e => setFieldBusinessName(e.target.value)}
                        placeholder="e.g. Apex Gym &amp; Fitness"
                        className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                      />
                    </div>
                    <div className="md:col-span-3">
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Reporting Month</label>
                      <select 
                        value={fieldReportMonth}
                        onChange={e => setFieldReportMonth(e.target.value)}
                        className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                      >
                        {['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'].map(m => (
                          <option key={m} value={m}>{m}</option>
                        ))}
                      </select>
                    </div>
                    <div className="md:col-span-3">
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Reporting Year</label>
                      <select 
                        value={fieldReportYear}
                        onChange={e => setFieldReportYear(e.target.value)}
                        className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                      >
                        {['2025', '2026', '2027', '2028', '2029'].map(y => (
                          <option key={y} value={y}>{y}</option>
                        ))}
                      </select>
                    </div>
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <div>
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Date Compiled</label>
                      <input 
                        type="date" 
                        value={fieldGeneratedDate}
                        onChange={e => setFieldGeneratedDate(e.target.value)}
                        className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border font-mono transition"
                      />
                    </div>

                    <div>
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Upload Corporate Logo</label>
                      <input 
                        type="file" 
                        accept="image/*"
                        onChange={e => handleImageChange(e, setFieldBusinessLogo)}
                        className="w-full bg-brand-bg border border-brand-border text-white text-xs rounded-xl p-2.5 outline-none focus:active-accent-border file:bg-brand-accent/10 file:text-brand-accent file:border-0 file:rounded-lg file:px-3 file:py-1.5 file:text-xs file:font-bold file:cursor-pointer hover:file:opacity-90"
                      />
                      {fieldBusinessLogo && (
                        <span className="text-[10px] text-brand-accent mt-1 block font-mono">Image loaded successfully.</span>
                      )}
                    </div>

                    <div>
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Upload Custom Cover image</label>
                      <input 
                        type="file" 
                        accept="image/*"
                        onChange={e => handleImageChange(e, setFieldCoverImage)}
                        className="w-full bg-brand-bg border border-brand-border text-white text-xs rounded-xl p-2.5 outline-none focus:active-accent-border file:bg-brand-accent/10 file:text-brand-accent file:border-0 file:rounded-lg file:px-3 file:py-1.5 file:text-xs file:font-bold file:cursor-pointer hover:file:opacity-90"
                      />
                      {fieldCoverImage && (
                        <span className="text-[10px] text-brand-accent mt-1 block font-mono">Image loaded successfully.</span>
                      )}
                    </div>
                  </div>
                </div>
              )}

              {/* ================= STEP 2: PERFORMANCE SUMMARY ================= */}
              {wizardStep === 2 && (
                <div className="space-y-4">
                  <h4 className="text-xs font-bold text-brand-accent uppercase tracking-widest mb-2">Step 2: Google Profile Clicks &amp; View metrics</h4>
                  
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">People Viewed Profile</label>
                      <input 
                        type="number" 
                        value={fieldPeopleViewed}
                        onChange={e => setFieldPeopleViewed(Number(e.target.value))}
                        className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                      />
                    </div>
                    <div>
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Direct Query clicks</label>
                      <input 
                        type="number" 
                        value={fieldSearchDirect}
                        onChange={e => setFieldSearchDirect(Number(e.target.value))}
                        className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                      />
                    </div>
                    <div>
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Discovery Query clicks</label>
                      <input 
                        type="number" 
                        value={fieldSearchDiscovery}
                        onChange={e => setFieldSearchDiscovery(Number(e.target.value))}
                        className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <div>
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Total GMB Profile Interactions</label>
                      <input 
                        type="number" 
                        value={fieldProfileInteractions}
                        onChange={e => setFieldProfileInteractions(Number(e.target.value))}
                        className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                      />
                    </div>
                    <div>
                      <label className="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Reviews Count Received</label>
                      <input 
                        type="number" 
                        value={fieldReviewsCount}
                        onChange={e => setFieldReviewsCount(Number(e.target.value))}
                        className="w-full bg-[#111] border border-brand-border text-white text-sm rounded-lg p-3 outline-none focus:active-accent-border transition"
                      />
                    </div>
                    <div>
                      <label className="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Average Rating Metric</label>
                      <input 
                        type="number" 
                        step="0.1"
                        max="5"
                        value={fieldRatingAverage}
                        onChange={e => setFieldRatingAverage(Number(e.target.value))}
                        className="w-full bg-[#111] border border-brand-border text-white text-sm rounded-lg p-3 outline-none focus:active-accent-border transition"
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div>
                      <label className="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Views on Google Maps</label>
                      <input 
                        type="number" 
                        value={fieldViewsMaps}
                        onChange={e => setFieldViewsMaps(Number(e.target.value))}
                        className="w-full bg-[#111] border border-brand-border text-white text-sm rounded-lg p-3 outline-none focus:active-accent-border transition"
                      />
                    </div>
                    <div>
                      <label className="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Views on Google Search</label>
                      <input 
                        type="number" 
                        value={fieldViewsSearch}
                        onChange={e => setFieldViewsSearch(Number(e.target.value))}
                        className="w-full bg-[#111] border border-brand-border text-white text-sm rounded-lg p-3 outline-none focus:active-accent-border transition"
                      />
                    </div>
                  </div>
                </div>
              )}

              {/* ================= STEP 3: KEYWORD RANKING ================= */}
              {wizardStep === 3 && (
                <div className="space-y-4">
                  <div className="flex justify-between items-center mb-2">
                    <h4 className="text-xs font-semibold text-brand-accent uppercase tracking-wider">Step 3: Keyword Search Movements Table</h4>
                    <button 
                      type="button" 
                      onClick={addKeywordRow}
                      className="flex items-center gap-1 text-[11px] font-semibold bg-brand-accent/15 border border-brand-accent/20 text-brand-accent px-2.5 py-1.5 rounded hover:opacity-90 transition"
                    >
                      <Plus className="w-3.5 h-3.5" />
                      <span>Add Keyword</span>
                    </button>
                  </div>

                  <div className="border border-brand-border rounded-lg overflow-hidden bg-[#121212]">
                    <table className="w-full text-xs text-left">
                      <thead>
                        <tr className="bg-[#181818] text-gray-500 uppercase text-[9px] font-bold border-b border-brand-border">
                          <th className="py-2.5 px-4">Keyword Term Target *</th>
                          <th className="py-2.5 px-4 text-center">Previous Rank *</th>
                          <th className="py-2.5 px-4 text-center">Current Rank *</th>
                          <th className="py-2.5 px-4 text-end">Action</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-[#252525]">
                        {fieldKeywords.map((item, idx) => (
                          <tr key={idx}>
                            <td className="py-2 px-4">
                              <input 
                                type="text"
                                required
                                value={item.keyword}
                                onChange={e => {
                                  const updated = [...fieldKeywords];
                                  updated[idx].keyword = e.target.value;
                                  setFieldKeywords(updated);
                                }}
                                placeholder="e.g. dental clinic city centre"
                                className="w-full bg-[#191919] border border-brand-border rounded text-xs p-2 text-white outline-none focus:border-brand-accent"
                              />
                            </td>
                            <td className="py-2 px-4">
                              <input 
                                type="number"
                                required
                                min="1"
                                value={item.prev_rank}
                                onChange={e => {
                                  const updated = [...fieldKeywords];
                                  updated[idx].prev_rank = Number(e.target.value);
                                  setFieldKeywords(updated);
                                }}
                                className="w-20 mx-auto text-center bg-[#191919] border border-brand-border rounded text-xs p-2 text-white outline-none focus:border-brand-accent block"
                              />
                            </td>
                            <td className="py-2 px-4">
                              <input 
                                type="number"
                                required
                                min="1"
                                value={item.curr_rank}
                                onChange={e => {
                                  const updated = [...fieldKeywords];
                                  updated[idx].curr_rank = Number(e.target.value);
                                  setFieldKeywords(updated);
                                }}
                                className="w-20 mx-auto text-center bg-[#191919] border border-brand-border rounded text-xs p-2 text-white outline-none focus:border-brand-accent block"
                              />
                            </td>
                            <td className="py-2 px-4 text-end">
                              <button 
                                type="button" 
                                onClick={() => removeKeywordRow(idx)}
                                className="p-1.5 text-gray-500 hover:text-red-400 hover:bg-red-950/10 rounded transition"
                              >
                                <Trash2 className="w-4 h-4" />
                              </button>
                            </td>
                          </tr>
                        ))}
                        {fieldKeywords.length === 0 && (
                          <tr>
                            <td colSpan={4} className="py-8 text-center text-gray-500">
                              No keywords added yet. Click 'Add Keyword' to construct ranking matrices.
                            </td>
                          </tr>
                        )}
                      </tbody>
                    </table>
                  </div>
                </div>
              )}

              {/* ================= STEP 4: LOCAL RANKING GRID ================= */}
              {wizardStep === 4 && (
                <div className="space-y-4">
                  <h4 className="text-xs font-semibold text-brand-accent uppercase tracking-wider mb-2">Step 4: Local GMB Grid Heatmap Coordinate tracker</h4>
                  
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Heatmap Screenshot upload</label>
                      <input 
                        type="file" 
                        accept="image/*"
                        onChange={e => handleImageChange(e, setFieldHeatmapImage)}
                        className="w-full bg-[#111] border border-brand-border text-white text-xs rounded-lg p-2.5 outline-none focus:active-accent-border file:bg-brand-accent/15 file:text-brand-accent file:border-0 file:rounded file:px-2.5 file:py-1 file:font-semibold file:cursor-pointer hover:file:opacity-90"
                      />
                      {fieldHeatmapImage && (
                        <span className="text-[10px] text-green-400 mt-1 block font-mono">Screenshot loaded successfully.</span>
                      )}
                    </div>
                    <div>
                      <label className="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Average Rank Position (e.g. 2.4)</label>
                      <input 
                        type="number" 
                        step="0.01"
                        value={fieldAvgRank}
                        onChange={e => setFieldAvgRank(Number(e.target.value))}
                        className="w-full bg-[#111] border border-brand-border text-white text-sm rounded-lg p-3 outline-none focus:active-accent-border transition"
                      />
                    </div>
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    <div>
                      <label className="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Green Nodes Top 3 Share (%)</label>
                      <input 
                        type="number" 
                        step="0.1"
                        value={fieldTop3Percentage}
                        onChange={e => setFieldTop3Percentage(Number(e.target.value))}
                        className="w-full bg-[#111] border border-brand-border text-white text-sm rounded-lg p-3 outline-none focus:active-accent-border transition"
                      />
                    </div>
                    <div>
                      <label className="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Grid Coordinates Tracked Points</label>
                      <input 
                        type="number" 
                        value={fieldPointsTracked}
                        onChange={e => setFieldPointsTracked(Number(e.target.value))}
                        className="w-full bg-[#111] border border-brand-border text-white text-sm rounded-lg p-3 outline-none focus:active-accent-border transition"
                      />
                    </div>
                  </div>

                  <div className="pt-2">
                    <label className="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Local Heatmap Grid Insights Text</label>
                    <textarea 
                      value={fieldInsightText}
                      onChange={e => setFieldInsightText(e.target.value)}
                      rows={4}
                      placeholder="Enter insights regarding competitor movements or grid updates..."
                      className="w-full bg-[#111] border border-brand-border text-white text-sm rounded-lg p-3 outline-none focus:active-accent-border transition"
                    />
                  </div>
                </div>
              )}

              {/* ================= STEP 5: BACKLINK REPORT ================= */}
              {wizardStep === 5 && (
                <div className="space-y-6">
                  <div>
                    <h4 className="text-xs font-semibold text-brand-accent uppercase tracking-wider mb-1">Step 5: Authority Backlink Building Categories</h4>
                    <p className="text-[10px] text-gray-400">Populate URLs created for various link networks during this cycle. Leave lists empty if not built.</p>
                  </div>

                  {[
                    { key: 'business_listings', list: fieldListings, label: 'Business Directory Citations' },
                    { key: 'profile_creations', list: fieldProfiles, label: 'Social Profiles Creations' },
                    { key: 'web_2', list: fieldWeb2, label: 'Web 2.0 Networks' },
                    { key: 'blogs', list: fieldBlogs, label: 'Editorial Blog Placements' },
                    { key: 'google_stacking', list: fieldStacking, label: 'Google Folder Stacks' },
                    { key: 'google_stacking_properties', list: fieldStackingProperties, label: 'Google Stack Properties Optimized' },
                    { key: 'guest_posting', list: fieldGuestPosting, label: 'Guest Posting Placements' }
                  ].map(({ key, list, label }) => (
                    <div key={key} className="bg-[#121212] border border-brand-border rounded-lg p-4 space-y-3">
                      <div className="flex justify-between items-center">
                        <span className="text-xs font-bold text-white font-display">{label}</span>
                        <button 
                          type="button"
                          onClick={() => addBacklinkRow(key)}
                          className="text-[10px] bg-[#222] border border-brand-border text-brand-accent px-2.5 py-1 rounded hover:bg-[#2e2e2e]"
                        >
                          + Add Placement
                        </button>
                      </div>

                      {list.length > 0 ? (
                        <div className="space-y-2">
                          {list.map((item, idx) => (
                            <div key={idx} className="flex gap-2 items-center">
                              <input 
                                type="url"
                                required
                                value={item.url}
                                onChange={e => updateBacklinkField(key, idx, 'url', e.target.value)}
                                placeholder="https://example.com/listing-link"
                                className="flex-1 bg-[#181818] border border-brand-border rounded text-xs p-2 text-white outline-none focus:border-brand-accent font-mono"
                              />
                              <select
                                value={item.status}
                                onChange={e => updateBacklinkField(key, idx, 'status', e.target.value)}
                                className="bg-[#181818] border border-brand-border rounded text-xs p-2 text-white outline-none"
                              >
                                <option value="Active">Active</option>
                                <option value="Pending">Pending</option>
                              </select>
                              <button 
                                type="button"
                                onClick={() => removeBacklinkRow(key, idx)}
                                className="p-2 text-gray-500 hover:text-red-400"
                              >
                                <Trash2 className="w-4 h-4" />
                              </button>
                            </div>
                          ))}
                        </div>
                      ) : (
                        <span className="text-[10px] text-gray-600 block italic">No links built in this directory category this month.</span>
                      )}
                    </div>
                  ))}
                </div>
              )}

              {/* ================= STEP 6: GEO FENCE ================= */}
              {wizardStep === 6 && (
                <div className="space-y-4">
                  <h4 className="text-xs font-semibold text-brand-accent uppercase tracking-wider mb-2">Step 6: Map Geo-Fence Embedded Links</h4>
                  <div>
                    <label className="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Google My Maps / Geo-Citation Frame Link</label>
                    <textarea 
                      value={fieldGeofenceUrl}
                      onChange={e => setFieldGeofenceUrl(e.target.value)}
                      rows={5}
                      placeholder="Paste your GMap share URL or raw iframe script here..."
                      className="w-full bg-[#111] border border-brand-border text-white text-xs rounded-lg p-3 outline-none focus:active-accent-border font-mono transition"
                    />
                    <span className="text-[10px] text-gray-500 mt-1.5 block">Paste standard share URL or raw Google Iframe markup. The compiler strips target properties automatically.</span>
                  </div>
                </div>
              )}

              {/* ================= STEP 7: ACTION PLAN ================= */}
              {wizardStep === 7 && (
                <div className="space-y-4">
                  <h4 className="text-xs font-semibold text-brand-accent uppercase tracking-wider mb-2">Step 7: July Strategy Action Plan</h4>
                  <div>
                    <label className="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Action Plan (Supports HTML / lists)</label>
                    <textarea 
                      value={fieldNextPlan}
                      onChange={e => setFieldNextPlan(e.target.value)}
                      rows={10}
                      placeholder="<h3>Strategy plan</h3><ul><li>Audit directories</li></ul>"
                      className="w-full bg-[#111] border border-brand-border text-white text-xs rounded-lg p-3 outline-none focus:active-accent-border font-mono transition"
                    />
                    <span className="text-[10px] text-gray-500 mt-1.5 block">Format with standard HTML elements (h3, ul, li, strong) to generate bulleted reports.</span>
                  </div>
                </div>
              )}

              {/* ================= STEP 8: THANK YOU PAGE ================= */}
              {wizardStep === 8 && (
                <div className="space-y-4">
                  <h4 className="text-xs font-semibold text-brand-accent uppercase tracking-wider mb-2">Step 8: Contact Details &amp; Custom Disclaimers</h4>
                  
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                      <label className="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Agency Support Email</label>
                      <input 
                        type="email"
                        value={fieldCompanyEmail}
                        onChange={e => setFieldCompanyEmail(e.target.value)}
                        className="w-full bg-[#111] border border-brand-border text-white text-sm rounded-lg p-3 outline-none focus:active-accent-border transition"
                      />
                    </div>
                    <div>
                      <label className="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Agency Support Phone</label>
                      <input 
                        type="text"
                        value={fieldCompanyPhone}
                        onChange={e => setFieldCompanyPhone(e.target.value)}
                        className="w-full bg-[#111] border border-brand-border text-white text-sm rounded-lg p-3 outline-none focus:active-accent-border transition"
                      />
                    </div>
                    <div>
                      <label className="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Agency Website URL</label>
                      <input 
                        type="text"
                        value={fieldCompanyWebsite}
                        onChange={e => setFieldCompanyWebsite(e.target.value)}
                        className="w-full bg-[#111] border border-brand-border text-white text-sm rounded-lg p-3 outline-none focus:active-accent-border transition"
                      />
                    </div>
                  </div>

                  <div className="pt-2">
                    <label className="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Bottom Page Footer Note</label>
                    <input 
                      type="text"
                      value={fieldFooterNotes}
                      onChange={e => setFieldFooterNotes(e.target.value)}
                      className="w-full bg-[#111] border border-brand-border text-white text-sm rounded-lg p-3 outline-none focus:active-accent-border transition"
                      placeholder="e.g. Eagle Digital Agency © 2026. Confidential SEO Performance Report."
                    />
                  </div>
                </div>
              )}

            </div>

            {/* Wizard Navigation Footer */}
            <div className="border-t border-brand-border px-6 py-4 bg-[#141414] flex justify-between items-center">
              <button 
                type="button" 
                onClick={() => setWizardStep(prev => Math.max(prev - 1, 1))}
                disabled={wizardStep === 1}
                className="flex items-center gap-1.5 text-xs font-semibold bg-[#222] hover:bg-[#2e2e2e] border border-brand-border text-gray-300 px-4 py-2 rounded-lg transition disabled:opacity-30 disabled:pointer-events-none"
              >
                <ChevronLeft className="w-4 h-4" />
                <span>Back</span>
              </button>

              <div className="flex gap-2">
                <button 
                  type="button" 
                  onClick={() => setActiveTab('dashboard')}
                  className="text-xs text-gray-400 hover:text-white font-semibold px-4 py-2"
                >
                  Cancel
                </button>

                {wizardStep < 8 ? (
                  <button 
                    type="button" 
                    onClick={handleNextStep}
                    className="flex items-center gap-1.5 text-xs font-semibold bg-brand-accent hover:opacity-90 text-black px-4 py-2 rounded-lg transition"
                  >
                    <span>Next</span>
                    <ChevronRight className="w-4 h-4" />
                  </button>
                ) : (
                  <button 
                    type="button" 
                    onClick={handleSaveReport}
                    className="flex items-center gap-1.5 text-xs font-semibold bg-brand-accent hover:opacity-90 text-black px-5 py-2 rounded-lg transition"
                  >
                    <CheckCircle className="w-4 h-4" />
                    <span>Compile &amp; Generate PDF</span>
                  </button>
                )}
              </div>
            </div>
          </div>
        )}

        {/* ================= VIEW: PHP CODEBASE ================= */}
        {activeTab === 'php-codebase' && (
          <PhpSourceViewer />
        )}

        {/* ================= VIEW: ADMIN PROFILE ================= */}
        {activeTab === 'profile' && (
          <form onSubmit={handleProfileUpdate} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              
              {/* Login creds */}
              <div className="bg-brand-card border border-brand-border rounded-2xl p-6 md:p-8 shadow-lg space-y-5">
                <h4 className="text-sm font-semibold text-white font-display border-b border-brand-border pb-2.5 flex items-center gap-2">
                  <Lock className="w-4 h-4 text-brand-accent" />
                  Staff Account Security
                </h4>

                <div>
                  <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Full Name</label>
                  <input 
                    type="text" 
                    required
                    value={profileName}
                    onChange={e => setProfileName(e.target.value)}
                    className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                  />
                </div>

                <div>
                  <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Staff Email Address</label>
                  <input 
                    type="email" 
                    required
                    value={profileEmail}
                    onChange={e => setProfileEmail(e.target.value)}
                    className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                  />
                </div>

                <hr className="border-brand-border my-4" />
                <span className="text-[10px] text-gray-500 block">Fill out the credentials below only if you want to alter your staff password.</span>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">New Password</label>
                    <input 
                      type="password" 
                      value={profilePassword}
                      onChange={e => setProfilePassword(e.target.value)}
                      placeholder="Min. 8 chars"
                      className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition placeholder:text-gray-600"
                    />
                  </div>
                  <div>
                    <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Confirm Password</label>
                    <input 
                      type="password" 
                      value={profileConfirmPassword}
                      onChange={e => setProfileConfirmPassword(e.target.value)}
                      placeholder="Repeat password"
                      className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition placeholder:text-gray-600"
                    />
                  </div>
                </div>
              </div>

              {/* Branding credentials */}
              <div className="bg-brand-card border border-brand-border rounded-2xl p-6 md:p-8 shadow-lg space-y-5">
                <h4 className="text-sm font-semibold text-white font-display border-b border-brand-border pb-2.5 flex items-center gap-2">
                  <BarChart3 className="w-4 h-4 text-brand-accent" />
                  Corporate Brand Presets
                </h4>

                <div>
                  <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Agency Corporate Name</label>
                  <input 
                    type="text" 
                    value={profileCompanyName}
                    onChange={e => setProfileCompanyName(e.target.value)}
                    className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                  />
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Corporate Website</label>
                    <input 
                      type="text" 
                      value={profileCompanyWebsite}
                      onChange={e => setProfileCompanyWebsite(e.target.value)}
                      className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                    />
                  </div>
                  <div>
                    <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Corporate Phone</label>
                    <input 
                      type="text" 
                      value={profileCompanyPhone}
                      onChange={e => setProfileCompanyPhone(e.target.value)}
                      className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Branding Email Contact</label>
                  <input 
                    type="email" 
                    value={profileCompanyEmail}
                    onChange={e => setProfileCompanyEmail(e.target.value)}
                    className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                  />
                </div>

                <div>
                  <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Global Cover Disclaimer Tag</label>
                  <textarea 
                    value={profileCompanyFooter}
                    onChange={e => setProfileCompanyFooter(e.target.value)}
                    rows={2}
                    className="w-full bg-brand-bg border border-brand-border text-white text-xs rounded-xl p-3 outline-none focus:active-accent-border transition"
                  />
                </div>
              </div>

            </div>

            <div className="flex justify-end gap-3 pt-2">
              <button 
                type="button" 
                onClick={() => setActiveTab('dashboard')}
                className="text-xs font-semibold text-gray-400 hover:text-white px-5 py-2.5 rounded-xl hover:bg-brand-card/40 transition"
              >
                Cancel
              </button>
              <button 
                type="submit" 
                className="bg-brand-accent hover:brightness-110 font-display font-bold text-brand-bg px-5 py-3 rounded-xl text-xs transition flex items-center gap-1.5 shadow-lg shadow-brand-accent/10 active:scale-[0.98]"
              >
                <Check className="w-4 h-4 stroke-[2.5]" />
                <span>Save Profile Parameters</span>
              </button>
            </div>
          </form>
        )}

        {/* ================= VIEW: GLOBAL SETTINGS ================= */}
        {activeTab === 'settings' && (
          <form onSubmit={handleSettingsUpdate} className="space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              
              {/* Default metadata Prefill */}
              <div className="bg-brand-card border border-brand-border rounded-2xl p-6 md:p-8 shadow-lg space-y-4">
                <h4 className="text-sm font-semibold text-white font-display border-b border-brand-border pb-2.5 flex items-center gap-2">
                  <FileText className="w-4 h-4 text-brand-accent" />
                  New Report Defaults Pre-fill
                </h4>

                <div>
                  <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Default Company / Agency Name</label>
                  <input 
                    type="text" 
                    required
                    value={settingsCompanyName}
                    onChange={e => setSettingsCompanyName(e.target.value)}
                    className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                  />
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Default Contact Website</label>
                    <input 
                      type="text" 
                      value={settingsWebsite}
                      onChange={e => setSettingsWebsite(e.target.value)}
                      className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                    />
                  </div>
                  <div>
                    <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Default Contact Telephone</label>
                    <input 
                      type="text" 
                      value={settingsPhone}
                      onChange={e => setSettingsPhone(e.target.value)}
                      className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Default Contact Email</label>
                  <input 
                    type="email" 
                    required
                    value={settingsEmail}
                    onChange={e => setSettingsEmail(e.target.value)}
                    className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition"
                  />
                </div>

                <div>
                  <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Default PDF Footer Disclaimer</label>
                  <textarea 
                    value={settingsFooter}
                    onChange={e => setSettingsFooter(e.target.value)}
                    rows={3}
                    required
                    className="w-full bg-brand-bg border border-brand-border text-white text-xs rounded-xl p-3 outline-none focus:active-accent-border transition"
                  />
                </div>
              </div>

              {/* Margins and colors */}
              <div className="bg-brand-card border border-brand-border rounded-2xl p-6 md:p-8 shadow-lg space-y-6">
                <div className="space-y-4">
                  <h4 className="text-sm font-semibold text-white font-display border-b border-brand-border pb-2.5 flex items-center gap-2">
                    <Eye className="w-4 h-4 text-brand-accent" />
                    Print Margins Configuration (mm)
                  </h4>
                  
                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Top Margin</label>
                      <input 
                        type="number" 
                        value={settingsMarginTop}
                        onChange={e => setSettingsMarginTop(Number(e.target.value))}
                        className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition font-mono"
                      />
                    </div>
                    <div>
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Bottom Margin</label>
                      <input 
                        type="number" 
                        value={settingsMarginBottom}
                        onChange={e => setSettingsMarginBottom(Number(e.target.value))}
                        className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition font-mono"
                      />
                    </div>
                    <div>
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Left Margin</label>
                      <input 
                        type="number" 
                        value={settingsMarginLeft}
                        onChange={e => setSettingsMarginLeft(Number(e.target.value))}
                        className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition font-mono"
                      />
                    </div>
                    <div>
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Right Margin</label>
                      <input 
                        type="number" 
                        value={settingsMarginRight}
                        onChange={e => setSettingsMarginRight(Number(e.target.value))}
                        className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-3 outline-none focus:active-accent-border transition font-mono"
                      />
                    </div>
                  </div>
                </div>

                <div className="space-y-4 pt-2">
                  <h4 className="text-sm font-semibold text-white font-display border-b border-brand-border pb-2.5 flex items-center gap-2">
                    <SettingsIcon className="w-4 h-4 text-brand-accent" />
                    Branding Color Theme Palette
                  </h4>

                  <div className="grid grid-cols-2 gap-4">
                    <div>
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Primary Accent Color</label>
                      <div className="flex gap-2">
                        <input 
                          type="color" 
                          value={settingsPrimaryColor}
                          onChange={e => setSettingsPrimaryColor(e.target.value)}
                          className="w-10 h-10 border-0 bg-transparent cursor-pointer rounded-xl shrink-0"
                        />
                        <input 
                          type="text" 
                          value={settingsPrimaryColor}
                          onChange={e => setSettingsPrimaryColor(e.target.value)}
                          className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-2.5 outline-none font-mono text-center"
                        />
                      </div>
                    </div>

                    <div>
                      <label className="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Secondary Theme Dark</label>
                      <div className="flex gap-2">
                        <input 
                          type="color" 
                          value={settingsSecondaryColor}
                          onChange={e => setSettingsSecondaryColor(e.target.value)}
                          className="w-10 h-10 border-0 bg-transparent cursor-pointer rounded-xl shrink-0"
                        />
                        <input 
                          type="text" 
                          value={settingsSecondaryColor}
                          onChange={e => setSettingsSecondaryColor(e.target.value)}
                          className="w-full bg-brand-bg border border-brand-border text-white text-sm rounded-xl p-2.5 outline-none font-mono text-center"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <div className="flex justify-end gap-3 pt-2">
              <button 
                type="button" 
                onClick={() => setActiveTab('dashboard')}
                className="text-xs font-semibold text-gray-400 hover:text-white px-5 py-2.5 rounded-xl hover:bg-brand-card/40 transition"
              >
                Cancel
              </button>
              <button 
                type="submit" 
                className="bg-brand-accent hover:brightness-110 font-display font-bold text-brand-bg px-5 py-3 rounded-xl text-xs transition flex items-center gap-1.5 shadow-lg shadow-brand-accent/10 active:scale-[0.98]"
              >
                <Check className="w-4 h-4 stroke-[2.5]" />
                <span>Save Global Settings</span>
              </button>
            </div>
          </form>
        )}

      </main>
    </div>
  );
}
