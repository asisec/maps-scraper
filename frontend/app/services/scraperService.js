angular.module("mapsScraperApp").service("ScraperService", [
    "$http",
    "$window",
    "API_URL",
    function ($http, $window, API_URL) {
        this.scrape = function (lat, lng, radius) {
            return $http.post(API_URL + "/scrape", {
                latitude: lat,
                longitude: lng,
                radius: radius,
            });
        };

        this.exportExcel = function () {
            $window.open(API_URL + "/export/excel", "_blank");
        };

        this.exportPdf = function () {
            $window.open(API_URL + "/export/pdf", "_blank");
        };

        this.exportImage = function () {
            $window.open(API_URL + "/export/image", "_blank");
        };
    },
]);
