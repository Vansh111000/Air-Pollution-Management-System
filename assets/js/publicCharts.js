// AQI TREND CHART

const ctxAqi = document.getElementById('publicAqiChart');

if(ctxAqi){

new Chart(ctxAqi,{
type:'line',
data:{
labels:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
datasets:[{
label:'AQI',
data:[110,120,135,150,170,160,142],
borderWidth:2
}]
},
options:{
responsive:true
}
});

}


// POLLUTION PIE CHART

const ctxPie = document.getElementById('publicPollutionPie');

if(ctxPie){

new Chart(ctxPie,{
type:'pie',
data:{
labels:['PM2.5','PM10','CO','NO2'],
datasets:[{
data:[40,30,20,10]
}]
},
options:{
responsive:true
}
});

}