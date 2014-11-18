
function getZipCodes(state, city){
	var client = new XMLHttpRequest();
	client.open("GET", "http://api.zippopotam.us/us/" + state + "/" + city, true);
	client.onreadystatechange = function() {
		if(client.readyState == 4) {
			alert(client.responseText);
		};
	};
	
	return client.send();
}
