angular.module("mapsScraperApp").controller("PanelController", [
    "$scope",
    "ScraperService",
    function ($scope, ScraperService) {
        $scope.businesses = [];
        $scope.isLoading = false;
        $scope.errorMessage = null;
        $scope.successMessage = null;
        $scope.selectedCircle = null;
        $scope.mapInstance = null;
        $scope.drawingManager = null;

        $scope.initMap = function () {
            const mapOptions = {
                center: { lat: 39.9334, lng: 32.8597 },
                zoom: 12,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
            };

            $scope.mapInstance = new google.maps.Map(
                document.getElementById("map-canvas"),
                mapOptions
            );

            $scope.drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.CIRCLE,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [google.maps.drawing.OverlayType.CIRCLE],
                },
                circleOptions: {
                    fillColor: "#1976D2",
                    fillOpacity: 0.25,
                    strokeWeight: 2,
                    strokeColor: "#1976D2",
                    clickable: false,
                    editable: true,
                    zIndex: 1,
                },
            });

            $scope.drawingManager.setMap($scope.mapInstance);

            google.maps.event.addListener(
                $scope.drawingManager,
                "circlecomplete",
                function (circle) {
                    if ($scope.selectedCircle) {
                        $scope.selectedCircle.setMap(null);
                    }
                    $scope.selectedCircle = circle;
                    $scope.drawingManager.setDrawingMode(null);
                }
            );
        };

        $scope.startScrape = function () {
            if (!$scope.selectedCircle) {
                $scope.errorMessage = "Lütfen haritada bir daire çizin.";
                return;
            }

            const center = $scope.selectedCircle.getCenter();
            const radius = Math.round($scope.selectedCircle.getRadius());

            $scope.isLoading = true;
            $scope.errorMessage = null;
            $scope.successMessage = null;
            $scope.businesses = [];

            ScraperService.scrape(center.lat(), center.lng(), radius)
                .then(function (response) {
                    $scope.businesses = response.data.data;
                    $scope.successMessage =
                        response.data.data.length +
                        " işletme başarıyla tarandı.";
                })
                .catch(function (error) {
                    $scope.errorMessage =
                        error.data && error.data.message
                            ? error.data.message
                            : "Tarama sırasında bir hata oluştu. Lütfen tekrar deneyin.";
                })
                .finally(function () {
                    $scope.isLoading = false;
                });
        };

        $scope.exportExcel = function () {
            ScraperService.exportExcel();
        };

        $scope.exportPdf = function () {
            ScraperService.exportPdf();
        };

        $scope.exportImage = function () {
            ScraperService.exportImage();
        };

        angular.element(document).ready(function () {
            if (typeof google !== "undefined") {
                $scope.initMap();
            }
        });
    },
]);