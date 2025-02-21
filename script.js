function localTime () {
	const d = new Date();
	document.getElementById("time").innerHTML = hours(d.getHours()) + ":" + minutes(d.getMinutes()) + " " + timetype(d.getHours());
}

setInterval(localTime, 0);

function hours(value) {
	if (value < 10) { return "0" + value;}
	if (value > 12 && value < 22) {return "0" + (value - 12);}
	if (value >= 22) {return value - 12;}
	else return value;
}

function minutes(value) {
	if (value < 10) { return "0" + value;}
	else return value;
}
function timetype(value) {
	if (value < 12) { return " AM";}
	else {return "PM";}
}
