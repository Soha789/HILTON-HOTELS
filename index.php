<?php /* index.php - Hilton-like listings (internal CSS + JS only) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Hilton Demo • Find Your Stay</title>
<style>
  :root{
    --bg:#0e0f14;--card:#141724;--muted:#8f98b2;--text:#e9ecf5;--brand:#4f8cff;--brand-2:#7b5cff;
    --ring:rgba(79,140,255,.4);--ok:#22c55e;--warn:#f59e0b;--danger:#ef4444;
    --radius:18px;--shadow:0 10px 30px rgba(0,0,0,.25);
  }
  *{box-sizing:border-box} body{margin:0;font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;background:
    radial-gradient(1200px 600px at 10% -10%,#1d2140 0%, transparent 60%),
    radial-gradient(1000px 500px at 110% 10%,#1d2438 0%, transparent 60%), var(--bg); color:var(--text)}
  a{color:inherit;text-decoration:none}
  .container{max-width:1200px;margin:auto;padding:24px}
  .nav{display:flex;gap:16px;align-items:center;justify-content:space-between;margin-bottom:18px}
  .brand{display:flex;align-items:center;gap:12px;font-weight:700;font-size:20px}
  .logo{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--brand),var(--brand-2));
        box-shadow:inset 0 0 30px rgba(255,255,255,.15)}
  .pill{padding:8px 12px;background:#1b2032;border:1px solid #2a314a;border-radius:999px;color:var(--muted)}
  .hdr{padding:18px;border:1px solid #2a314a;background:linear-gradient(180deg,#141a2b,#121527);
       border-radius:var(--radius);box-shadow:var(--shadow);position:sticky;top:0;z-index:5}
  .search{display:grid;grid-template-columns:1.1fr repeat(2, .9fr) .7fr .5fr;gap:12px}
  .field{display:flex;flex-direction:column;gap:6px}
  label{font-size:12px;color:var(--muted)}
  input,select{height:44px;border-radius:12px;border:1px solid #2a314a;background:#0f1322;color:var(--text);
               padding:0 12px;outline:0}
  .btn{height:44px;border-radius:12px;border:1px solid transparent;padding:0 14px;cursor:pointer;
       background:linear-gradient(135deg,var(--brand),var(--brand-2));color:white;font-weight:600}
  .btn.ghost{background:transparent;border-color:#2a314a;color:var(--text)}
  .filters{display:flex;flex-wrap:wrap;gap:10px;margin-top:12px}
  .chip{display:flex;gap:8px;align-items:center;padding:8px 12px;border:1px dashed #2a314a;border-radius:999px;color:var(--muted)}
  .main{margin-top:18px;display:grid;grid-template-columns:260px 1fr;gap:18px}
  .side{position:sticky;top:108px;align-self:start;border:1px solid #2a314a;background:#111629;border-radius:16px;padding:14px}
  .side h4{margin:4px 0 10px 0}
  .range{display:flex;gap:8px}
  .checks{display:grid;gap:8px;margin-top:8px}
  .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
  .card{border:1px solid #2a314a;background:linear-gradient(180deg,#151a2d,#121527);border-radius:18px;overflow:hidden;box-shadow:var(--shadow)}
  .img{aspect-ratio:16/10;background:conic-gradient(from 25deg at 70% 20%, #2a3153, #1d2340, #22294b, #2a3153)}
  .body{padding:14px}
  .title{display:flex;justify-content:space-between;gap:10px}
  .title h3{margin:0;font-size:18px}
  .rating{font-weight:700;color:#ffd166}
  .amen{color:var(--muted);font-size:13px;margin:6px 0 10px}
  .price{display:flex;justify-content:space-between;align-items:center;margin-top:6px}
  .price b{font-size:18px}
  .toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
  .sort{display:flex;gap:8px;align-items:center}
  .empty{padding:28px;text-align:center;color:var(--muted);border:1px dashed #2a314a;border-radius:16px}
  @media (max-width:1024px){.grid{grid-template-columns:repeat(2,1fr)}.main{grid-template-columns:1fr}}
  @media (max-width:640px){.search{grid-template-columns:1fr 1fr;grid-auto-rows:auto}.grid{grid-template-columns:1fr}}
</style>
</head>
<body>
  <div class="container">
    <div class="nav">
      <div class="brand"><div class="logo"></div>Hilton-Style Stays</div>
      <div class="pill">Front-end demo • Local data</div>
    </div>

    <div class="hdr">
      <div class="search">
        <div class="field"><label>Destination</label><input id="q" placeholder="City, area or hotel name"></div>
        <div class="field"><label>Check-in</label><input id="ci" type="date"></div>
        <div class="field"><label>Check-out</label><input id="co" type="date"></div>
        <div class="field"><label>Guests</label><input id="guests" type="number" min="1" value="2"></div>
        <button class="btn" id="btnSearch">Search</button>
      </div>
      <div class="filters">
        <span class="chip">Price: <input id="minP" type="number" min="0" placeholder="Min" style="width:90px">–<input id="maxP" type="number" min="0" placeholder="Max" style="width:90px"></span>
        <span class="chip">Rating:
          <select id="minR"><option value="0">Any</option><option>5</option><option>4.5</option><option>4</option><option>3.5</option></select>
        </span>
        <span class="chip"><input type="checkbox" class="am" value="pool" id="am1"><label for="am1">Pool</label></span>
        <span class="chip"><input type="checkbox" class="am" value="spa" id="am2"><label for="am2">Spa</label></span>
        <span class="chip"><input type="checkbox" class="am" value="gym" id="am3"><label for="am3">Gym</label></span>
        <span class="chip"><input type="checkbox" class="am" value="wifi" id="am4"><label for="am4">Free Wi-Fi</label></span>
        <span class="chip"><input type="checkbox" class="am" value="breakfast" id="am5"><label for="am5">Breakfast</label></span>
      </div>
    </div>

    <div class="main">
      <aside class="side">
        <h4>Quick Actions</h4>
        <div class="checks">
          <button class="btn" onclick="go('/dashboard.php')">Open Dashboard</button>
          <button class="btn ghost" onclick="resetFilters()">Reset Filters</button>
        </div>
      </aside>

      <section>
        <div class="toolbar">
          <div class="pill" id="countPill">Showing 0 stays</div>
          <div class="sort">
            <label>Sort</label>
            <select id="sortBy">
              <option value="relevance">Relevance</option>
              <option value="priceAsc">Price: Low to High</option>
              <option value="priceDesc">Price: High to Low</option>
              <option value="ratingDesc">Best Rated</option>
            </select>
          </div>
        </div>
        <div id="grid" class="grid"></div>
        <div id="empty" class="empty" style="display:none">No results. Try widening your filters.</div>
      </section>
    </div>
  </div>

<script>
  // ---------- Mini “DB” (seed) ----------
  const seedHotels = [
    {id:"HIL-001", name:"Hilton Sky Tower", city:"Riyadh", price:620, rating:4.7, amenities:["wifi","pool","gym","breakfast","spa"]},
    {id:"HIL-002", name:"Hilton Corniche Suites", city:"Jeddah", price:540, rating:4.5, amenities:["wifi","pool","gym","breakfast"]},
    {id:"HIL-003", name:"Hilton Downtown Oasis", city:"Dubai", price:780, rating:4.8, amenities:["wifi","pool","spa","breakfast","gym"]},
    {id:"HIL-004", name:"Hilton Marina Bay", city:"Doha", price:510, rating:4.3, amenities:["wifi","gym"]},
    {id:"HIL-005", name:"Hilton Desert Pearl", city:"Abu Dhabi", price:590, rating:4.6, amenities:["wifi","pool","breakfast"]},
    {id:"HIL-006", name:"Hilton Creekside", city:"Sharjah", price:360, rating:4.0, amenities:["wifi","breakfast"]},
  ];

  // Load custom hotels from dashboard (localStorage)
  function loadHotels(){
    const extra = JSON.parse(localStorage.getItem('my_hotels')||'[]');
    return [...seedHotels, ...extra];
  }

  // ---------- Helpers ----------
  const $ = sel => document.querySelector(sel);
  const $$ = sel => Array.from(document.querySelectorAll(sel));
  function go(path){ window.location.href = path; } // JS redirection only

  // ---------- Search/Filter/Sort ----------
  function getFilters(){
    return {
      q: $('#q').value.trim().toLowerCase(),
      minP: parseFloat($('#minP').value)||0,
      maxP: parseFloat($('#maxP').value)||Infinity,
      minR: parseFloat($('#minR').value)||0,
      ams: $$('.am:checked').map(x=>x.value),
      sort: $('#sortBy').value,
      ci: $('#ci').value, co: $('#co').value, guests: $('#guests').value||2
    }
  }

  function matches(h,f){
    const text = (h.name+' '+h.city).toLowerCase();
    if(f.q && !text.includes(f.q)) return false;
    if(!(h.price>=f.minP && h.price<=f.maxP)) return false;
    if(h.rating < f.minR) return false;
    if(f.ams.length && !f.ams.every(a=>h.amenities.includes(a))) return false;
    return true;
  }

  function sortHotels(list, sort){
    const arr=[...list];
    if(sort==='priceAsc') arr.sort((a,b)=>a.price-b.price);
    if(sort==='priceDesc') arr.sort((a,b)=>b.price-a.price);
    if(sort==='ratingDesc') arr.sort((a,b)=>b.rating-a.rating);
    return arr;
  }

  function renderCard(h,f){
    const am = ['wifi','pool','spa','gym','breakfast'].map(a=>
      `<span style="opacity:${h.amenities.includes(a)?1:.3}">${a}</span>`).join(' • ');
    return `
      <article class="card">
        <div class="img" role="img" aria-label="${h.name} scenic image"></div>
        <div class="body">
          <div class="title">
            <h3>${h.name}</h3>
            <div class="rating">★ ${h.rating.toFixed(1)}</div>
          </div>
          <div class="amen">${h.city} • ${am}</div>
          <div class="price">
            <b>SAR ${h.price}/night</b>
            <div>
              <button class="btn" onclick="viewHotel('${h.id}')">View</button>
            </div>
          </div>
        </div>
      </article>`;
  }

  function viewHotel(id){
    const f = getFilters();
    // pass dates & guests via query for convenience
    const q = new URLSearchParams({id, ci:f.ci||'', co:f.co||'', guests:String(f.guests||2)});
    window.location.href = '/hilton/bookings.php?'+q.toString();
  }

  function apply(){
    const f = getFilters();
    const hotels = loadHotels().filter(h=>matches(h,f));
    const sorted = sortHotels(hotels, f.sort);
    $('#grid').innerHTML = sorted.map(h=>renderCard(h,f)).join('');
    $('#countPill').textContent = `Showing ${sorted.length} ${sorted.length===1?'stay':'stays'}`;
    $('#empty').style.display = sorted.length? 'none':'block';
  }

  function resetFilters(){
    $('#q').value=''; $('#minP').value=''; $('#maxP').value=''; $('#minR').value='0';
    $$('.am').forEach(x=>x.checked=false);
    apply();
  }

  // ---------- Events ----------
  $('#btnSearch').addEventListener('click', apply);
  $('#sortBy').addEventListener('change', apply);
  $$('.am').forEach(x=>x.addEventListener('change', apply));
  ['q','minP','maxP','minR'].forEach(id=>$('#'+id).addEventListener('input', apply));

  // Default dates (today/tomorrow)
  const today = new Date(); const t2 = new Date(Date.now()+86400000);
  $('#ci').valueAsDate = today; $('#co').valueAsDate = t2;

  // Initial draw
  apply();
</script>
</body>
</html>
