import React, { useRef, useState } from 'react';
import { ArrowLeft, Download, FileText, Printer, Check, ShieldAlert } from 'lucide-react';
import html2canvas from 'html2canvas';
import { jsPDF } from 'jspdf';
import { Report, Settings } from '../types';

// Helper functions for OKLCH conversion to bypass html2canvas parsing errors
const parseVal = (valStr: string, isPercentScale = false): number => {
  if (valStr.endsWith('%')) {
    return parseFloat(valStr) / 100;
  }
  const parsed = parseFloat(valStr);
  if (isPercentScale && parsed > 1) {
    return parsed / 100;
  }
  return parsed;
};

function oklchToRgb(l: number, c: number, h: number, alpha?: number): string {
  // h is in degrees, convert to radians
  const hRad = (h * Math.PI) / 180;
  const a = c * Math.cos(hRad);
  const b = c * Math.sin(hRad);

  const l_ = l + 0.3963377774 * a + 0.2158037573 * b;
  const m_ = l - 0.1055613458 * a - 0.0638541728 * b;
  const s_ = l - 0.0894841775 * a - 1.2914855480 * b;

  const l_3 = l_ * l_ * l_;
  const m_3 = m_ * m_ * m_;
  const s_3 = s_ * s_ * s_;

  let r = 4.0767416621 * l_3 - 3.3077115913 * m_3 + 0.2309699292 * s_3;
  let g = -1.2684380046 * l_3 + 2.6097574011 * m_3 - 0.3413193965 * s_3;
  let bd = -0.0041960863 * l_3 - 0.7034186147 * m_3 + 1.7076147010 * s_3;

  const f = (val: number) => {
    const clamped = Math.max(0, Math.min(1, val));
    return clamped <= 0.0031308
      ? 12.92 * clamped
      : 1.055 * Math.pow(clamped, 1 / 2.4) - 0.055;
  };

  const R = Math.round(f(r) * 255);
  const G = Math.round(f(g) * 255);
  const B = Math.round(f(bd) * 255);

  if (alpha !== undefined) {
    return `rgba(${R}, ${G}, ${B}, ${alpha})`;
  }
  return `rgb(${R}, ${G}, ${B})`;
}

function replaceOklchWithRgb(cssText: string): string {
  return cssText.replace(
    /oklch\(\s*([\d.]+%?)\s+([\d.]+%?)\s+([\d.]+%?)(?:\s*\/\s*([\d.]+%?))?\s*\)/gi,
    (match, lStr, cStr, hStr, aStr) => {
      try {
        const l = parseVal(lStr, true);
        const c = parseFloat(cStr);
        const h = parseFloat(hStr);
        const a = aStr ? parseVal(aStr) : undefined;
        return oklchToRgb(l, c, h, a);
      } catch (err) {
        return 'rgb(120, 120, 120)';
      }
    }
  );
}

const prepareStyles = async () => {
  const styleElements = Array.from(document.querySelectorAll('style'));
  const linkElements = Array.from(document.querySelectorAll('link[rel="stylesheet"]')) as HTMLLinkElement[];
  
  const originalStyleContents = styleElements.map(el => ({
    el,
    content: el.textContent || ''
  }));

  // 1. Modify existing inline <style> tags
  styleElements.forEach(el => {
    el.textContent = replaceOklchWithRgb(el.textContent || '');
  });

  // 2. Fetch and convert external stylesheets, then substitute them
  const tempStyleTags: HTMLStyleElement[] = [];
  const linksToRestore: HTMLLinkElement[] = [];

  await Promise.all(
    linkElements.map(async link => {
      try {
        const url = new URL(link.href, window.location.origin);
        if (url.origin === window.location.origin) {
          const response = await fetch(link.href);
          if (response.ok) {
            const rawCss = await response.text();
            const convertedCss = replaceOklchWithRgb(rawCss);
            
            const tempStyle = document.createElement('style');
            tempStyle.setAttribute('data-temp-converted-css', 'true');
            tempStyle.textContent = convertedCss;
            document.head.appendChild(tempStyle);
            tempStyleTags.push(tempStyle);

            link.disabled = true;
            linksToRestore.push(link);
          }
        }
      } catch (err) {
        console.warn('Could not preprocess stylesheet link:', link.href, err);
      }
    })
  );

  return () => {
    // Restore inline styles
    originalStyleContents.forEach(({ el, content }) => {
      el.textContent = content;
    });

    // Remove temporary style tags
    tempStyleTags.forEach(el => el.remove());

    // Enable original link tags
    linksToRestore.forEach(link => {
      link.disabled = false;
    });
  };
};

