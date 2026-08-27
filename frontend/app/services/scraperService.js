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

        this.getBusinesses = function (jobId) {
            const params = jobId ? { job_id: jobId } : {};
            return $http.get(API_URL + "/businesses", { params: params });
        };

        this.exportExcel = function (jobId) {
            const url = API_URL + "/export/excel" + (jobId ? "?job_id=" + encodeURIComponent(jobId) : "");
            $window.open(url, "_blank");
        };

        this.exportPdf = function (jobId) {
            const url = API_URL + "/export/pdf" + (jobId ? "?job_id=" + encodeURIComponent(jobId) : "");
            $window.open(url, "_blank");
        };

        this.exportImage = function (jobId) {
            const url = API_URL + "/export/image" + (jobId ? "?job_id=" + encodeURIComponent(jobId) : "");
            $window.open(url, "_blank");
        };
    },
]);