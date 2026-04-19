// AQI TREND

const ctx1 = document.getElementById('stationAqiChart');

if(ctx1){

new Chart(ctx1,{
type:'line',
data:{
labels:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
datasets:[{
label:'AQI',
data:[110,135,120,150,170,160,142],
borderWidth:2
}]
},
options:{
responsive:true
}
});

}


// POLLUTION PIE

const ctx2 = document.getElementById('pollutionPie');

if(ctx2){

new Chart(ctx2,{
type:'pie',
data:{
labels:['PM2.5','PM10','CO','NO2'],
datasets:[{
data:[40,25,20,15]
}]
},
options:{
responsive:true
}
});

}