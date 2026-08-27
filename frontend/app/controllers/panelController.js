angular.module("mapsScraperApp").controller("PanelController", [
    "$scope",
    "$timeout",
    "ScraperService",
    function ($scope, $timeout, ScraperService) {
        $scope.businesses = [];
        $scope.filteredBusinesses = [];
        $scope.stats = {
            total: 0,
            withWebsite: 0,
            withEmail: 0,
            withPhone: 0,
            withSocial: 0,
        };
        $scope.searchQuery = "";
        $scope.activeFilter = "all";
        $scope.selectedBusiness = null;
        $scope.isLoading = false;
        $scope.errorMessage = null;
        $scope.successMessage = null;
        $scope.map = null;
        $scope.circleLayer = null;
        $scope.centerMarker = null;
        $scope.radiusMarker = null;
        $scope.businessMarkers = [];
        $scope.currentJobId = null;
        $scope.centerLat = null;
        $scope.centerLng = null;
        $scope.circleRadius = 1000;
        $scope.isDrawing = false;
        $scope.hasSelectedArea = false;

        $scope.initMap = function () {
            if ($scope.map) {
                $scope.map.remove();
            }

            const defaultLat = 39.9334;
            const defaultLng = 32.8597;

            $scope.map = L.map("map-canvas", {
                center: [defaultLat, defaultLng],
                zoom: 13,
                zoomControl: true,
            });

            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19,
            }).addTo($scope.map);

            $scope.map.on("click", function (e) {
                $scope.$apply(function () {
                    $scope.createCircleAt(e.latlng.lat, e.latlng.lng, $scope.circleRadius || 1000);
                });
            });

            $timeout(function () {
                $scope.map.invalidateSize();
                $scope.createCircleAt(defaultLat, defaultLng, 1000);
            }, 250);
        };

        $scope.createCircleAt = function (lat, lng, radius) {
            $scope.centerLat = parseFloat(lat.toFixed(6));
            $scope.centerLng = parseFloat(lng.toFixed(6));
            $scope.circleRadius = Math.round(radius);
            $scope.hasSelectedArea = true;
            $scope.errorMessage = null;

            if ($scope.circleLayer) {
                $scope.map.removeLayer($scope.circleLayer);
            }
            if ($scope.centerMarker) {
                $scope.map.removeLayer($scope.centerMarker);
            }
            if ($scope.radiusMarker) {
                $scope.map.removeLayer($scope.radiusMarker);
            }

            $scope.circleLayer = L.circle([$scope.centerLat, $scope.centerLng], {
                color: "#4f46e5",
                fillColor: "#6366f1",
                fillOpacity: 0.2,
                weight: 2,
                radius: $scope.circleRadius,
            }).addTo($scope.map);

            const centerIcon = L.divIcon({
                className: "custom-center-marker",
                html: '<div style="background-color:#4f46e5; width:14px; height:14px; border-radius:50%; border:3px solid #fff; box-shadow:0 0 6px rgba(0,0,0,0.5);"></div>',
                iconSize: [14, 14],
                iconAnchor: [7, 7],
            });

            $scope.centerMarker = L.marker([$scope.centerLat, $scope.centerLng], {
                icon: centerIcon,
                draggable: true,
            }).addTo($scope.map);

            $scope.centerMarker.on("drag", function (e) {
                $scope.$apply(function () {
                    const newPos = e.target.getLatLng();
                    $scope.centerLat = parseFloat(newPos.lat.toFixed(6));
                    $scope.centerLng = parseFloat(newPos.lng.toFixed(6));
                    $scope.circleLayer.setLatLng(newPos);
                    $scope.updateRadiusMarkerPosition();
                });
            });

            $scope.updateRadiusMarkerPosition();
        };

        $scope.updateRadiusMarkerPosition = function () {
            if (!$scope.centerLat || !$scope.centerLng || !$scope.circleRadius) {
                return;
            }

            const earthRadius = 6378137;
            const dLat = 0;
            const dLng = ($scope.circleRadius / (earthRadius * Math.cos(Math.PI * $scope.centerLat / 180))) * (180 / Math.PI);
            const edgePos = L.latLng($scope.centerLat + dLat, $scope.centerLng + dLng);

            if ($scope.radiusMarker) {
                $scope.radiusMarker.setLatLng(edgePos);
            } else {
                const handleIcon = L.divIcon({
                    className: "custom-radius-marker",
                    html: '<div style="background-color:#f59e0b; width:12px; height:12px; border-radius:50%; border:2px solid #fff; box-shadow:0 0 4px rgba(0,0,0,0.6); cursor:ew-resize;"></div>',
                    iconSize: [12, 12],
                    iconAnchor: [6, 6],
                });

                $scope.radiusMarker = L.marker(edgePos, {
                    icon: handleIcon,
                    draggable: true,
                }).addTo($scope.map);

                $scope.radiusMarker.on("drag", function (e) {
                    $scope.$apply(function () {
                        const newEdgePos = e.target.getLatLng();
                        const centerPos = L.latLng($scope.centerLat, $scope.centerLng);
                        const newRadius = centerPos.distanceTo(newEdgePos);
                        $scope.circleRadius = Math.max(50, Math.round(newRadius));
                        $scope.circleLayer.setRadius($scope.circleRadius);
                    });
                });
            }
        };

        $scope.setRadiusPreset = function (radiusMeters) {
            $scope.circleRadius = radiusMeters;
            if ($scope.circleLayer) {
                $scope.circleLayer.setRadius(radiusMeters);
                $scope.updateRadiusMarkerPosition();
            }
        };

        $scope.clearSelection = function () {
            if ($scope.circleLayer) {
                $scope.map.removeLayer($scope.circleLayer);
                $scope.circleLayer = null;
            }
            if ($scope.centerMarker) {
                $scope.map.removeLayer($scope.centerMarker);
                $scope.centerMarker = null;
            }
            if ($scope.radiusMarker) {
                $scope.map.removeLayer($scope.radiusMarker);
                $scope.radiusMarker = null;
            }
            $scope.clearBusinessMarkers();
            $scope.centerLat = null;
            $scope.centerLng = null;
            $scope.hasSelectedArea = false;
            $scope.businesses = [];
            $scope.filteredBusinesses = [];
            $scope.updateStats();
            $scope.errorMessage = null;
            $scope.successMessage = null;
            $scope.currentJobId = null;
        };

        $scope.clearBusinessMarkers = function () {
            for (let i = 0; i < $scope.businessMarkers.length; i++) {
                $scope.map.removeLayer($scope.businessMarkers[i]);
            }
            $scope.businessMarkers = [];
        };

        $scope.renderBusinessMarkers = function () {
            $scope.clearBusinessMarkers();
            if (!$scope.filteredBusinesses || $scope.filteredBusinesses.length === 0 || !$scope.map) {
                return;
            }

            $scope.filteredBusinesses.forEach(function (biz, idx) {
                if (biz.latitude && biz.longitude) {
                    const lat = parseFloat(biz.latitude);
                    const lng = parseFloat(biz.longitude);

                    const markerHtml = '<div class="biz-map-pin">' +
                        '<span>' + (idx + 1) + '</span>' +
                        '</div>';

                    const icon = L.divIcon({
                        className: "biz-pin-wrapper",
                        html: markerHtml,
                        iconSize: [28, 28],
                        iconAnchor: [14, 28],
                        popupAnchor: [0, -28],
                    });

                    const marker = L.marker([lat, lng], { icon: icon }).addTo($scope.map);

                    const popupContent = '<div style="min-width: 240px; font-family: sans-serif; padding: 4px;">' +
                        '<h6 style="margin: 0 0 6px 0; font-weight: bold; color: #4f46e5; font-size: 14px;">' + biz.name + '</h6>' +
                        '<p style="margin: 0 0 6px 0; font-size: 12px; color: #64748b;"><i class="fas fa-map-pin" style="color:#ef4444; margin-right:4px;"></i>' + (biz.address || "-") + '</p>' +
                        (biz.phone ? '<p style="margin: 0 0 4px 0; font-size: 12px;"><strong>Tel:</strong> <a href="tel:' + biz.phone + '" style="color:#0f172a; text-decoration:none;">' + biz.phone + '</a></p>' : '') +
                        (biz.email ? '<p style="margin: 0 0 4px 0; font-size: 12px;"><strong>E-posta:</strong> <a href="mailto:' + biz.email + '" style="color:#4f46e5; text-decoration:none;">' + biz.email + '</a></p>' : '') +
                        (biz.website ? '<p style="margin: 0 0 4px 0; font-size: 12px;"><strong>Web:</strong> <a href="' + biz.website + '" target="_blank" style="color:#2563eb; font-weight:600;">Siteyi Ziyaret Et &rarr;</a></p>' : '') +
                        (biz.rating ? '<p style="margin: 4px 0 0 0; font-size: 12px; color: #f59e0b; font-weight: bold;">★ ' + biz.rating + (biz.reviews_count ? ' (' + biz.reviews_count + ')' : '') + '</p>' : '') +
                        '</div>';

                    marker.bindPopup(popupContent);
                    $scope.businessMarkers.push(marker);
                }
            });

            if ($scope.circleLayer) {
                $scope.map.fitBounds($scope.circleLayer.getBounds(), { padding: [30, 30] });
            }
        };

        $scope.updateStats = function () {
            const list = $scope.businesses || [];
            let withWeb = 0;
            let withMail = 0;
            let withPh = 0;
            let withSoc = 0;

            for (let i = 0; i < list.length; i++) {
                const b = list[i];
                if (b.website) {
                    withWeb++;
                }
                if (b.email || (b.emails && b.emails.length > 0)) {
                    withMail++;
                }
                if (b.phone || (b.phones && b.phones.length > 0)) {
                    withPh++;
                }
                if (b.whatsapp || (b.social_links && Object.keys(b.social_links).length > 0)) {
                    withSoc++;
                }
            }

            $scope.stats = {
                total: list.length,
                withWebsite: withWeb,
                withEmail: withMail,
                withPhone: withPh,
                withSocial: withSoc,
            };
        };

        $scope.setFilter = function (filterType) {
            $scope.activeFilter = filterType;
            $scope.applyFilter();
        };

        $scope.applyFilter = function () {
            let result = $scope.businesses || [];

            if ($scope.activeFilter === "website") {
                result = result.filter(function (b) { return !!b.website; });
            } else if ($scope.activeFilter === "email") {
                result = result.filter(function (b) { return !!b.email || (b.emails && b.emails.length > 0); });
            } else if ($scope.activeFilter === "phone") {
                result = result.filter(function (b) { return !!b.phone || (b.phones && b.phones.length > 0); });
            }

            if ($scope.searchQuery && $scope.searchQuery.trim() !== "") {
                const q = $scope.searchQuery.toLowerCase().trim();
                result = result.filter(function (b) {
                    const name = (b.name || "").toLowerCase();
                    const addr = (b.address || "").toLowerCase();
                    const phone = (b.phone || "").toLowerCase();
                    const email = (b.email || "").toLowerCase();
                    const web = (b.website || "").toLowerCase();
                    return name.includes(q) || addr.includes(q) || phone.includes(q) || email.includes(q) || web.includes(q);
                });
            }

            $scope.filteredBusinesses = result;
            $timeout(function () {
                $scope.renderBusinessMarkers();
            }, 50);
        };

        $scope.openDetailModal = function (business) {
            $scope.selectedBusiness = business;
            const modalEl = document.getElementById("businessDetailModal");
            if (modalEl && window.bootstrap) {
                const modal = new window.bootstrap.Modal(modalEl);
                modal.show();
            }
        };

        $scope.startScrape = function () {
            if (!$scope.hasSelectedArea || !$scope.centerLat || !$scope.centerLng || !$scope.circleRadius) {
                $scope.errorMessage = "Lütfen harita üzerinde tıklayarak bir tarama alanı seçin.";
                $scope.successMessage = null;
                return;
            }

            $scope.isLoading = true;
            $scope.errorMessage = null;
            $scope.successMessage = null;
            $scope.businesses = [];
            $scope.filteredBusinesses = [];

            ScraperService.scrape($scope.centerLat, $scope.centerLng, $scope.circleRadius)
                .then(function (response) {
                    $scope.businesses = response.data.data || [];
                    $scope.currentJobId = response.data.job_id || null;
                    $scope.updateStats();
                    $scope.applyFilter();
                    $scope.successMessage = ($scope.businesses.length > 0)
                        ? ($scope.businesses.length + " adet işletme ve iletişim bilgileri başarıyla tarandı.")
                        : "Seçilen alanda işletme bulunamadı.";
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
            if ($scope.businessMarkers[index]) {
                const marker = $scope.businessMarkers[index];
                $scope.map.setView(marker.getLatLng(), 16);
                marker.openPopup();
            }
        };

        angular.element(document).ready(function () {
            $scope.initMap();
        });
    },
]);