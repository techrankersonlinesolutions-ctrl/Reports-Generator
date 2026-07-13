export interface Keyword {
  keyword: string;
  prev_rank: number;
  curr_rank: number;
}

export interface Backlink {
  category: string;
  url: string;
  status: string;
}

export interface Report {
  id: string;
  business_name: string;
  report_month: string;
  report_year: string;
  generated_date: string;
  business_logo: string;
  cover_image: string;
  
  // Step 2: Performance
  people_viewed: number;
  search_direct: number;
  search_discovery: number;
  profile_interactions: number;
  reviews_count: number;
  rating_average: number;
  views_maps: number;
  views_search: number;
  
  // Step 3
  keyword_ranking: Keyword[];
  
  // Step 4: Map grid
  heatmap_image: string;
  avg_rank: number;
  top_3_percentage: number;
  points_tracked: number;
  insight_text: string;
  
  // Step 5: Backlinks
  backlinks: {
    business_listings: Backlink[];
    profile_creations: Backlink[];
    web_2: Backlink[];
    blogs: Backlink[];
    google_stacking: Backlink[];
    google_stacking_properties: Backlink[];
    guest_posting: Backlink[];
  };
  
  // Step 6: Geo Fence
  geofence_map_url: string;
  
  // Step 7: Strategy plan
  next_month_plan: string;
  
  // Step 8: Thank You page
  company_email: string;
  company_phone: string;
  company_website: string;
  footer_notes: string;
}

export interface Settings {
  default_company_name: string;
  default_email: string;
  default_phone: string;
  default_website: string;
  default_footer: string;
  pdf_margin_top: number;
  pdf_margin_bottom: number;
  pdf_margin_left: number;
  pdf_margin_right: number;
  primary_color: string;
  secondary_color: string;
}

export interface User {
  id: number;
  name: string;
  email: string;
  company_name?: string;
  company_phone?: string;
  company_email?: string;
  company_website?: string;
  company_footer?: string;
}
