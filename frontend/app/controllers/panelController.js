angular.module("mapsScraperApp").controller("PanelController", [
    "$scope",
    "$timeout",
    "ScraperService",
    function ($scope, $timeout, ScraperService) {
        $scope.businesses = [];
        $scope.isLoading = false;
        $scope.errorMessage = null;
        $scope.successMessage = null;
        $scope.selectedCircle = null;
        $scope.mapInstance = null;
        $scope.drawingManager = null;
        $scope.markers = [];
        $scope.infoWindow = null;
        $scope.currentJobId = null;
        $scope.centerLat = null;
        $scope.centerLng = null;
        $scope.circleRadius = null;
        $scope.searchQuery = "";

        $scope.initMap = function () {
            const defaultCenter = { lat: 39.9334, lng: 32.8597 };
            const mapOptions = {
                center: defaultCenter,
                zoom: 13,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: true,
                streetViewControl: false,
                fullscreenControl: true,
            };

            $scope.mapInstance = new google.maps.Map(
                document.getElementById("map-canvas"),
                mapOptions
            );

            $scope.infoWindow = new google.maps.InfoWindow();

            $scope.drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.CIRCLE,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [google.maps.drawing.OverlayType.CIRCLE],
                },
                circleOptions: {
                    fillColor: "#1976D2",
                    fillOpacity: 0.2,
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

                    $scope.updateCircleStats();

                    google.maps.event.addListener(circle, "radius_changed", function () {
                        $scope.$apply(function () {
                            $scope.updateCircleStats();
                        });
                    });

                    google.maps.event.addListener(circle, "center_changed", function () {
                        $scope.$apply(function () {
                            $scope.updateCircleStats();
                        });
                    });

                    $scope.$apply();
                }
            );
        };

        $scope.updateCircleStats = function () {
            if (!$scope.selectedCircle) {
                return;
            }
            const center = $scope.selectedCircle.getCenter();
            $scope.centerLat = center.lat().toFixed(6);
            $scope.centerLng = center.lng().toFixed(6);
            $scope.circleRadius = Math.round($scope.selectedCircle.getRadius());
        };

        $scope.clearSelection = function () {
            if ($scope.selectedCircle) {
                $scope.selectedCircle.setMap(null);
                $scope.selectedCircle = null;
            }
            $scope.clearMarkers();
            $scope.centerLat = null;
            $scope.centerLng = null;
            $scope.circleRadius = null;
            $scope.businesses = [];
            $scope.errorMessage = null;
            $scope.successMessage = null;
            $scope.currentJobId = null;

            if ($scope.drawingManager) {
                $scope.drawingManager.setDrawingMode(google.maps.drawing.OverlayType.CIRCLE);
            }
        };

        $scope.clearMarkers = function () {
            for (let i = 0; i < $scope.markers.length; i++) {
                $scope.markers[i].setMap(null);
            }
            $scope.markers = [];
        };

        $scope.renderMarkers = function () {
            $scope.clearMarkers();
            if (!$scope.businesses || $scope.businesses.length === 0 || !$scope.mapInstance) {
                return;
            }

            const bounds = new google.maps.LatLngBounds();

            $scope.businesses.forEach(function (biz, idx) {
                if (biz.latitude && biz.longitude) {
                    const position = { lat: parseFloat(biz.latitude), lng: parseFloat(biz.longitude) };
                    const marker = new google.maps.Marker({
                        position: position,
                        map: $scope.mapInstance,
                        title: biz.name,
                        label: (idx + 1).toString(),
                        animation: google.maps.Animation.DROP,
                    });

                    marker.addListener("click", function () {
                        const content = "<div style=\"padding: 8px; max-width: 250px;\">" +
                            "<h6 style=\"margin: 0 0 5px 0; font-weight: bold; color: #1976D2;\">" + biz.name + "</h6>" +
                            "<p style=\"margin: 0 0 5px 0; font-size: 12px; color: #555;\">" + (biz.address || "-") + "</p>" +
                            "<p style=\"margin: 0; font-size: 12px;\"><strong>Telefon:</strong> " + (biz.phone || "-") + "</p>" +
                            (biz.rating ? "<p style=\"margin: 3px 0 0 0; color: #f57f17; font-weight: bold;\">★ " + biz.rating + "</p>" : "") +
                            "</div>";

                        $scope.infoWindow.setContent(content);
                        $scope.infoWindow.open($scope.mapInstance, marker);
                    });

                    $scope.markers.push(marker);
                    bounds.extend(position);
                }
            });

            if ($scope.markers.length > 0) {
                $scope.mapInstance.fitBounds(bounds);
            }
        };

        $scope.startScrape = function () {
            if (!$scope.selectedCircle) {
                $scope.errorMessage = "Lütfen harita üzerinde dairesel bir tarama alanı çizin.";
                $scope.successMessage = null;
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
                    $scope.businesses = response.data.data || [];
                    $scope.currentJobId = response.data.job_id || null;
                    $scope.successMessage = ($scope.businesses.length > 0)
                        ? ($scope.businesses.length + " adet işletme başarıyla tarandı ve listelendi.")
                        : "Seçilen alanda işletme bulunamadı.";

                    $timeout(function () {
                        $scope.renderMarkers();
                    }, 100);
                })
                .catch(function (error) {
                    $scope.errorMessage = (error.data && error.data.message)
                        ? error.data.message
                        : "Tarama işlemi sırasında bir hata oluştu. Lütfen bağlantınızı kontrol edip tekrar deneyin.";
                })
                .finally(function () {
                    $scope.isLoading = false;
                });
        };

        $scope.exportExcel = function () {
            ScraperService.exportExcel($scope.currentJobId);
        };

        $scope.exportPdf = function () {
            ScraperService.exportPdf($scope.currentJobId);
        };

        $scope.exportImage = function () {
            ScraperService.exportImage($scope.currentJobId);
        };

        $scope.highlightMarker = function (index) {
            if ($scope.markers[index]) {
                google.maps.event.trigger($scope.markers[index], "click");
                $scope.mapInstance.panTo($scope.markers[index].getPosition());
            }
        };

        angular.element(document).ready(function () {
            if (typeof google !== "undefined" && typeof google.maps !== "undefined") {
                $scope.initMap();
            } else {
                window.initGoogleMaps = function () {
                    $scope.$apply(function () {
                        $scope.initMap();
                    });
                };
            }
        });
    },
]);