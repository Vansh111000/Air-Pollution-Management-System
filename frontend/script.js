// Task 1: Welcome Message Using DOM
document.getElementById('welcome-message').innerHTML = "Air Pollution Management System";

// Task 2: Data Type & Sensor Configuration
let stationUID = "Eco-ST-405";
let aqiReading = 168;
let isCalibrated = true;

document.getElementById('sensor-info').innerHTML =
    `<div style="margin-bottom:5px;"><strong>UID:</strong> ${stationUID}</div>
     <div style="margin-bottom:5px;"><strong>Live Index:</strong> ${aqiReading}</div>
     <div><strong>Status:</strong> <span style="color: green;">${isCalibrated ? "Verified" : "Check"}</span></div>`;

// Task 3: Operators (Emission Mass Calculation)
let sampleWeight = 3.5;
let totalMassLoad = aqiReading * sampleWeight;
document.getElementById('pollution-calculation').innerHTML =
    `${totalMassLoad.toFixed(1)} <small style="font-size:0.4em; color:gray">mg/m²</small>`;

// Task 4: Control Flow (Risk Protocol Logic)
const alertDiv = document.getElementById('risk-info');
if (totalMassLoad > 500) {
    let adjustment = totalMassLoad * 0.10; // 10% safety buffer
    alertDiv.innerHTML = `<strong>DANGER:</strong> Alert Issued. Buffer: ${adjustment.toFixed(0)}`;
    alertDiv.classList.add('risk-high');
} else {
    alertDiv.innerHTML = "<strong>OPTIMAL:</strong> Quality within safe limits.";
    alertDiv.classList.add('risk-low');
}

// Task 5: Switch Statement (Zonal Protocol)
let zoneSelection = "industrial";
let activeProtocol = "";
switch (zoneSelection) {
    case "industrial":
        activeProtocol = "Zone: Industrial (Protocol 4A)";
        break;
    case "residential":
        activeProtocol = "Zone: Residential";
        break;
    default:
        activeProtocol = "Zone: General";
}
document.getElementById('zone-display').innerText = activeProtocol;

// Task 6: Arrays & Loops (Pollutant Inventory)
const elements = ["Nitrogen Dioxide", "Sulfur Dioxide", "Carbon Monoxide", "PM 2.5", "PM 10", "Ground-level Ozone"];
const listContainer = document.getElementById('pollutant-list');

function renderElements(query = "") {
    listContainer.innerHTML = "";
    elements.forEach(el => {
        if (el.toLowerCase().includes(query.toLowerCase())) {
            let li = document.createElement('li');
            li.className = "pollutant-card";
            li.textContent = el;

            // Task 13: Mouse Events (Hover Effect)
            li.onmouseover = () => {
                li.style.transform = "scale(1.05)";
                li.style.borderColor = "#006064";
            };
            li.onmouseout = () => {
                li.style.transform = "scale(1)";
                li.style.borderColor = "#ddd";
            };
            listContainer.appendChild(li);
        }
    });
}
renderElements();

// Task 7 & 8: Array Operations (Push & Length for Logs)
let incidentLogs = [];

function updateLogView() {
    const logList = document.getElementById('data-log-list');
    logList.innerHTML = "";
    incidentLogs.forEach(item => {
        let li = document.createElement('li');
        li.style.borderBottom = "1px solid #eee";
        li.style.padding = "5px 0";
        li.textContent = item;
        logList.appendChild(li);
    });
    // Task 8: Use array length
    document.getElementById('log-count-display').innerText = `Total Records: ${incidentLogs.length}`;
}

// Task 11: Click Event to add Log
document.getElementById('add-log-btn').addEventListener('click', function () {
    let time = new Date().toLocaleTimeString();
    incidentLogs.push(`${time} - Critical Peak`);
    updateLogView();

    let msg = document.getElementById('log-message');
    msg.innerText = "Recorded!";
    setTimeout(() => msg.innerText = "", 2000);
});

// Task 9: Search/Loop Logic (Console Check)
function checkHazardCapability() {
    for (let i = 0; i < elements.length; i++) {
        if (elements[i].includes("PM 2.5")) return "System Capable of Hazardous Detection";
    }
    return "Detection module missing";
}
console.log(checkHazardCapability());

// Task 10: Sum Loop (Analytics Audit)
function calculateMonthlyAudit() {
    let weeklyReadings = [120, 145, 110, 160];
    let sumTotal = 0;
    for (let i = 0; i < weeklyReadings.length; i++) {
        sumTotal += weeklyReadings[i];
    }
    let avg = (sumTotal / weeklyReadings.length).toFixed(1);

    document.getElementById('total-readings-display').innerHTML =
        `<div style="font-size:1.5em; font-weight:bold;">${avg} <small>Avg</small></div>
        <small>Total Load: ${sumTotal}</small>`;
}
calculateMonthlyAudit();

// Task 12: Keyboard Input Events
document.getElementById('search-bar').addEventListener('input', (e) => {
    renderElements(e.target.value);
});

// Task 13: Theme Toggle
function toggleTheme() {
    document.body.classList.toggle('dark-mode');
}