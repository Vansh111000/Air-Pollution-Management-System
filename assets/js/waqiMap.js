/**
 * waqiMap.js — WAQI Real-Time AQI Tile Layer Integration
 * VayuDarpan Air Pollution Management System
 *
 * HOW TO USE:
 *   1. Get your free token from https://aqicn.org/data-platform/token/
 *   2. Replace YOUR_WAQI_TOKEN below with your actual token
 *   3. This file is included in public_dashboard.php
 *
 * What this does:
 *   - Adds a real-time WAQI AQI colored tile overlay on top of the Leaflet map
 *   - Adds a toggle button to show/hide the WAQI overlay
 *   - Fetches live AQI data for any city on map click via WAQI API
 *   - Auto-refreshes the tile layer every 10 minutes
 */

// ─────────────────────────────────────────────
// 1.  CONFIGURATION — paste your token here
// ─────────────────────────────────────────────
const WAQI_TOKEN = 'a4876cc4b95bebfeecefd06cf7c60889dd3be28d'; // ← replace with token from aqicn.org/data-platform/token/

// Refresh interval in milliseconds (10 minutes)
const WAQI_REFRESH_INTERVAL = 10 * 60 * 1000;

// ─────────────────────────────────────────────
// 2.  WAQI TILE LAYER
//     Tiles URL format:
//     https://tiles.waqi.info/tiles/usepa-aqi/{z}/{x}/{y}.png?token=TOKEN
// ─────────────────────────────────────────────
let waqiTileLayer = null;
let waqiLayerVisible = true;

function createWaqiTileLayer() {
  return L.tileLayer(
    `https://tiles.waqi.info/tiles/usepa-aqi/{z}/{x}/{y}.png?token=${WAQI_TOKEN}`,
    {
      attribution:
        'Air Quality tiles: <a href="https://waqi.info/" target="_blank">WAQI</a>',
      opacity: 0.75,
      maxZoom: 16,
      // Cache-bust on refresh by adding a timestamp param
      // (Leaflet appends it automatically when we recreate the layer)
    }
  );
}

/**
 * Initialise the WAQI tile layer on an existing Leaflet map instance.
 * Call this AFTER your map has been created.
 * @param {L.Map} map  — the Leaflet map object
 */
function initWaqiTileLayer(map) {
  if (WAQI_TOKEN === 'YOUR_WAQI_TOKEN') {
    console.warn(
      '[WAQI] Token not set. Open assets/js/waqiMap.js and replace YOUR_WAQI_TOKEN.'
    );
    showWaqiTokenBanner();
    return;
  }

  // Add tile layer
  waqiTileLayer = createWaqiTileLayer();
  waqiTileLayer.addTo(map);

  // Add toggle control
  addWaqiToggleControl(map);

  // Add AQI legend
  addWaqiLegend(map);

  // Auto-refresh every 10 minutes
  setInterval(() => refreshWaqiTiles(map), WAQI_REFRESH_INTERVAL);

  // Click-to-lookup: when user clicks anywhere on the map, fetch live AQI
  map.on('click', function (e) {
    fetchWaqiByLatLng(e.latlng.lat, e.latlng.lng);
  });

  console.log('[WAQI] Real-time AQI tile layer loaded.');
}

/**
 * Remove old tile layer and add a fresh one (forces reload of tiles).
 */
function refreshWaqiTiles(map) {
  if (!waqiTileLayer || !waqiLayerVisible) return;
  map.removeLayer(waqiTileLayer);
  waqiTileLayer = createWaqiTileLayer();
  waqiTileLayer.addTo(map);
  console.log('[WAQI] Tiles refreshed at', new Date().toLocaleTimeString());
}

