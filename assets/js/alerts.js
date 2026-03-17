function checkAQI(){

let aqi = Math.floor(Math.random()*300);

if(aqi > 200){

showAlert("Red Alert! AQI Dangerous: " + aqi,"red");

}

else if(aqi > 120){

showAlert("Warning! AQI High: " + aqi,"yellow");

}

}

function showAlert(message,type){

let alertBox = document.createElement("div");

alertBox.className = "alert-popup " + type;

alertBox.innerText = message;

document.body.appendChild(alertBox);

setTimeout(()=>{

alertBox.remove();

},5000);

}

setInterval(checkAQI,7000);