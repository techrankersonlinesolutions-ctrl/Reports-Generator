# Eagle Reports Generator (PHP MVC Edition)

A professional, cPanel-ready web application built with robust Object-Oriented PHP 8+, a clean MVC architecture, vanilla JavaScript, and Bootstrap 5. It features a complete multi-step report builder specifically crafted for SEO agencies.

## Features
- **Simple Security Gate:** staff login portal featuring session protections, password hashes, and CSRF guard tags.
- **Agency Metrics Dashboard:** live counters tracking global volumes, monthly generations, and recent reports.
- **8-Step Report Wizard:**
  - *Step 1: Basic Info* — uploads custom client logos and covers.
  - *Step 2: Performance Summary* — tracks GMB profile interactions, rating scales, and search breakdown ratios.
  - *Step 3: Keyword Rankings* — custom dynamic table utilizing inline JS appenders.
  - *Step 4: Map Ranking Grid* — uploads local green node grids with insight summaries.
  - *Step 5: Authority Backlinks* — separate dynamic catalogs (Citations, Web 2.0s, Google Stacks, Guest Posts).
  - *Step 6: Geofencing Mapping* — embed iframe or Maps configurations.
  - *Step 7: Action Strategy Plan* — HTML prefilled formatting for monthly deliverables.
  - *Step 8: Thank You page* — pre-populates corporate settings and details.
- **One-Click Rollover Duplications:** rolls current rankings into "previous ranks" automatically for the new month, ready for fast metric updates.
- **Print & mPDF Engines:** generates high-resolution, branded PDF documents based on custom templates.

## Directory Layout
- `/config`: Database connections and app configs.
- `/controllers`: Authentication, dashboard widgets, and report/profile processes.
- `/models`: User, Report, and Setting schemas (PDO prepared statements).
- `/views`: Pure PHP responsive layout templates and step-by-step form designs.
- `/database`: Database schema `.sql` backup.
- `/uploads`: Resized, secured images.
- `/pdf`: PDF temporary caching folders.
- `/composer.json`: Dependency listing for mPDF.