// ─────────────────────────────────────────────
// 3.  LIVE AQI LOOKUP BY LAT/LNG
//     Uses: https://api.waqi.info/feed/geo:{lat};{lng}/?token=TOKEN
// ─────────────────────────────────────────────
async function fetchWaqiByLatLng(lat, lng) {
  if (WAQI_TOKEN === 'YOUR_WAQI_TOKEN') return;

  try {
    const url = `https://api.waqi.info/feed/geo:${lat};${lng}/?token=${WAQI_TOKEN}`;
    const res = await fetch(url);
    const json = await res.json();

    if (json.status !== 'ok') {
      console.warn('[WAQI] No data for this location.');
      return;
    }

    const d = json.data;
    const aqi = d.aqi;
    const stationName = d.city?.name || 'Unknown Station';
    const time = d.time?.s || '';

    // Build popup content from WAQI response
    const iaqi = d.iaqi || {};
    const pm25 = iaqi.pm25?.v ?? '—';
    const pm10 = iaqi.pm10?.v ?? '—';
    const no2  = iaqi.no2?.v  ?? '—';
    const so2  = iaqi.so2?.v  ?? '—';
    const co   = iaqi.co?.v   ?? '—';
    const temp = iaqi.t?.v    ?? '—';
    const wind = iaqi.w?.v    ?? '—';

    const color = pinColor(aqi);
    const label = aqiLabel(aqi);

    // Show a Leaflet popup at the clicked point
    L.popup({ maxWidth: 260 })
      .setLatLng([lat, lng])
      .setContent(`
        <div class="custom-popup">
          <h4>📡 ${stationName}</h4>
          <div class="popup-aqi" style="color:${color}">${aqi}</div>
          <div style="font-size:0.75rem;color:#666;margin-bottom:6px;">${label} · ${time}</div>
          <table>
            <tr><td>🔬 PM2.5</td><td><b>${pm25} µg/m³</b></td></tr>
            <tr><td>💨 PM10</td><td><b>${pm10} µg/m³</b></td></tr>
            <tr><td>🏭 NO₂</td><td><b>${no2} µg/m³</b></td></tr>
            <tr><td>⚗️ SO₂</td><td><b>${so2} µg/m³</b></td></tr>
            <tr><td>🚗 CO</td><td><b>${co}</b></td></tr>
            <tr><td>🌡 Temp</td><td><b>${temp}°C</b></td></tr>
            <tr><td>💨 Wind</td><td><b>${wind} m/s</b></td></tr>
          </table>
          <div style="font-size:0.65rem;color:#aaa;margin-top:6px;">Source: WAQI · Live</div>
        </div>
      `)
      .openOn(window._vayuMap);

    // Also update the city side panel if AQI is available
    updateCityPanelFromWaqi({ stationName, aqi, pm25, pm10, no2, so2, co, temp, wind });

  } catch (err) {
    console.error('[WAQI] Fetch error:', err);
  }
}

/**
 * Fetch live AQI data for a known city name via WAQI search API.
 * Useful for pre-loading data for your hardcoded cities array.
 */
async function fetchWaqiByCity(cityName) {
  if (WAQI_TOKEN === 'YOUR_WAQI_TOKEN') return null;
  try {
    const url = `https://api.waqi.info/feed/${encodeURIComponent(cityName)}/?token=${WAQI_TOKEN}`;
    const res = await fetch(url);
    const json = await res.json();
    if (json.status === 'ok') return json.data;
    return null;
  } catch {
    return null;
  }
}

// ─────────────────────────────────────────────
// 4.  UPDATE CITY SIDE PANEL FROM WAQI DATA
// ─────────────────────────────────────────────
function updateCityPanelFromWaqi(d) {
  const aqi = typeof d.aqi === 'number' ? d.aqi : parseInt(d.aqi) || 0;
  const cls = aqiClass(aqi);
  const needleLeft = Math.min(Math.round((aqi / 500) * 100), 97);

  const nameEl  = document.getElementById('cityName');
  const stateEl = document.getElementById('cityState');
  const bodyEl  = document.getElementById('cityCardBody');

  if (!nameEl || !bodyEl) return;

  nameEl.textContent  = '📡 ' + (d.stationName || 'Live Station');
  stateEl.textContent = 'Real-time WAQI data';

  bodyEl.innerHTML = `
    <div class="aqi-display">
      <div class="aqi-circle ${cls}">
        <span class="num">${aqi}</span>
        <span class="lbl">AQI</span>
      </div>
      <div class="aqi-meta">
        <h4>${aqiLabel(aqi)}</h4>
        <p>PM2.5: ${d.pm25} µg/m³<br>PM10: ${d.pm10} µg/m³</p>
      </div>
    </div>
    <div class="stat-row">
      <div class="stat-box"><div class="s-val">${d.temp}°C</div><div class="s-lbl">Temperature</div></div>
      <div class="stat-box"><div class="s-val">${d.wind}</div><div class="s-lbl">Wind m/s</div></div>
      <div class="stat-box"><div class="s-val">${d.no2}</div><div class="s-lbl">NO₂ µg/m³</div></div>
      <div class="stat-box"><div class="s-val">${d.so2}</div><div class="s-lbl">SO₂ µg/m³</div></div>
    </div>
    <div class="aqi-bar-wrap">
      <label>AQI SCALE (LIVE)</label>
      <div class="aqi-bar-bg">
        <div class="aqi-needle" style="left:${needleLeft}%"></div>
      </div>
    </div>
    <div style="font-size:0.68rem;color:#00bcd4;margin-top:8px;text-align:center;letter-spacing:0.5px;">
      ⚡ Live data from WAQI
    </div>
  `;

  // Update alerts panel with live AQI
  if (typeof updateAlerts === 'function') {
    updateAlerts({
      aqi,
      pm25: d.pm25,
      pm10: d.pm10,
      no2: d.no2,
      so2: d.so2,
      co: parseFloat(d.co) || 0,
      temp: d.temp,
      wind: d.wind,
    });
  }
}

