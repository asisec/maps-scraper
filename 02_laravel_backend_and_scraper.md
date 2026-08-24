# Phase 2: Laravel Backend, MongoDB Integration, and Scraper Service

## SYSTEM RULES REMINDER
- NO comments in code except `TODO:` where strictly necessary.
- Turkish for all user-facing text (API error messages in JSON).
- English for all variables, functions, classes, and routing.
- Provide COMPLETE, production-ready code blocks.

### Tasks for this Prompt:
1. **MongoDB Configuration:** Set up Laravel to connect to MongoDB using `mongodb/laravel-mongodb` package. Provide the necessary database configuration files.
2. **Swagger UI:** Integrate Swagger UI (e.g., `darkaonline/l5-swagger`) and provide the complete controller and annotations for the API documentation.
3. **Scraper Service (Core Logic):**
   - Create a service class that takes a central coordinate (lat, lng) and a radius.
   - Implement the logic to scrape business data from Google Maps (Name, Full Address, Average Star Rating, Phone Numbers, Emails) without using the paid Places API. You can utilize Goutte, Puppeteer/Browsershot, or raw HTTP client strategies for open-source scraping.
   - Do NOT filter by business type. Scrape everything in the radius.
   - Add `TODO:` notes in the scraping algorithm where DOM selectors might need future updates.
4. **Export Service:** 
   - Create endpoints and services to export the scraped JSON data into:
     - XLSX (using `maatwebsite/excel`)
     - PDF (using `barryvdh/laravel-dompdf`)
     - Image format (PNG/JPG using `spatie/browsershot` or similar).
5. **API Endpoints:** Create the necessary routes and controllers to trigger the scrape and trigger the exports.

Provide the complete code for the Controllers, Services, Routes, and Configs. 
At the end, provide the Git commands to checkout a new branch `feature/backend-scraper-api`, commit, and push to `asisec/maps-scraper`.