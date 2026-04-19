const ctx = document.getElementById('aqiChart');

if(ctx){

new Chart(ctx, {
type: 'line',
data: {
labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
datasets: [{
label: 'AQI',
data: [120,140,135,160,170,150,142],
borderWidth: 2
}]
},
options: {
responsive:true
}
});

}