// ─────────────────────────────────────────────
// 5.  LEAFLET CUSTOM CONTROLS
// ─────────────────────────────────────────────

/** Toggle button to show/hide the WAQI heatmap overlay */
function addWaqiToggleControl(map) {
  const WaqiToggle = L.Control.extend({
    options: { position: 'topright' },
    onAdd: function () {
      const btn = L.DomUtil.create('button', 'waqi-toggle-btn');
      btn.innerHTML = '🌍 AQI Layer: ON';
      btn.title = 'Toggle WAQI real-time AQI tile overlay';
      btn.style.cssText = `
        background:#0d2137;color:#00bcd4;border:2px solid #00bcd4;
        padding:8px 14px;border-radius:8px;cursor:pointer;
        font-family:'Rajdhani',sans-serif;font-weight:700;font-size:0.82rem;
        letter-spacing:1px;box-shadow:0 2px 10px rgba(0,0,0,0.25);
        transition:background 0.2s;
      `;
      L.DomEvent.on(btn, 'click', function (e) {
        L.DomEvent.stopPropagation(e);
        if (waqiLayerVisible) {
          map.removeLayer(waqiTileLayer);
          btn.innerHTML = '🌍 AQI Layer: OFF';
          btn.style.color = '#aaa';
          btn.style.borderColor = '#aaa';
        } else {
          waqiTileLayer.addTo(map);
          btn.innerHTML = '🌍 AQI Layer: ON';
          btn.style.color = '#00bcd4';
          btn.style.borderColor = '#00bcd4';
        }
        waqiLayerVisible = !waqiLayerVisible;
      });
      return btn;
    },
  });
  new WaqiToggle().addTo(map);
}

/** AQI colour legend in the bottom-left */
function addWaqiLegend(map) {
  const Legend = L.Control.extend({
    options: { position: 'bottomleft' },
    onAdd: function () {
      const div = L.DomUtil.create('div', 'waqi-legend');
      div.style.cssText = `
        background:rgba(13,33,55,0.88);color:#fff;
        padding:10px 14px;border-radius:10px;
        font-family:'Rajdhani',sans-serif;font-size:0.78rem;
        font-weight:600;letter-spacing:0.5px;
        box-shadow:0 4px 18px rgba(0,0,0,0.3);
        border:1px solid rgba(0,188,212,0.25);
        pointer-events:none;
      `;
      div.innerHTML = `
        <div style="margin-bottom:6px;color:#00bcd4;font-size:0.8rem;letter-spacing:1px;">AQI SCALE</div>
        ${[
          ['#00c853','0–50','Good'],
          ['#ffc107','51–100','Moderate'],
          ['#ff6d00','101–150','Unhealthy (Sensitive)'],
          ['#e53935','151–200','Unhealthy'],
          ['#9c27b0','201–300','Very Unhealthy'],
          ['#7b1414','301+','Hazardous'],
        ].map(([c, r, l]) => `
          <div style="display:flex;align-items:center;gap:7px;margin-bottom:4px;">
            <div style="width:14px;height:14px;border-radius:3px;background:${c};flex-shrink:0;"></div>
            <span style="color:#cde8f5;">${r}</span>
            <span style="color:#7a9ab8;font-size:0.7rem;">${l}</span>
          </div>`).join('')}
        <div style="margin-top:6px;color:#5a7a9a;font-size:0.65rem;">
          Source: WAQI · tiles.waqi.info
        </div>
      `;
      return div;
    },
  });
  new Legend().addTo(map);
}

// ─────────────────────────────────────────────
// 6.  TOKEN-NOT-SET BANNER
// ─────────────────────────────────────────────
function showWaqiTokenBanner() {
  const banner = document.createElement('div');
  banner.style.cssText = `
    position:fixed;top:64px;left:50%;transform:translateX(-50%);
    background:#ff6d00;color:#fff;padding:12px 24px;border-radius:8px;
    font-family:'Rajdhani',sans-serif;font-weight:700;font-size:0.9rem;
    z-index:9999;box-shadow:0 4px 20px rgba(0,0,0,0.25);
    letter-spacing:0.5px;text-align:center;max-width:520px;
  `;
  banner.innerHTML = `
    ⚠️ WAQI token not set. Open <code style="background:rgba(0,0,0,0.2);padding:2px 6px;border-radius:4px;">assets/js/waqiMap.js</code>
    and replace <b>YOUR_WAQI_TOKEN</b>.
    Get a free token at <a href="https://aqicn.org/data-platform/token/" target="_blank" style="color:#fff;text-decoration:underline;">aqicn.org</a>
    <span style="cursor:pointer;margin-left:12px;opacity:0.75;" onclick="this.parentElement.remove()">✖</span>
  `;
  document.body.appendChild(banner);
}
