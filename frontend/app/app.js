angular.module("mapsScraperApp", ["ngRoute"])
    .config(["$routeProvider", function ($routeProvider) {
        $routeProvider
            .when("/panel", {
                templateUrl: "app/views/panel.html",
                controller: "PanelController",
            })
            .when("/admin", {
                templateUrl: "app/views/admin.html",
                controller: "AdminController",
            })
            .otherwise({ redirectTo: "/panel" });
    }])
    .constant("API_URL", "http://localhost:8000/api");