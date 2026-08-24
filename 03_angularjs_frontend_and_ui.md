# Phase 2: AngularJS Frontend, Google Maps UI, and Export Panel

## SYSTEM RULES REMINDER
- NO HTML/JS comments except `TODO:`.
- All UI text (buttons, tables, alerts, titles) MUST be in fluent Turkish.
- All code logic (controllers, scopes, variables) MUST be in English.
- Provide COMPLETE files.

### Tasks for this Prompt:
1. **Base Layout & Routing:** Set up the AngularJS application with a simple, clean, and user-friendly interface. Configure routes for `/panel` (User Dashboard) and `/admin` (Admin Panel).
2. **Admin Panel:** Create a very simple view for the admin panel that currently only displays a clean interface with the text "Merhaba Admin" (Hello Admin) in Turkish. No other logic is needed for now.
3. **User Panel (Google Maps Integration):**
   - Integrate Google Maps purely for the visual map interface (using the standard Maps JavaScript API for rendering only).
   - Implement drawing tools to allow the user to draw a circular area on the map.
   - When the circle is drawn, extract the center coordinates and radius, and send a request to the Laravel API created in Phase 2.
4. **Data Table & Exports:**
   - Display the scraped business data (İşletme Adı, Açık Adres, Ortalama Yıldız, Telefon, E-posta) in a clean table.
   - Add three buttons for exporting the table data: "Excel Olarak İndir", "PDF Olarak İndir", "Resim Olarak İndir". Wire these to the backend export endpoints.
5. Provide the complete HTML views, AngularJS controllers, and service files.

At the end, provide the Git commands to checkout a new branch `feature/frontend-maps-ui`, commit, and push to `asisec/maps-scraper`.