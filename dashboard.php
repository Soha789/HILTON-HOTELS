<?php /* dashboard.php - Manage (internal CSS + JS only) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Hilton Demo • Dashboard</title>
<style>
  :root{--bg:#0d0f16;--card:#141827;--muted:#96a0bb;--text:#eef1fa;--brand:#4f8cff;--brand-2:#7b5cff;--radius:18px;--shadow:0 10px 30px rgba(0,0,0,.25)}
  body{margin:0;background:radial-gradient(1200px 600px at 110% -10%,#1b2140,transparent 60%),var(--bg);color:var(--text);font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif}
  .container{max-width:1200px;margin:auto;padding:24px}
  .nav{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
  .btn{height:42px;border-radius:12px;border:1px solid transparent;padding:0 14px;cursor:pointer;background:linear-gradient(135deg,var(--brand),var(--brand-2));color:white;font-weight:600}
  .btn.ghost{background:transparent;border:1px solid #2a314a;color:var(--text)}
  .tabs{display:flex;gap:8px;margin:8px 0 14px}
  .tab{padding:10px 14px;border:1px solid #2a314a;border-radius:999px;color:var(--muted);cursor:pointer}
  .tab.active{color:white;border-color:transparent;background:linear-gradient(135deg,var(--brand),var(--brand-2))}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
  .card{border:1px solid #2a314a;background:linear-gradient(180deg,#151b2e,#111629);border-radius:18px;box-shadow:var(--shadow)}
  .pad{padding:16px}
  .muted{color:var(--muted)}
  .table{width:100%;border-collapse:collapse}
  .table th,.table td{border-bottom:1px solid #2a314a;padding:10px;text-align:left}
  input,select{height:44px;border-radius:12px;border:1px solid #2a314a;background:#0f1322;color:var(--text);padding:0 12px;outline:0}
  .row{display:grid;gap:10px}
  .flex{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
  .chip{padding:6px 10px;border:1px dashed #2a314a;border-radius:999px}
  @media (max-width:980px){.grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="container">
  <div class="nav">
    <button class="btn ghost" onclick="go('/index.php')">← Back to search</button>
    <div class="flex">
      <button class="btn ghost" onclick="exportCSV()">Export bookings CSV</button>
      <button class="btn danger" style="background:transparent;border:1px solid #2a314a" onclick="clearAll()">Clear all data</button>
    </div>
  </div>

  <div class="tabs">
    <div class="tab active" data-tab="hotels">Our Hotels</div>
    <div class="tab" data-tab="bookings">Bookings</div>
  </div>

  <section id="hotels" class="grid">
    <article class="card pad">
      <h3>Existing Hotels</h3>
      <p class="muted">Seeded + anything you add below. These appear in search on the homepage.</p>
      <table class="table" id="hotelsTable">
        <thead><tr><th>ID</th><th>Name</th><th>City</th><th>Price</th><th>Rating</th><th>Amenities</th><th></th></tr></thead>
        <tbody></tbody>
      </table>
    </article>

    <article class="card pad">
      <h3>Add a New Hotel</h3>
      <div class="row">
        <div class="flex">
          <input id="hName" placeholder="Hotel name" style="flex:1 1 260px">
          <input id="hCity" placeholder="City" style="width:160px">
        </div>
        <div class="flex">
          <input id="hPrice" type="number" min="100" placeholder="Price per night (SAR)" style="width:220px">
          <input id="hRating" type="number" min="1" max="5" step="0.1" placeholder="Rating 1–5" style="width:160px">
        </div>
        <div class="flex">
          <label class="chip"><input type="checkbox" class="am" value="wifi" checked> Wi-Fi</label>
          <label class="chip"><input type="checkbox" class="am" value="pool"> Pool</label>
          <label class="chip"><input type="checkbox" class="am" value="spa"> Spa</label>
          <label class="chip"><input type="checkbox" class="am" value="gym"> Gym</label>
          <label class="chip"><input type="checkbox" class="am" value="breakfast"> Breakfast</label>
        </div>
        <button class="btn" onclick="addHotel()">Add Hotel</button>
      </div>
    </article>
  </section>

  <section id="bookings" class="grid" style="display:none">
    <article class="card pad" style="grid-column:1/-1">
      <h3>All Bookings</h3>
      <p class="muted">These come from bookings made on <b>bookings.php</b> and are stored in your browser.</p>
      <table class="table" id="bookingsTable">
        <thead>
          <tr><th>Ref</th><th>Hotel</th><th>City</th><th>Dates</th><th>Rooms</th><th>Guests</th><th>Total (SAR)</th><th>Name</th><th>Email</th><th></th></tr>
        </thead>
        <tbody></tbody>
      </table>
    </article>
  </section>
</div>

<script>
  const $ = s=>document.querySelector(s); const $$ = s=>Array.from(document.querySelectorAll(s));
  function go(p){ window.location.href = p; }

  const seedHotels = [
    {id:"HIL-001", name:"Hilton Sky Tower", city:"Riyadh", price:620, rating:4.7, amenities:["wifi","pool","gym","breakfast","spa"]},
    {id:"HIL-002", name:"Hilton Corniche Suites", city:"Jeddah", price:540, rating:4.5, amenities:["wifi","pool","gym","breakfast"]},
    {id:"HIL-003", name:"Hilton Downtown Oasis", city:"Dubai", price:780, rating:4.8, amenities:["wifi","pool","spa","breakfast","gym"]},
    {id:"HIL-004", name:"Hilton Marina Bay", city:"Doha", price:510, rating:4.3, amenities:["wifi","gym"]},
    {id:"HIL-005", name:"Hilton Desert Pearl", city:"Abu Dhabi", price:590, rating:4.6, amenities:["wifi","pool","breakfast"]},
    {id:"HIL-006", name:"Hilton Creekside", city:"Sharjah", price:360, rating:4.0, amenities:["wifi","breakfast"]},
  ];
  function getMyHotels(){ return JSON.parse(localStorage.getItem('my_hotels')||'[]'); }
  function saveMyHotels(list){ localStorage.setItem('my_hotels', JSON.stringify(list)); }
  function allHotels(){ return [...seedHotels, ...getMyHotels()]; }

  function drawHotels(){
    const tbody = $('#hotelsTable tbody');
    tbody.innerHTML = allHotels().map(h=>`
      <tr>
        <td>${h.id}</td><td>${h.name}</td><td>${h.city}</td>
        <td>SAR ${h.price}</td><td>★ ${h.rating.toFixed(1)}</td>
        <td>${h.amenities.join(', ')}</td>
        <td>${seedHotels.find(s=>s.id===h.id)? '' : `<button class="btn ghost" onclick="delHotel('${h.id}')">Delete</button>`}</td>
      </tr>`).join('');
  }

  function addHotel(){
    const name=$('#hName').value.trim(), city=$('#hCity').value.trim();
    const price=parseFloat($('#hPrice').value||'0'), rating=parseFloat($('#hRating').value||'0');
    const ams=$$('.am:checked').map(x=>x.value);
    if(!name||!city||price<=0||rating<=0) return alert('Please fill all hotel fields correctly.');
    const id='CUS-'+Math.random().toString(36).slice(2,8).toUpperCase();
    const list=getMyHotels(); list.push({id,name,city,price,rating,amenities:ams});
    saveMyHotels(list);
    $('#hName').value=''; $('#hCity').value=''; $('#hPrice').value=''; $('#hRating').value='';
    $$('.am').forEach((x,i)=>x.checked = i===0); // Wi-Fi default
    drawHotels();
    alert('Hotel added! It now appears on the homepage.');
  }
  function delHotel(id){
    const list=getMyHotels().filter(h=>h.id!==id); saveMyHotels(list); drawHotels();
  }

  function bookings(){ return JSON.parse(localStorage.getItem('bookings')||'[]'); }
  function saveBookings(b){ localStorage.setItem('bookings', JSON.stringify(b)); }
  function drawBookings(){
    const tbody = $('#bookingsTable tbody');
    const rows = bookings().map(b=>`
      <tr>
        <td>${b.ref}</td>
        <td>${b.hotelName}</td>
        <td>${b.city}</td>
        <td>${b.ci} → ${b.co}</td>
        <td>${b.rooms}</td>
        <td>${b.guests}</td>
        <td>${b.total}</td>
        <td>${b.name}</td>
        <td>${b.email}</td>
        <td><button class="btn ghost" onclick="delBooking('${b.ref}')">Delete</button></td>
      </tr>`).join('');
    tbody.innerHTML = rows || `<tr><td colspan="10" class="muted">No bookings yet.</td></tr>`;
  }
  function delBooking(ref){
    const list = bookings().filter(b=>b.ref!==ref); saveBookings(list); drawBookings();
  }

  function exportCSV(){
    const cols = ['ref','hotelName','city','ci','co','rooms','guests','total','name','email'];
    const data = bookings();
    if(!data.length) return alert('No bookings to export.');
    const lines = [cols.join(',')].concat(data.map(b=>cols.map(k=>`"${String(b[k]??'').replaceAll('"','""')}"`).join(',')));
    const blob = new Blob([lines.join('\n')], {type:'text/csv'});
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'bookings.csv'; a.click();
  }

  function clearAll(){
    if(confirm('This will remove custom hotels and bookings on this browser. Continue?')){
      localStorage.removeItem('my_hotels'); localStorage.removeItem('bookings'); drawHotels(); drawBookings();
    }
  }

  // Tabs
  $$('.tab').forEach(t=>t.addEventListener('click', ()=>{
    $$('.tab').forEach(x=>x.classL