interface ReportPdfViewerProps {
  report: Report;
  settings: Settings;
  onBack: () => void;
}

export default function ReportPdfViewer({ report, settings, onBack }: ReportPdfViewerProps) {
  const [downloading, setDownloading] = useState(false);
  const [done, setDone] = useState(false);
  
  // Refs for each printable page section to maintain pristine multi-page boundaries
  const page1Ref = useRef<HTMLDivElement>(null);
  const page2Ref = useRef<HTMLDivElement>(null);
  const page3Ref = useRef<HTMLDivElement>(null);
  const page4Ref = useRef<HTMLDivElement>(null);
  const page5Ref = useRef<HTMLDivElement>(null);

  const primaryColor = settings.primary_color || '#CFFE1C';
  const secondaryColor = settings.secondary_color || '#141414';

  const downloadPdf = async () => {
    setDownloading(true);
    setDone(false);
    let restoreStyles: (() => void) | null = null;

    try {
      restoreStyles = await prepareStyles();

      const pdf = new jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        format: 'a4',
      });

      const pages = [page1Ref, page2Ref, page3Ref, page4Ref, page5Ref];
      
      for (let i = 0; i < pages.length; i++) {
        const pageRef = pages[i];
        if (!pageRef.current) continue;

        // Force a brief delay to ensure images render
        const canvas = await html2canvas(pageRef.current, {
          scale: 2, // Retinal high resolution
          useCORS: true,
          backgroundColor: '#ffffff',
          logging: false
        });

        const imgData = canvas.toDataURL('image/jpeg', 0.95);
        
        // A4 Dimensions: 210mm x 297mm
        const pdfWidth = 210;
        const pdfHeight = 297;

        if (i > 0) {
          pdf.addPage();
        }

        pdf.addImage(imgData, 'JPEG', 0, 0, pdfWidth, pdfHeight, undefined, 'FAST');
      }

      // Save PDF with requested filename format: Business Name - Month Year Report.pdf
      const filename = `${report.business_name} - ${report.report_month} ${report.report_year} Report.pdf`;
      pdf.save(filename);
      
      setDone(true);
      setTimeout(() => setDone(false), 3000);
    } catch (error) {
      console.error('Error generating PDF:', error);
      alert('An error occurred during PDF compiling. Please try again.');
    } finally {
      if (restoreStyles) {
        restoreStyles();
      }
      setDownloading(false);
    }
  };

  // Safe calculators
  const totalSearch = Number(report.search_direct) + Number(report.search_discovery);
  const directPct = totalSearch > 0 ? Math.round((Number(report.search_direct) / totalSearch) * 100) : 0;
  const discoveryPct = totalSearch > 0 ? Math.round((Number(report.search_discovery) / totalSearch) * 100) : 0;

  const totalViews = Number(report.views_maps) + Number(report.views_search);
  const mapsPct = totalViews > 0 ? Math.round((Number(report.views_maps) / totalViews) * 100) : 0;
  const searchPct = totalViews > 0 ? Math.round((Number(report.views_search) / totalViews) * 100) : 0;

  return (
    <div className="space-y-6">
      {/* Top action bar */}
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-brand-card border border-brand-border px-5 py-4 rounded-2xl shadow-lg">
        <div className="flex items-center gap-3">
          <button
            onClick={onBack}
            className="p-2 bg-brand-bg hover:bg-brand-border border border-brand-border rounded-xl text-gray-400 hover:text-white transition"
          >
            <ArrowLeft className="w-4 h-4" />
          </button>
          <div>
            <h3 className="text-sm font-semibold text-white font-display flex items-center gap-1.5">
              <FileText className="w-4 h-4 text-brand-accent" />
              Interactive PDF Compiler
            </h3>
            <p className="text-xs text-gray-400">Preview report layouts and download high-resolution multi-page PDF documents.</p>
          </div>
        </div>

        <div className="flex gap-2">
          <button
            onClick={() => window.print()}
            className="flex items-center gap-1.5 text-xs font-semibold bg-brand-bg border border-brand-border text-gray-300 hover:text-white hover:bg-brand-border px-3.5 py-2 rounded-xl transition"
          >
            <Printer className="w-4 h-4" />
            Browser Print
          </button>
          <button
            onClick={downloadPdf}
            disabled={downloading}
            className="flex items-center gap-1.5 text-xs font-bold bg-brand-accent hover:opacity-90 text-black px-4 py-2 rounded-xl transition disabled:opacity-50 shadow-lg shadow-brand-accent/10"
          >
            {downloading ? (
              <>
                <div className="w-3.5 h-3.5 border-2 border-black border-t-transparent rounded-full animate-spin"></div>
                <span>Compiling pages...</span>
              </>
            ) : done ? (
              <>
                <Check className="w-4 h-4" />
                <span>Saved PDF!</span>
              </>
            ) : (
              <>
                <Download className="w-4 h-4" />
                <span>Download PDF Report</span>
              </>
            )}
          </button>
        </div>
      </div>

      {/* PDF Pages container (on-screen scaled representation) */}
      <div className="flex flex-col items-center gap-8 py-4 bg-brand-bg border border-brand-border rounded-2xl shadow-inner max-h-[800px] overflow-y-auto custom-scroll p-6">
        <div className="flex items-center gap-1.5 text-xs text-yellow-500 bg-yellow-950/20 border border-yellow-800/30 px-3 py-1.5 rounded-lg mb-2 max-w-xl text-center">
          <ShieldAlert className="w-4 h-4 shrink-0" />
          <span>This interactive preview mimics the physical A4 output. Renders pixel-perfect margins, background layouts, and alignments on PDF creation.</span>
        </div>

        {/* PAGE 1: COVER PAGE */}
        <div 
          ref={page1Ref} 
          className="relative bg-[#141414] text-white border border-[#2d2d2d] flex flex-col justify-between shrink-0"
          style={{ width: '210mm', height: '297mm', padding: '60px', backgroundColor: secondaryColor }}
        >
          {/* Header logo space */}
          <div className="flex justify-end h-16">
            {report.business_logo ? (
              <img src={report.business_logo} className="max-h-16 max-w-[200px] object-contain" alt="Client Logo" />
            ) : (
              <div className="w-24 h-12 bg-white/5 border border-white/10 rounded flex items-center justify-center text-[10px] text-gray-500">Logo Space</div>
            )}
          </div>

          {/* Center Titles */}
          <div className="my-auto space-y-4">
            <span className="text-xs font-bold uppercase tracking-wider text-brand-accent" style={{ color: primaryColor }}>Performance Evaluation</span>
            <h1 className="text-5xl font-black uppercase tracking-tight leading-none text-white font-display">
              SEO &amp; Google<br />Profile Report
            </h1>
            <div className="w-16 h-1" style={{ backgroundColor: primaryColor }}></div>
            <div className="pt-4">
              <h2 className="text-2xl font-semibold text-white font-display">{report.business_name}</h2>
              <p className="text-sm text-gray-400 mt-2 font-display">
                Reporting Month: {report.report_month} {report.report_year}
              </p>
              <p className="text-xs text-gray-500">
                Generated Date: {report.generated_date}
              </p>
            </div>
          </div>

          {/* Footer bar */}
          <div className="border-t border-white/10 pt-4 flex justify-between items-center text-xs text-gray-500">
            <span>Prepared By: {settings.default_company_name}</span>
            <span>Confidential GMB Analytics Report</span>
          </div>
          {/* Bottom Accent border */}
          <div className="absolute bottom-0 left-0 right-0 h-2" style={{ backgroundColor: primaryColor }}></div>
        </div>

        {/* PAGE 2: PERFORMANCE SUMMARY */}
        <div 
          ref={page2Ref} 
          className="bg-white text-gray-800 border border-gray-200 p-12 shrink-0 flex flex-col justify-between"
          style={{ width: '210mm', height: '297mm' }}
        >
          <div>
            <div className="flex justify-between items-center border-b-2 pb-3 mb-6" style={{ borderColor: primaryColor }}>
              <span className="text-base font-bold uppercase tracking-wider text-[#141414] font-display">GMB Profile Performance</span>
              <span className="text-xs text-gray-500">{report.business_name} &bull; {report.report_month} {report.report_year}</span>
            </div>

            <p className="text-xs text-gray-600 mb-6">
              This section provides an aggregated breakdown of discovery volumes, profile traffic views, and customer interactions recorded on Google Search and Maps channels.
            </p>

            {/* Stat Widgets Grid */}
            <div className="grid grid-cols-3 gap-4 mb-8">
              <div className="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center">
                <span className="text-[10px] text-gray-500 uppercase tracking-wider font-semibold block mb-1">People Viewed Profile</span>
                <span className="text-2xl font-bold block" style={{ color: secondaryColor }}>{Number(report.people_viewed).toLocaleString()}</span>
              </div>
              <div className="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center">
                <span className="text-[10px] text-gray-500 uppercase tracking-wider font-semibold block mb-1">Profile Interactions</span>
                <span className="text-2xl font-bold block" style={{ color: secondaryColor }}>{Number(report.profile_interactions).toLocaleString()}</span>
              </div>
              <div className="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center">
                <span className="text-[10px] text-gray-500 uppercase tracking-wider font-semibold block mb-1">Average Rating</span>
                <span className="text-2xl font-bold block" style={{ color: secondaryColor }}>{report.rating_average || '0.0'} ★</span>
              </div>
            </div>

            {/* Discovery Queries Section */}
            <h4 className="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3">Discovery Search Queries</h4>
            <table className="w-full text-xs text-left border border-gray-200 mb-8">
              <thead>
                <tr className="bg-gray-100 border-b border-gray-200 text-gray-700">
                  <th className="p-3">Query Classification</th>
                  <th className="p-3 text-right">Impressions / Volume</th>
                  <th className="p-3 text-right">Share Ratio</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100 text-gray-600">
                <tr>
                  <td className="p-3">
                    <strong>Direct Queries:</strong> Customers searching for your business name directly.
                  </td>
                  <td className="p-3 text-right">{Number(report.search_direct).toLocaleString()}</td>
                  <td className="p-3 text-right">{directPct}%</td>
                </tr>
                <tr>
                  <td className="p-3">
                    <strong>Discovery Queries:</strong> Customers searching category, service or product keywords.
                  </td>
                  <td className="p-3 text-right">{Number(report.search_discovery).toLocaleString()}</td>
                  <td className="p-3 text-right">{discoveryPct}%</td>
                </tr>
                <tr className="bg-gray-50 font-semibold text-gray-800">
                  <td className="p-3">Total Profile Search Clicks</td>
                  <td className="p-3 text-right">{totalSearch.toLocaleString()}</td>
                  <td className="p-3 text-right">100%</td>
                </tr>
              </tbody>
            </table>

            {/* Platform views */}
            <h4 className="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3">Maps vs Search Views Breakdown</h4>
            <table className="w-full text-xs text-left border border-gray-200">
              <thead>
                <tr className="bg-gray-100 border-b border-gray-200 text-gray-700">
                  <th className="p-3">Organic Listing Channels</th>
                  <th className="p-3 text-right">Impressions</th>
                  <th className="p-3 text-right">Channel Ratio</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100 text-gray-600">
                <tr>
                  <td className="p-3">Google Maps App &amp; Desktop Search views</td>
                  <td className="p-3 text-right">{Number(report.views_maps).toLocaleString()}</td>
                  <td className="p-3 text-right">{mapsPct}%</td>
                </tr>
                <tr>
                  <td className="p-3">Google Web Organic Search listings views</td>
                  <td className="p-3 text-right">{Number(report.views_search).toLocaleString()}</td>
                  <td className="p-3 text-right">{searchPct}%</td>
                </tr>
                <tr className="bg-gray-50 font-semibold text-gray-800">
                  <td className="p-3">Total Google Organic Views</td>
                  <td className="p-3 text-right">{totalViews.toLocaleString()}</td>
                  <td className="p-3 text-right">100%</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div className="border-t border-gray-100 pt-3 text-center text-[10px] text-gray-400">
            Page 2 &bull; {report.footer_notes}
          </div>
        </div>

        {/* PAGE 3: ORGANIC KEYWORDS & HEATMAP */}
        <div 
          ref={page3Ref} 
          className="bg-white text-gray-800 border border-gray-200 p-12 shrink-0 flex flex-col justify-between"
          style={{ width: '210mm', height: '297mm' }}
        >
          <div>
            <div className="flex justify-between items-center border-b-2 pb-3 mb-6" style={{ borderColor: primaryColor }}>
              <span className="text-base font-bold uppercase tracking-wider text-[#141414] font-display">Local Grid Heatmap &amp; Keywords</span>
              <span className="text-xs text-gray-500">{report.business_name} &bull; {report.report_month} {report.report_year}</span>
            </div>

            {/* Heatmap Section */}
            <h4 className="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3">Google Maps Geo-Coordinate Heatmap</h4>
            <div className="flex gap-6 items-start mb-6">
              <div className="w-[45%] shrink-0">
                {report.heatmap_image ? (
                  <img src={report.heatmap_image} className="w-full border border-gray-200 rounded-lg object-cover h-56" alt="Heatmap" />
                ) : (
                  <div className="w-full border-2 border-dashed border-gray-200 bg-gray-50 rounded-lg h-56 flex flex-col items-center justify-center text-center p-4">
                    <div className="w-8 h-8 rounded bg-[#CFFE1C]/10 flex items-center justify-center text-brand-accent font-semibold mb-1">G</div>
                    <span className="text-xs font-bold text-gray-700">7x7 Geo Grid visual</span>
                    <span className="text-[10px] text-gray-400 mt-1">Image not uploaded</span>
                  </div>
                )}
              </div>
              
              <div className="flex-1 space-y-4">
                <div className="bg-gray-50 border border-gray-200 rounded-lg p-3">
                  <span className="text-[9px] text-gray-500 uppercase tracking-wider block font-semibold">Average Position</span>
                  <span className="text-2xl font-extrabold text-gray-800">{report.avg_rank || '0.00'}</span>
                </div>
                <div className="bg-gray-50 border border-gray-200 rounded-lg p-3">
                  <span className="text-[9px] text-gray-500 uppercase tracking-wider block font-semibold">Top 3 Green Node share</span>
                  <span className="text-2xl font-extrabold text-gray-800">{report.top_3_percentage || '0.0'}%</span>
                </div>
                <div className="text-[11px] text-gray-500">
                  <strong>Tracked Grid Points:</strong> {report.points_tracked || 49} points<br />
                  Our geo-tracking tool queries real user devices across a localized matrix to verify GMB dominance.
                </div>
              </div>
            </div>

            {report.insight_text && (
              <div className="bg-[#f7f9fa] border-l-4 p-3.5 italic text-xs text-gray-600 mb-6" style={{ borderLeftColor: secondaryColor }}>
                <strong>Local Ranking Grid Insights:</strong> "{report.insight_text}"
              </div>
            )}

            {/* Keyword rankings table */}
            <h4 className="text-xs font-bold text-gray-800 uppercase tracking-wider mb-3">Primary Keyword Movements</h4>
            <table className="w-full text-xs text-left border border-gray-200">
              <thead>
                <tr className="bg-gray-100 border-b border-gray-200 text-gray-700">
                  <th className="p-2.5">Keyword Phrase</th>
                  <th className="p-2.5 text-center">Previous Rank</th>
                  <th className="p-2.5 text-center">Current Rank</th>
                  <th className="p-2.5 text-right">Movement Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-100 text-gray-600">
                {report.keyword_ranking && report.keyword_ranking.length > 0 ? (
                  report.keyword_ranking.map((kw, i) => {
                    const diff = Number(kw.prev_rank) - Number(kw.curr_rank);
                    return (
                      <tr key={i}>
                        <td className="p-2.5 font-semibold text-gray-800">{kw.keyword}</td>
                        <td className="p-2.5 text-center">{kw.prev_rank}</td>
                        <td className="p-2.5 text-center">{kw.curr_rank}</td>
                        <td className="p-2.5 text-right font-semibold">
                          {diff > 0 ? (
                            <span className="text-green-600">↑ Improved by {diff}</span>
                          ) : diff < 0 ? (
                            <span className="text-red-600">↓ Dropped by {Math.abs(diff)}</span>
                          ) : (
                            <span className="text-gray-400">● Stable</span>
                          )}
                        </td>
                      </tr>
                    );
                  })
                ) : (
                  <tr>
                    <td colSpan={4} className="p-4 text-center text-gray-400">No keyword metrics logged this month.</td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>

          <div className="border-t border-gray-100 pt-3 text-center text-[10px] text-gray-400">
            Page 3 &bull; {report.footer_notes}
          </div>
        </div>

        {/* PAGE 4: BACKLINK BUILDING */}
        <div 
          ref={page4Ref} 
          className="bg-white text-gray-800 border border-gray-200 p-12 shrink-0 flex flex-col justify-between"
          style={{ width: '210mm', height: '297mm' }}
        >
          <div>
            <div className="flex justify-between items-center border-b-2 pb-3 mb-6" style={{ borderColor: primaryColor }}>
              <span className="text-base font-bold uppercase tracking-wider text-[#141414] font-display">Backlink Catalogs &amp; Citations</span>
              <span className="text-xs text-gray-500">{report.business_name} &bull; {report.report_month} {report.report_year}</span>
            </div>

            <p className="text-xs text-gray-600 mb-6">
              Listing of authority URLs, social profile listings, and Web 2.0 editorial blogs indexed on behalf of your domain.
            </p>

            <div className="space-y-4 overflow-hidden" style={{ maxHeight: '200mm' }}>
              {Object.entries({
                business_listings: 'Business Directory Citations',
                profile_creations: 'Profile Authority Creations',
                web_2: 'Web 2.0 Networks',
                blogs: 'Editorial Blogs',
                google_stacking: 'Google Folder Stacks',
                google_stacking_properties: 'Google Properties Optimized',
                guest_posting: 'Guest Posting'
              }).map(([key, label]) => {
                const links = report.backlinks?.[key as keyof typeof report.backlinks] || [];
                if (links.length === 0) return null;

                return (
                  <div key={key} className="space-y-1.5">
                    <h5 className="text-[10px] font-bold text-gray-800 uppercase tracking-wider" style={{ color: secondaryColor }}>{label}</h5>
                    <table className="w-full text-[10px] text-left border border-gray-150">
                      <thead>
                        <tr className="bg-gray-50 text-gray-600 border-b border-gray-150">
                          <th className="py-1.5 px-3">Indexed Target Link</th>
                          <th className="py-1.5 px-3 text-right">Status</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-gray-100 text-gray-500">
                        {links.map((link, idx) => (
                          <tr key={idx}>
                            <td className="py-1 px-3 truncate font-mono text-[9px] max-w-lg">{link.url}</td>
                            <td className="py-1 px-3 text-right"><span className="bg-green-50 text-green-700 px-1.5 py-0.5 rounded text-[9px] font-bold">{link.status}</span></td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                );
              })}
            </div>
          </div>

          <div className="border-t border-gray-100 pt-3 text-center text-[10px] text-gray-400">
            Page 4 &bull; {report.footer_notes}
          </div>
        </div>

        {/* PAGE 5: STRATEGY PLAN & THANK YOU */}
        <div 
          ref={page5Ref} 
          className="bg-white text-gray-800 border border-gray-200 p-12 shrink-0 flex flex-col justify-between"
          style={{ width: '210mm', height: '297mm' }}
        >
          <div>
            <div className="flex justify-between items-center border-b-2 pb-3 mb-6" style={{ borderColor: primaryColor }}>
              <span className="text-base font-bold uppercase tracking-wider text-[#141414] font-display">July Strategy Action Plan</span>
              <span className="text-xs text-gray-500">{report.business_name} &bull; {report.report_month} {report.report_year}</span>
            </div>

            <p className="text-xs text-gray-600 mb-6">
              Our proposed SEO blueprints to further lock in your local maps green coordinates and citation network dominance:
            </p>

            <div className="bg-gray-50 border border-gray-200 rounded-lg p-6 text-xs leading-relaxed text-gray-700 mb-8 max-h-[120mm] overflow-hidden select-text">
              {report.next_month_plan ? (
                <div 
                  dangerouslySetInnerHTML={{ __html: report.next_month_plan }} 
                  className="prose prose-sm font-sans"
                />
              ) : (
                <p className="italic text-gray-400">No custom next month blueprint logged for this cycle.</p>
              )}
            </div>

            <div className="border-t-2 pt-6 mt-8" style={{ borderColor: secondaryColor }}>
              <h4 className="text-sm font-bold text-gray-800 uppercase tracking-wider mb-2">Thank You for Your Partnership</h4>
              <p className="text-xs text-gray-500 leading-relaxed mb-6">
                Our agency is committed to tracking maps grids, authority placements, and directory listings to secure your niche leadership. Reach our account division for any feedback.
              </p>
              
              <div className="bg-gray-50 border border-dashed border-gray-300 rounded p-4 flex justify-between items-end">
                <div className="text-xs text-gray-600 space-y-1">
                  <span className="font-bold text-gray-800 block">{settings.default_company_name}</span>
                  <span>Email: {report.company_email || settings.default_email}</span><br />
                  <span>Phone: {report.company_phone || settings.default_phone}</span><br />
                  <span>Website: {report.company_website || settings.default_website}</span>
                </div>
                <div className="text-[10px] text-gray-400 italic text-right">
                  Report verified by internal division.<br />
                  All Rights Reserved &copy; {new Date().getFullYear()}
                </div>
              </div>
            </div>
          </div>

          <div className="border-t border-gray-100 pt-3 text-center text-[10px] text-gray-400">
            Page 5 &bull; {report.footer_notes}
          </div>
        </div>

      </div>
    </div>
  );
}
