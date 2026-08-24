db = db.getSiblingDB("maps_scraper");

db.createUser({
    user: "maps_user",
    pwd: "maps_password",
    roles: [{ role: "readWrite", db: "maps_scraper" }],
});

db.createCollection("businesses");
db.createCollection("scrape_jobs");
