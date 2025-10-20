<?php /* bookings.php - Book & confirm (internal CSS + JS only) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Hilton Demo • Book Your Stay</title>
<style>
  :root{--bg:#0d0f16;--card:#141827;--muted:#96a0bb;--text:#eef1fa;--brand:#4f8cff;--brand-2:#7b5cff;
        --ring:rgba(79,140,255,.4);--radius:18px;--shadow:0 10px 30px rgba(0,0,0,.25)}
  body{margin:0;background:radial-gradient(1200px 600px at -10% -20%,#1b2140,transparent 60%),var(--bg);
       color:var(--text);font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif}
  .container{max-width:1100px;margin:auto;padding:24px}
  .nav{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
  .btn{height:42px;border-radius:12px;border:1px solid transparent;padding:0 14px;cursor:pointer;
       background:linear-gradient(135deg,var(--brand),var(--brand-2));color:white;font-weight:600}
  .btn.ghost{background:transparent;border:1px solid #2a314a;color:var(--text)}
  .wrap{display:grid;grid-template-columns:1.2fr .9fr;gap:18px}
  .card{border:1px solid #2a314a;background:linear-gradient(180deg,#151b2e,#111629);border-radius:18px;box-shadow:var(--shadow)}
  .hero{aspect-ratio:16/9;border-bottom:1px solid #2a314a;background:
        conic-gradient(from 45deg at 70% 20%, #2a3153, #1d2340, #22294b, #2a3153)}
  .pad{padding:16px}
  h2{margin:0 0 8px}
  .muted{color:var(--muted)}
  .grid2{display:grid;grid-template-columns:1fr 1fr;gap:10px}
  input,select{height:44px;border-radius:12px;border:1px solid #2a314a;background:#0f1322;color:var(--text);padding:0 12px;outline:0}
  .row{display:grid;gap:10px}
  .priceBox{display:flex;justify-content:space-between;align-items:center;margin-top:8px;padding:12px;border:1px dashed #2a314a;border-radius:12px}
  .ticket{margin-top:14px;padding:14px;border:1px solid #29406d;background:linear-gradient(180deg,#122040,#0e1730);border-radius:16px}
  .ok{color:#22c55e}
  @media (max-width:980px){.wrap{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="container">
  <div class="nav">
    <button class="btn ghost" onclick="go('/index.php')">← Back to search</button>
    <button class="btn" onclick="go('/dashboard.php')">Dashboard</button>
  </div>

  <div class="wrap">
    <article class="card">
      <div class="hero"></div>
      <div class="pad">
        <h2 id="hName">Loading…</h2>
        <div class="muted" id="hMeta"></div>
        <div style="margin-top:10px" id="hAmen" class="muted"></div>
      </div>
    </article>

    <aside class="card pad">
      <h3>Book this room</h3>
      <div class="row">
        <div class="grid2">
          <div><label class="muted">Check-in</label><input id="ci" type="date"></div>
          <div><label class="muted">Check-out</label><input id="co" type="date"></div>
        </div>
        <div class="grid2">
          <div><label class="muted">Guests</label><input id="guests" type="number" min="1" value="2"></div>
          <div><label class="muted">Rooms</label><input id="rooms" type="number" min="1" value="1"></div>
        </div>
        <div><label class="muted">Full name</label><input id="name" placeholder="Your name"></div>
        <div><label class="muted">Email</label><input id="email" type="email" placeholder="you@example.com"></div>
        <div class="grid2">
          <div><label class="muted">Promo code</label><input id="promo" placeholder="(optional)"></div>
          <div class="priceBox"><span>Price / night</span><b id="night">—</b></div>
        </div>
        <div class="priceBox"><span>Nights × Rooms</span><b id="calc">—</b></div>
        <div class="priceBox"><span>Total (incl. 10% tax)</span><b id="total">—</b></div>
        <button class="btn" onclick="confirmBooking()">Confirm reservation</button>
        <div id="ticket" class="ticket" style="display:none"></div>
      </div>
    </aside>
  </div>
</div>

<script>
  // Seed same as index + any user hotels from localStorage
  const seedHotels = [
    {id:"HIL-001", name:"Hilton Sky Tower", city:"Riyadh", price:620, rating:4.7, amenities:["wifi","pool","gym","breakfast","spa"]},
    {id:"HIL-002", name:"Hilton Corniche Suites", city:"Jeddah", price:540, rating:4.5, amenities:["wifi","pool","gym","breakfast"]},
    {id:"HIL-003", name:"Hilton Downtown Oasis", city:"Dubai", price:780, rating:4.8, amenities:["wifi","pool","spa","breakfast","gym"]},
    {id:"HIL-004", name:"Hilton Marina Bay", city:"Doha", price:510, rating:4.3, amenities:["wifi","gym"]},
    {id:"HIL-005", name:"Hilton Desert Pearl", city:"Abu Dhabi", price:590, rating:4.6, amenities:["wifi","pool","breakfast"]},
    {id:"HIL-006", name:"Hilton Creekside", city:"Sharjah", price:360, rating:4.0, amenities:["wifi","breakfast"]},
  ];
  function loadHotels(){ return [...seedHotels, ...JSON.parse(localStorage.getItem('my_hotels')||'[]')]; }
  const $ = s=>document.querySelector(s);
  function go(p){ window.location.href = p; }

  // Query params
  const params = new URLSearchParams(location.search);
  const hid = params.get('id')||'';
  const ciQ = params.get('ci')||''; const coQ = params.get('co')||''; const gQ = params.get('guests')||'2';

  const hotel = loadHotels().find(h=>h.id===hid) || seedHotels[0];
  const amenText = ['wifi','pool','spa','gym','breakfast'].map(a=>`<span style="opacity:${hotel.amenities.includes(a)?1:.3}">${a}</span>`).join(' • ');

  $('#hName').textContent = hotel.name;
  $('#hMeta').textContent = `${hotel.city} • ★ ${hotel.rating.toFixed(1)} • SAR ${hotel.price}/night`;
  $('#hAmen').innerHTML = amenText;
  $('#night').textContent = `SAR ${hotel.price}`;

  // Date defaults
  const today = new Date(); const t2 = new Date(Date.now()+86400000);
  $('#ci').value = ciQ || new Date(today.getTime()-today.getTimezoneOffset()*60000).toISOString().split('T')[0];
  $('#co').value = coQ || new Date(t2.getTime()-t2.getTimezoneOffset()*60000).toISOString().split('T')[0];
  $('#guests').value = gQ;

  function nights(ci,co){
    const A=new Date(ci), B=new Date(co);
    const n=Math.round((B-A)/86400000);
    return Math.max(0,n);
  }
  function recalc(){
    const n = nights($('#ci').value,$('#co').value);
    const rooms = Math.max(1, parseInt($('#rooms').value||'1',10));
    const base = hotel.price * Math.max(0,n) * rooms;
    const disc = ($('#promo').value||'').toUpperCase()==='WELCOME10' ? base*0.1 : 0;
    const taxed = (base-disc)*1.10;
    $('#calc').textContent = `${n||0} × ${rooms} = SAR ${ (hotel.price*(n||0)*rooms).toFixed(0) }`;
    $('#total').textContent = `SAR ${ taxed.toFixed(0) }${disc? ' (−10% promo)': ''}`;
    return {n, rooms, total: Math.round(taxed), discount: Math.round(disc)};
  }
  ['ci','co','rooms','promo'].forEach(id=>$('#'+id).addEventListener('input', recalc));
  recalc();

  function confirmBooking(){
    const ci=$('#ci').value, co=$('#co').value, guests=$('#guests').value;
    const name=$('#name').value.trim(), email=$('#email').value.trim();
    const {n,rooms,total,discount} = recalc();
    if(!name||!email) return alert('Please enter your name and email.');
    if(n<=0) return alert('Check-out must be after check-in.');
    const ref = 'HR'+Math.random().toString(36).slice(2,8).toUpperCase();
    const booking = {ref, hotelId:hotel.id, hotelName:hotel.name, city:hotel.city, price:hotel.price, rating:hotel.rating,
                     ci, co, nights:n, rooms, guests, name, email, total, discount, createdAt: Date.now()};
    const list = JSON.parse(localStorage.getItem('bookings')||'[]'); list.push(booking);
    localStorage.setItem('bookings', JSON.stringify(list));
    $('#ticket').style.display='block';
    $('#ticket').innerHTML = `
      <div><b class="ok">Reservation confirmed!</b></div>
      <div class="muted">Reference</div><div><b>${ref}</b></div>
      <div style="margin-top:8px">${hotel.name} — ${hotel.city}</div>
      <div class="muted">${ci} → ${co} • ${rooms} room(s) • ${guests} guest(s)</div>
      <div style="margin-top:8px"><b>Total paid: SAR ${total}</b></div>
      <div class="muted">A summary has been saved to your device (Dashboard → Bookings).</div>
      <div style="margin-top:10px;display:flex;gap:8px">
        <button class="btn" onclick="go('/dashboard.php')">Go to Dashboard</button>
        <button class="btn ghost" onclick="print()">Print</button>
      </div>`;
    window.scrollTo({top:0,behavior:'smooth'});
  }
</script>
</body>
</html>
