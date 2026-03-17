function toggleTheme(){

let body = document.body;

body.classList.toggle("light");

if(body.classList.contains("light")){
localStorage.setItem("theme","light");
}else{
localStorage.setItem("theme","dark");
}

}

window.onload = function(){

let theme = localStorage.getItem("theme");

if(theme==="light"){
document.body.classList.add("light");
}

}