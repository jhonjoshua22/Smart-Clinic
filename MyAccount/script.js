
    function localTime() {
        const d = new Date();
        document.getElementById("time").innerHTML = hours(d.getHours()) + ":" + minutes(d.getMinutes()) + " " + timetype(d.getHours());
    }

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
