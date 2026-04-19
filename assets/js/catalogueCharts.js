// PIE CHART

const pie = document.getElementById('cataloguePie');

if(pie){

new Chart(pie,{
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


// BAR CHART

const bar = document.getElementById('catalogueBar');

if(bar){

new Chart(bar,{
type:'bar',
data:{
labels:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
datasets:[{
label:'Pollution Index',
data:[110,125,140,150,170,160,145]
}]
},
options:{
responsive:true
}
});

}