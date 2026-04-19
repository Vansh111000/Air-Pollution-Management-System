function random(min,max){
return Math.floor(Math.random()*(max-min+1))+min;
}

function updateSensors(){

let cards = document.querySelectorAll(".stat-value");

cards.forEach(card=>{

card.innerText = random(20,200);

});

}

setInterval(updateSensors,4000);