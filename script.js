document.addEventListener("DOMContentLoaded", function () {
    function localTime() {
        const d = new Date();
        document.getElementById("time").innerHTML = hours(d.getHours()) + ":" + minutes(d.getMinutes()) + " " + timetype(d.getHours());
    }

localTime();

    setInterval(localTime, 1000);

    function hours(value) {
        return value < 10 ? "0" + value : value > 12 ? value - 12 : value;
    }

    function minutes(value) {
        return value < 10 ? "0" + value : value;
    }

    function timetype(value) {
        return value < 12 ? "AM" : "PM";
    }

    var accessToken = "pk.1da2a6c2605a05780b17d7810c280ae0";
    var mapContainer = document.getElementById("map");

    if (!mapContainer) {
        console.error("Map container not found!");
        return;
    }

    var map = L.map('map').setView([14.5995, 120.9842], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var userLat = position.coords.latitude;
            var userLon = position.coords.longitude;
            map.setView([userLat, userLon], 15);
            L.marker([userLat, userLon]).addTo(map).bindPopup("You are here!").openPopup();
        }, function () {
            console.warn("Geolocation permission denied.");
        });
    }

    function searchLocation() {
        var query = document.getElementById('search-box').value;
        if (!query) {
            alert("Please enter a location!");
            return;
        }

        fetch(`https://us1.locationiq.com/v1/search.php?key=${accessToken}&q=${query}&countrycodes=PH&format=json`)
            .then(response => response.json())
            .then(data => {
                console.log("Search Data:", data); // Debugging

                if (data.length > 0) {
                    var lat = data[0].lat, lon = data[0].lon;
                    L.marker([lat, lon]).addTo(map).bindPopup(query).openPopup();
                    map.setView([lat, lon], 15);
                } else {
                    alert("Location not found!");
                }
            })
            .catch(error => console.error("Error:", error));
    }

    document.getElementById("search-box").addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            searchLocation();
        }
    });

    function fetchSuggestions() {
        var query = document.getElementById('search-box').value;
        if (query.length < 3) {
            document.getElementById('suggestions').style.display = "none";
            return;
        }

        fetch(`https://us1.locationiq.com/v1/autocomplete.php?key=${accessToken}&q=${query}&countrycodes=PH&format=json`)
            .then(response => response.json())
            .then(data => {
                console.log("Autocomplete Data:", data); // Debugging

                var suggestionsBox = document.getElementById('suggestions');
                suggestionsBox.innerHTML = "";

                if (!Array.isArray(data) || data.length === 0) {
                    console.warn("No suggestions found.");
                    suggestionsBox.style.display = "none";
                    return;
                }

                data.forEach(location => {
                    if (location.address && location.address.country_code === "ph") {
                        var div = document.createElement("div");
                        div.textContent = location.display_name;
                        div.classList.add("suggestion-item"); // Add class for styling
                        div.onclick = function () {
                            document.getElementById('search-box').value = location.display_name;
                            searchLocation();
                            suggestionsBox.style.display = "none";
                        };
                        suggestionsBox.appendChild(div);
                    }
                });

                if (suggestionsBox.children.length > 0) {
                    suggestionsBox.style.display = "block";
                } else {
                    suggestionsBox.style.display = "none";
                }
            })
            .catch(error => console.error("Error:", error));
    }

    document.getElementById("search-box").addEventListener("input", fetchSuggestions);

    document.addEventListener("click", function (event) {
        if (!event.target.closest(".search-container")) {
            document.getElementById('suggestions').style.display = "none";
        }
    });
});

function checkPasswordStrength() {
    var password = document.getElementById("password").value;
    var strengthText = document.getElementById("password-strength");

    if (password.length < 6) {
        strengthText.innerHTML = "<span style='color: red;'>Weak</span>";
    } else if (password.match(/[A-Za-z]/) && password.match(/[0-9]/)) {
        strengthText.innerHTML = "<span style='color: orange;'>Medium</span>";
    } else if (password.match(/[A-Za-z]/) && password.match(/[0-9]/) && password.match(/[\W]/)) {
        strengthText.innerHTML = "<span style='color: green;'>Strong</span>";
    } else {
        strengthText.innerHTML = "";
    }
}

