<?php
// مالي — صفحة المال والسلعة، تحفظ في جدول mali_docs داخل قاعدة بيانات cod-studio.
// ===== الإعدادات: ضع نفس بيانات قاعدة cod-studio =====
$DB_HOST = 'localhost';
$DB_NAME = 'cod_studio';   // ← غيّرها إلى اسم قاعدة بيانات مشروعك
$DB_USER = 'root';
$DB_PASS = '';

if (isset($_GET['api'])) {
  header('Content-Type: application/json; charset=utf-8');
  try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec("CREATE TABLE IF NOT EXISTS mali_docs (
      col VARCHAR(20) NOT NULL,
      id VARCHAR(64) NOT NULL,
      data LONGTEXT NOT NULL,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (col, id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $in = json_decode(file_get_contents('php://input'), true) ?: [];
    $op = $in['op'] ?? '';
    if ($op === 'load') {
      $out = [];
      foreach ($pdo->query("SELECT col, id, data FROM mali_docs") as $r) $out[$r['col']][$r['id']] = json_decode($r['data']);
      echo json_encode((object)$out, JSON_UNESCAPED_UNICODE);
      exit;
    }
    $col = $in['col'] ?? ''; $id = $in['id'] ?? '';
    if (!in_array($col, ['state', 'items', 'camps', 'log'], true) || !preg_match('/^[A-Za-z0-9_-]{1,64}$/', $id)) {
      http_response_code(400); echo '{"error":"bad request"}'; exit;
    }
    if ($op === 'set') {
      $st = $pdo->prepare("REPLACE INTO mali_docs (col, id, data) VALUES (?, ?, ?)");
      $st->execute([$col, $id, json_encode($in['data'] ?? new stdClass(), JSON_UNESCAPED_UNICODE)]);
    } elseif ($op === 'delete') {
      $pdo->prepare("DELETE FROM mali_docs WHERE col = ? AND id = ?")->execute([$col, $id]);
    } else { http_response_code(400); echo '{"error":"bad op"}'; exit; }
    echo '{"ok":true}';
  } catch (Throwable $e) {
    http_response_code(500); echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
  }
  exit;
}
?><!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<style>:root{padding-top:env(safe-area-inset-top,0px);padding-bottom:env(safe-area-inset-bottom,0px)}body{margin:0;font:14px system-ui}img{max-width:100%}[hidden]{display:none!important}</style>
</head>
<body>
<title>مالي</title>
<meta name="claude:viewport-fit-sides" content="cover">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap">
<style>
/* Layout: one calm column. Money cards, goods with a one-line breakdown, active campaigns, recent events.
   One fixed button opens a step-by-step wizard that holds every money, goods, return and campaign path. */
:root {
  --bg: #f5f6f4;
  --surface: #ffffff;
  --fg: #17201c;
  --muted: #64716b;
  --line: #dfe4e1;
  --accent: #0f6b5c;
  --accent-ink: #ffffff;
  --usdt: #2f6f8f;
  --good: #1b7a45; --good-bg: #e2f3e9;
  --bad: #b3261e; --bad-bg: #fbe7e5;
  --warn: #8a5a00; --warn-bg: #fbf0d9;
  --font-body: "IBM Plex Sans Arabic", "Segoe UI", Tahoma, system-ui, sans-serif;
  --font-num: "IBM Plex Mono", ui-monospace, Menlo, monospace;
}
@media (prefers-color-scheme: dark) {
  :root:not([data-theme="light"]) {
    --bg: #101513; --surface: #182019; --fg: #e6ece9; --muted: #98a6a0; --line: #29332f;
    --accent: #5cc4ae; --accent-ink: #101513; --usdt: #79b8d6;
    --good: #5fcf8f; --good-bg: #15301f; --bad: #ff8a80; --bad-bg: #3a1a18; --warn: #f0c060; --warn-bg: #352a12;
    color-scheme: dark;
  }
}
:root[data-theme="dark"] {
  --bg: #101513; --surface: #182019; --fg: #e6ece9; --muted: #98a6a0; --line: #29332f;
  --accent: #5cc4ae; --accent-ink: #101513; --usdt: #79b8d6;
  --good: #5fcf8f; --good-bg: #15301f; --bad: #ff8a80; --bad-bg: #3a1a18; --warn: #f0c060; --warn-bg: #352a12;
  color-scheme: dark;
}
* { box-sizing: border-box; }
[hidden] { display: none !important; }
body {
  background: var(--bg); color: var(--fg); font-family: var(--font-body); font-size: 15px; line-height: 1.55;
  padding-left: max(16px, env(safe-area-inset-left, 0px));
  padding-right: max(16px, env(safe-area-inset-right, 0px));
}
main { max-width: 560px; margin: 0 auto; padding-block: 20px calc(110px + env(safe-area-inset-bottom, 0px)); display: grid; gap: 16px; }
h1 { font-size: 1.3rem; margin: 0; }
h2 { font-size: 0.95rem; margin: 0; color: var(--muted); font-weight: 600; }
p { margin: 0; }
.status { color: var(--muted); font-size: 0.78rem; }
.card { background: var(--surface); border: 1px solid var(--line); border-radius: 14px; padding: 18px; display: grid; gap: 10px; min-width: 0; }
.big { font-family: var(--font-num); font-size: 2.2rem; font-weight: 500; line-height: 1.15; direction: ltr; unicode-bidi: isolate; text-align: right; }
.usdt .big { color: var(--usdt); }
.rows { display: grid; gap: 4px; }
.r { display: flex; justify-content: space-between; gap: 12px; font-size: 0.9rem; color: var(--muted); }
.r .v { font-family: var(--font-num); direction: ltr; unicode-bidi: isolate; color: var(--fg); white-space: nowrap; }
.r.sep { border-top: 1px dashed var(--line); padding-top: 6px; margin-top: 2px; }
.goods { display: grid; }
.g { display: flex; align-items: center; gap: 10px; padding: 9px 0; border-bottom: 1px solid var(--line); }
.g:last-child { border-bottom: none; }
.g .n { flex: 1; min-width: 0; display: grid; }
.g .sub { font-size: 0.75rem; color: var(--muted); }
.g .alert { font-size: 0.75rem; color: var(--warn); }
.g .c { font-family: var(--font-num); font-size: 1.15rem; direction: ltr; unicode-bidi: isolate; }
.g.zero .n, .g.zero .c { color: var(--muted); }
.pill { display: inline-block; font-size: 0.72rem; font-weight: 600; padding: 2px 9px; border-radius: 99px; white-space: nowrap; }
.good { background: var(--good-bg); color: var(--good); } .bad { background: var(--bad-bg); color: var(--bad); }
.warn { background: var(--warn-bg); color: var(--warn); } .none { background: var(--bg); color: var(--muted); }
button {
  font: inherit; font-weight: 600; font-size: 0.92rem; border-radius: 10px; padding: 10px 14px; cursor: pointer;
  border: 1px solid var(--accent); background: var(--accent); color: var(--accent-ink);
}
button.soft { background: transparent; color: var(--muted); border-color: var(--line); font-size: 0.78rem; padding: 4px 10px; font-weight: 500; }
button.link { background: transparent; border: none; color: var(--accent); padding: 4px 6px; font-size: 0.85rem; }
button:focus-visible, input:focus-visible, summary:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
details > summary { cursor: pointer; color: var(--muted); font-size: 0.85rem; list-style: none; padding-top: 4px; }
details > summary::-webkit-details-marker { display: none; }
.log { display: grid; gap: 6px; font-size: 0.86rem; }
.log .e { display: flex; justify-content: space-between; gap: 10px; align-items: center; }
.log .e span { min-width: 0; }
.log .t { color: var(--muted); font-size: 0.75rem; }
.fab {
  position: fixed; inset: auto 0 0 0; z-index: 20; display: flex; justify-content: center;
  padding: 12px max(16px, env(safe-area-inset-right, 0px)) calc(14px + env(safe-area-inset-bottom, 0px)) max(16px, env(safe-area-inset-left, 0px));
  background: linear-gradient(to top, var(--bg) 70%, transparent);
}
.fab button { width: 100%; max-width: 560px; font-size: 1.1rem; padding: 15px; border-radius: 14px; }
.sheet-bg { position: fixed; inset: 0; z-index: 30; background: rgba(0,0,0,.45); display: flex; align-items: flex-end; justify-content: center; }
.sheet {
  background: var(--surface); width: 100%; max-width: 560px; max-height: 90%; overflow-y: auto; border-radius: 18px 18px 0 0;
  padding: 18px max(16px, env(safe-area-inset-right, 0px)) calc(20px + env(safe-area-inset-bottom, 0px)) max(16px, env(safe-area-inset-left, 0px));
  display: grid; gap: 14px;
}
.sheet h3 { margin: 0; font-size: 1.2rem; text-wrap: balance; }
.sheet .hint { color: var(--muted); font-size: 0.85rem; }
.opts { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.opts button { background: var(--bg); color: var(--fg); border-color: var(--line); padding: 14px 8px; font-size: 0.95rem; }
.opts button.full { grid-column: 1 / -1; }
.sheet input { font: inherit; font-size: 1.4rem; color: var(--fg); background: var(--bg); border: 1px solid var(--line); border-radius: 10px; padding: 12px; width: 100%; }
.sheet input[type="number"] { font-family: var(--font-num); direction: ltr; text-align: right; }
.chips { display: flex; flex-wrap: wrap; gap: 6px; }
.chips button { background: var(--bg); color: var(--fg); border-color: var(--line); padding: 6px 12px; font-size: 0.85rem; font-weight: 500; }
.top { display: flex; justify-content: space-between; align-items: center; }
.done { text-align: center; display: grid; gap: 10px; padding-block: 10px; }
@media (prefers-reduced-motion: no-preference) { button { transition: opacity .15s; } button:hover { opacity: .88; } }
</style>

<main dir="rtl" lang="ar">
  <header>
    <h1>مالي</h1>
    <p class="status" id="status">جارٍ التحميل…</p>
  </header>

  <section class="card" aria-labelledby="h-da">
    <h2 id="h-da">💵 مالك بالدينار</h2>
    <div class="big" id="daTotal">–</div>
    <div class="rows" id="daRows"></div>
  </section>

  <section class="card usdt" aria-labelledby="h-usdt">
    <h2 id="h-usdt">💲 USDT</h2>
    <div class="big" id="usdtTotal">–</div>
    <div class="rows" id="usdtRows"></div>
  </section>

  <section class="card" aria-labelledby="h-goods">
    <h2 id="h-goods">📦 سلعتك</h2>
    <div class="goods" id="goods"></div>
    <details id="boxWrap" hidden>
      <summary id="boxSum"></summary>
      <div class="goods" id="box"></div>
    </details>
  </section>

  <section class="card" aria-labelledby="h-camps" id="campCard">
    <h2 id="h-camps">📣 حملاتك</h2>
    <div class="goods" id="camps"></div>
    <div style="display:flex;gap:8px;flex-wrap:wrap"><button type="button" id="newCampBtn">+ حملة جديدة</button><button type="button" class="soft" id="adsBtn" style="font-size:0.9rem;padding:10px 14px">صرفت إعلانات</button></div>
  </section>

  <section class="card" aria-labelledby="h-log">
    <h2 id="h-log">آخر ما حدث</h2>
    <div class="log" id="log"></div>
  </section>
</main>

<div class="fab" dir="rtl" lang="ar"><button type="button" id="go">ماذا حدث؟</button></div>
<div id="sheetRoot"></div>

<script>
(function () {
  "use strict";
  const $ = (id) => document.getElementById(id);
  const el = (tag, cls, text) => { const e = document.createElement(tag); if (cls) e.className = cls; if (text !== undefined) e.textContent = text; return e; };
  const uid = () => Date.now().toString(36) + Math.random().toString(36).slice(2, 7);
  const num = (v) => { const n = parseFloat(String(v ?? "").replace(",", ".")); return isFinite(n) ? n : 0; };
  const clone = (o) => JSON.parse(JSON.stringify(o));
  const today = () => new Date().toLocaleDateString("en-CA");
  const days = (d) => Math.max(0, Math.round((new Date(today() + "T12:00:00") - new Date(d + "T12:00:00")) / 864e5));
  const da = (n) => Math.round(n).toLocaleString("en-US") + " دج";
  const ut = (n) => (Math.round(n * 100) / 100).toLocaleString("en-US") + " USDT";
  const usd = (n) => "$" + (Math.round(n * 100) / 100).toLocaleString("en-US");
  const ACC = { cash: "كاش", stash: "مخبأ البيت", ccp: "CCP", dhd: "عند dhd", bybit: "Bybit" };
  const DA_ACC = ["cash", "stash", "ccp"];
  const LOCN = { home: "البيت", wh: "المخزن", transit: "في التوصيل" };
  const CTYPE = { stock: "بيع من المخزن", home: "بيع منتج البيت", test: "تست", partner: "شراكة" };
  const RETURN_FEE = 50, BE_FACTOR = 1.5;

  let ST = { cash: 0, stash: 0, ccp: 0, dhd: 0, bybit: 0, rate: 257, debts: [] };
  let ITEMS = [], CAMPS = [], LOG = [];

  // ---------- item helpers ----------
  // An item keeps pieces per place: home, wh (delivery warehouse), transit (out for delivery), stuck (refused, still with dhd).
  function norm(it) {
    if (it.home === undefined && it.wh === undefined && it.transit === undefined) { it.home = num(it.qty); }
    it.home = num(it.home); it.wh = num(it.wh); it.transit = num(it.transit); it.stuck = Array.isArray(it.stuck) ? it.stuck : [];
    return it;
  }
  const stuckN = (it) => it.stuck.reduce((t, s) => t + num(s.n), 0);
  const total = (it) => it.home + it.wh + it.transit + stuckN(it);
  // Take n pieces that were out for delivery: from transit first, then the warehouse, then home.
  function takeOut(it, n, prefer) {
    const order = prefer ? ["transit", prefer, prefer === "wh" ? "home" : "wh"] : ["transit", "wh", "home"];
    let left = n;
    order.forEach((k) => { const t = Math.min(left, Math.max(0, it[k])); it[k] -= t; left -= t; });
    if (left > 0) it[order[order.length - 1]] -= left;
  }
  function takeStuck(it, n) { let left = n; while (left > 0 && it.stuck.length) { const k = Math.min(left, num(it.stuck[0].n)); it.stuck[0].n -= k; left -= k; if (!it.stuck[0].n) it.stuck.shift(); } }
  const item = (id) => ITEMS.find((i) => i.id === id);

  // ---------- campaign math ----------
  // Break-even ad cost per order in USD = (sale price - buy price) / 1.5 / dollar rate.
  function cstats(c) {
    const it = item(c.item); const partner = c.type === "partner";
    const price = partner ? num(c.sell) : num(it && it.price), cost = partner ? num(c.buy) : num(it && it.cost);
    const rate = num(ST.rate) || 257, margin = price - cost;
    const s = { margin, rate, be: margin > 0 ? margin / BE_FACTOR / rate : null, cpo: num(c.orders) > 0 ? num(c.adUsd) / num(c.orders) : null };
    if (!num(c.adUsd)) s.v = ["none", "لا أرقام بعد"];
    else if (margin <= 0) s.v = ["bad", "لا هامش"];
    else if (s.cpo === null) s.v = ["warn", "صرفت ولا طلبيات"];
    else if (s.cpo <= s.be) s.v = ["good", "تربح"];
    else if (s.cpo <= s.be * BE_FACTOR) s.v = ["warn", "تسترجع رأس المال فقط"];
    else s.v = ["bad", "تخسر"];
    if (partner) {
      const adDa = num(c.adUsd) * rate; s.total = num(c.delivered) * margin - adDa;
      s.mine = (num(c.share) || 50) / 100 * s.total; s.owes = adDa + s.mine - num(c.settled);
    }
    return s;
  }

  // ---------- storage: MySQL through this same PHP file (?api=1) ----------
  const setStatus = (t) => { $("status").textContent = t; };
  async function api(body) {
    const r = await fetch("?api=1", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(body) });
    if (!r.ok) throw new Error("http " + r.status);
    return r.json();
  }
  async function write(body) { try { await api(body); setStatus("محفوظ في قاعدة البيانات."); } catch (e) { setStatus("تعذّر الحفظ. هل MySQL يعمل في XAMPP؟"); } }
  const saveState = () => write({ op: "set", col: "state", id: "main", data: clone(ST) });
  const saveDoc = (col, o) => { const d = clone(o); delete d.id; return write({ op: "set", col, id: o.id, data: d }); };
  const delDoc = (col, id) => write({ op: "delete", col, id });

  async function init() {
    try {
      const all = await api({ op: "load" });
      const rows = (col) => Object.entries(all[col] || {}).map(([id, d]) => Object.assign({ id }, d));
      ST = Object.assign({ cash: 0, stash: 0, ccp: 0, dhd: 0, bybit: 0, rate: 257, debts: [] }, (all.state && all.state.main) || {});
      ITEMS = rows("items").map(norm);
      CAMPS = rows("camps");
      LOG = rows("log").sort((x, y) => (y.ts || "").localeCompare(x.ts || "")).slice(0, 8);
      setStatus("محفوظ في قاعدة بيانات cod-studio.");
    } catch (e) { setStatus("لا يمكن الوصول إلى قاعدة البيانات. شغّل MySQL في XAMPP ثم أعد فتح الصفحة."); }
    render();
  }

  // ---------- apply with undo ----------
  // Runs a change, saves the state and every touched item or campaign, and logs what was there before.
  function apply(text, fn) {
    const before = { state: clone(ST), docs: [] }; const touched = new Map();
    const touch = (col, o) => {
      const key = col + "/" + o.id;
      if (!touched.has(key)) { touched.set(key, [col, o]); const list = col === "items" ? ITEMS : CAMPS; const old = list.find((x) => x.id === o.id); before.docs.push(old ? { col, d: clone(old) } : { col, d: { id: o.id }, isNew: true }); }
      return o;
    };
    fn(touch);
    touched.forEach(([col, o]) => { const list = col === "items" ? ITEMS : CAMPS; if (!list.includes(o)) list.push(o); saveDoc(col, o); });
    const l = { id: uid(), ts: new Date().toISOString(), text, before };
    LOG = [l].concat(LOG).slice(0, 8);
    saveState(); saveDoc("log", l); render();
  }
  function undo(l) {
    ST = clone(l.before.state); saveState();
    // Older log lines kept only items under before.items.
    const docs = l.before.docs || (l.before.items || []).map((d) => ({ col: "items", d, isNew: !!d.__new }));
    docs.forEach(({ col, d, isNew }) => {
      const key = col === "items" ? "ITEMS" : "CAMPS"; const list = col === "items" ? ITEMS : CAMPS;
      if (isNew) { const f = list.filter((x) => x.id !== d.id); if (key === "ITEMS") ITEMS = f; else CAMPS = f; delDoc(col, d.id); }
      else { const i = list.findIndex((x) => x.id === d.id); const v = col === "items" ? norm(d) : d; if (i >= 0) list[i] = v; else list.push(v); saveDoc(col, v); }
    });
    LOG = LOG.filter((x) => x.id !== l.id); delDoc("log", l.id); render();
  }

  // ---------- render ----------
  function row(box, label, value, cls) { const r = el("div", "r" + (cls ? " " + cls : "")); r.append(el("span", "", label), el("span", "v", value)); box.append(r); }
  function render() {
    $("daTotal").textContent = da(num(ST.cash) + num(ST.stash) + num(ST.ccp) + num(ST.dhd));
    const dr = $("daRows"); dr.replaceChildren();
    row(dr, "كاش", da(num(ST.cash)));
    if (num(ST.stash)) row(dr, "مخبأ البيت", da(num(ST.stash)));
    if (num(ST.ccp)) row(dr, "CCP", da(num(ST.ccp)));
    row(dr, "عند dhd", da(num(ST.dhd)));
    const debts = (ST.debts || []).filter((d) => Math.abs(num(d.amount)) > 0.004);
    let first = true; const sepRow = (box, l, v) => { row(box, l, v, first ? "sep" : ""); first = false; };
    debts.filter((d) => d.cur === "DA" && d.amount > 0).forEach((d) => sepRow(dr, "لك عند " + d.name, da(d.amount)));
    debts.filter((d) => d.cur === "DA" && d.amount < 0).forEach((d) => sepRow(dr, "عليك لـ " + d.name, da(-d.amount)));
    CAMPS.filter((c) => c.type === "partner" && c.status !== "closed").forEach((c) => { const s = cstats(c); if (Math.abs(s.owes) > 0.5) sepRow(dr, (s.owes > 0 ? "لك عند " : "عليك لـ ") + (c.partner || "الشريك") + " (" + c.name + ")", da(Math.abs(s.owes))); });

    $("usdtTotal").textContent = ut(num(ST.bybit));
    const ur = $("usdtRows"); ur.replaceChildren();
    row(ur, "في Bybit", ut(num(ST.bybit)));
    first = true;
    debts.filter((d) => d.cur === "USDT" && d.amount > 0).forEach((d) => sepRow(ur, "عند " + d.name, ut(d.amount)));
    debts.filter((d) => d.cur === "USDT" && d.amount < 0).forEach((d) => sepRow(ur, "عليك لـ " + d.name, ut(-d.amount)));

    const vis = ITEMS.filter((i) => !i.hidden).sort((a, b) => total(b) - total(a));
    const hid = ITEMS.filter((i) => i.hidden);
    const g = $("goods"); g.replaceChildren();
    if (!vis.length) g.append(el("p", "status", hid.length ? "كل منتجاتك في الصندوق." : "لا توجد سلعة بعد."));
    vis.forEach((it) => g.append(goodRow(it, "إخفاء", () => { it.hidden = true; saveDoc("items", it); render(); })));
    $("boxWrap").hidden = !hid.length;
    $("boxSum").textContent = "الصندوق: " + hid.length + " منتج مخفي";
    const bx = $("box"); bx.replaceChildren();
    hid.forEach((it) => bx.append(goodRow(it, "أرجِعه", () => { it.hidden = false; saveDoc("items", it); render(); })));

    const act = CAMPS.filter((c) => c.status !== "closed");
    const cb = $("camps"); cb.replaceChildren();
    $("adsBtn").hidden = !act.length;
    if (!act.length) cb.append(el("p", "status", "لا توجد حملة بعد. أنشئ حملتك الأولى: من المخزن، من البيت، تست، أو شراكة."));
    act.forEach((c) => {
      const s = cstats(c); const r = el("div", "g"); const n = el("span", "n");
      n.append(el("span", "", c.name), el("span", "sub", CTYPE[c.type] + " · طلبية " + (s.cpo !== null ? usd(s.cpo) : "–") + " / تعادل " + (s.be !== null ? usd(s.be) : "–")));
      r.append(n, el("span", "pill " + s.v[0], s.v[1])); cb.append(r);
    });

    const lg = $("log"); lg.replaceChildren();
    if (!LOG.length) lg.append(el("p", "status", "لا شيء بعد. اضغط \"ماذا حدث؟\" في الأسفل."));
    LOG.forEach((l, i) => {
      const e = el("div", "e"); const s = el("span"); s.append(el("span", "", l.text + " "), el("span", "t", (l.ts || "").slice(0, 10)));
      e.append(s);
      if (i === 0) { const u = el("button", "link", "تراجع"); u.type = "button"; u.onclick = () => undo(l); e.append(u); }
      lg.append(e);
    });
  }
  function goodRow(it, label, fn) {
    const t = total(it); const r = el("div", "g" + (t <= 0 ? " zero" : ""));
    const n = el("span", "n"); n.append(el("span", "", it.name));
    const parts = [["البيت", it.home], ["المخزن", it.wh], ["في التوصيل", it.transit], ["مرتجع", stuckN(it)]].filter(([, v]) => v).map(([k, v]) => k + " " + Math.round(v));
    if (parts.length > 1 || (parts.length === 1 && !parts[0].startsWith("البيت"))) n.append(el("span", "sub", parts.join(" · ")));
    if (it.stuck.length) { const d = days(it.stuck[0].d); if (d >= 7) n.append(el("span", "alert", "⚠ مرتجع عالق عند dhd منذ " + d + " يوم")); }
    r.append(n, el("span", "c", String(Math.round(t))));
    const b = el("button", "soft", label); b.type = "button"; b.setAttribute("aria-label", label + " " + it.name); b.onclick = fn; r.append(b);
    return r;
  }

  // ---------- wizard ----------
  const W = { stack: [] };
  function sheet(build) {
    const root = $("sheetRoot"); root.replaceChildren();
    const bg = el("div", "sheet-bg"); const sh = el("div", "sheet"); sh.dir = "rtl"; sh.lang = "ar";
    sh.setAttribute("role", "dialog"); sh.setAttribute("aria-modal", "true");
    const top = el("div", "top");
    const back = el("button", "link", "→ رجوع"); back.type = "button"; back.hidden = W.stack.length <= 1;
    back.onclick = () => { W.stack.pop(); W.stack[W.stack.length - 1](); };
    const x = el("button", "link", "إغلاق"); x.type = "button"; x.onclick = close;
    top.append(back, x); sh.append(top);
    build(sh);
    bg.onclick = (e) => { if (e.target === bg) close(); };
    bg.append(sh); root.append(bg);
    const f = sh.querySelector("input, .opts button"); if (f) f.focus();
  }
  function close() { W.stack = []; $("sheetRoot").replaceChildren(); }
  function step(fn) { W.stack.push(fn); fn(); }
  function ask(q, opts, cb, hint) {
    step(() => sheet((sh) => {
      sh.append(el("h3", "", q)); if (hint) sh.append(el("p", "hint", hint));
      const o = el("div", "opts");
      opts.forEach(([label, v], i) => { const b = el("button", opts.length % 2 && i === opts.length - 1 ? "full" : "", label); b.type = "button"; b.onclick = () => cb(v); o.append(b); });
      sh.append(o);
    }));
  }
  function askNum(q, cb, def, hint) {
    step(() => sheet((sh) => {
      sh.append(el("h3", "", q)); if (hint) sh.append(el("p", "hint", hint));
      const i = el("input"); i.type = "number"; i.inputMode = "decimal"; i.min = "0"; i.step = "any"; i.id = "w-num"; if (def !== undefined) i.value = def;
      const ok = el("button", "", "التالي"); ok.type = "button";
      const go = () => { const v = num(i.value); if (v > 0 || (def !== undefined && i.value !== "")) cb(v); else i.focus(); };
      ok.onclick = go; i.onkeydown = (e) => { if (e.key === "Enter") go(); };
      sh.append(i, ok);
    }));
  }
  function askText(q, chips, cb) {
    step(() => sheet((sh) => {
      sh.append(el("h3", "", q));
      if (chips.length) { const c = el("div", "chips"); chips.forEach((t) => { const b = el("button", "", t); b.type = "button"; b.onclick = () => cb(t); c.append(b); }); sh.append(c); }
      const i = el("input"); i.type = "text"; i.id = "w-text";
      const ok = el("button", "", "التالي"); ok.type = "button";
      const go = () => { const v = i.value.trim(); if (v) cb(v); else i.focus(); };
      ok.onclick = go; i.onkeydown = (e) => { if (e.key === "Enter") go(); };
      sh.append(i, ok);
    }));
  }
  function finish(text, fn) {
    apply(text, fn); W.stack = [];
    sheet((sh) => { const d = el("div", "done"); d.append(el("h3", "", "تم ✓"), el("p", "", text)); const ok = el("button", "", "تمام"); ok.type = "button"; ok.onclick = close; d.append(ok); sh.append(d); });
  }
  const accOpts = (keys) => keys.map((k) => [ACC[k], k]);
  function pickItem(cb, opt) {
    opt = opt || {};
    let list = ITEMS.filter((i) => (opt.filter ? opt.filter(i) : true));
    const opts = list.filter((i) => !i.hidden).map((i) => [i.name + " (" + Math.round(opt.count ? opt.count(i) : total(i)) + ")", i.id])
      .concat(list.filter((i) => i.hidden).map((i) => [i.name + " · مخفي", i.id]));
    if (opt.allowNew) opts.push(["+ منتج جديد", "__new"]);
    if (!opts.length) { step(() => sheet((sh) => sh.append(el("h3", "", opt.empty || "لا توجد سلعة هنا.")))); return; }
    ask(opt.q || "أي منتج؟", opts, (id) => {
      if (id === "__new") askText("اسم المنتج الجديد؟", [], (name) => cb(norm({ id: uid(), name, cost: 0, price: 0, home: 0, wh: 0, transit: 0, stuck: [], hidden: false })));
      else cb(item(id));
    });
  }
  function pickCamp(cb, filter, q) {
    const list = CAMPS.filter((c) => c.status !== "closed" && (!filter || filter(c)));
    if (!list.length) { step(() => sheet((sh) => sh.append(el("h3", "", "لا توجد حملة هنا. أنشئ حملة أولاً من \"📣 حملة\".")))); return; }
    ask(q || "أي حملة؟", list.map((c) => [c.name, c.id]), (id) => cb(CAMPS.find((c) => c.id === id)));
  }
  // For a sale or return, optionally credit an active campaign of this product.
  function maybeCamp(it, cb) {
    const list = CAMPS.filter((c) => c.status !== "closed" && c.item === it.id);
    if (!list.length) { cb(null); return; }
    ask("من أي حملة؟", list.map((c) => [c.name, c.id]).concat([["بدون حملة", ""]]), (id) => cb(id ? CAMPS.find((c) => c.id === id) : null));
  }
  function debtFlow(cur) {
    const names = [...new Set((ST.debts || []).filter((d) => d.cur === cur && Math.abs(d.amount) > 0.004).map((d) => d.name))];
    askText("مع من؟", names, (who) => ask("ماذا حدث مع " + who + "؟", [["أعطيته (صار مديناً لي)", "lend"], ["أخذت منه (صرت مديناً له)", "borrow"], ["ردّ لي", "repaid"], ["رددت له", "repay"]], (kind) =>
      askNum("كم؟" + (cur === "USDT" ? " (USDT)" : " (دج)"), (a) =>
        ask(kind === "lend" || kind === "repay" ? "من أين خرج المال؟" : "أين دخل المال؟", cur === "USDT" ? [["Bybit", "bybit"], ["لم يتحرك مال", "none"]] : accOpts(DA_ACC).concat([["لم يتحرك مال", "none"]]), (k) => {
          const sign = kind === "lend" || kind === "repay" ? 1 : -1;
          const unit = cur === "USDT" ? ut : da;
          const txt = { lend: "أعطيت " + who, borrow: "أخذت من " + who, repaid: who + " ردّ لي", repay: "رددت لـ " + who }[kind] + " " + unit(a);
          finish(txt, () => {
            ST.debts = ST.debts || [];
            let d = ST.debts.find((x) => x.name === who && x.cur === cur);
            if (!d) { d = { name: who, cur, amount: 0 }; ST.debts.push(d); }
            d.amount = Math.round((num(d.amount) + sign * a) * 100) / 100;
            ST.debts = ST.debts.filter((x) => Math.abs(x.amount) > 0.004);
            if (k !== "none") ST[k] = num(ST[k]) - sign * a;
          });
        }))));
  }

  const F = {
    root: () => ask("ماذا حدث؟", [["💵 مال", "money"], ["📦 سلعة", "goods"], ["📣 حملة / إعلان", "camp"], ["💲 USDT", "usdt"], ["✏️ تصحيح رقم", "fix"]], (v) => F[v]()),

    // ----- money -----
    money: () => ask("ماذا حدث للمال؟", [["صرفت مالاً", "spent"], ["دخلني مال", "in"], ["dhd حوّلت لي", "payout"], ["نقلت مالاً من مكان لمكان", "move"], ["دين بالدينار", "debt"]], (v) => F[v]()),
    spent: () => askNum("كم صرفت؟ (دج)", (a) => ask("من أين؟", accOpts(DA_ACC), (k) => finish("صرفت " + da(a) + " من " + ACC[k], () => { ST[k] = num(ST[k]) - a; }))),
    in: () => askNum("كم دخلك؟ (دج)", (a) => ask("أين وضعته؟", accOpts(DA_ACC), (k) => finish("دخلني " + da(a) + " إلى " + ACC[k], () => { ST[k] = num(ST[k]) + a; }))),
    payout: () => askNum("كم حوّلت لك dhd؟ (دج)", (a) => ask("إلى أين؟", accOpts(DA_ACC), (k) => finish("dhd حوّلت " + da(a) + " إلى " + ACC[k], () => { ST.dhd = num(ST.dhd) - a; ST[k] = num(ST[k]) + a; }))),
    move: () => ask("من أين؟", accOpts(DA_ACC), (from) => ask("إلى أين؟", accOpts(DA_ACC.filter((k) => k !== from)), (to) => askNum("كم؟ (دج)", (a) =>
      finish("نقلت " + da(a) + " من " + ACC[from] + " إلى " + ACC[to], () => { ST[from] = num(ST[from]) - a; ST[to] = num(ST[to]) + a; })))),
    debt: () => debtFlow("DA"),

    // ----- goods -----
    goods: () => ask("ماذا حدث للسلعة؟", [["اشتريت سلعة", "bought"], ["أرسلت طرود للتوصيل", "ship"], ["تسلّم الزبون (بِعت)", "sold"], ["رجع مرتجع", "ret"], ["وصلني مرتجع عالق", "stuckIn"], ["نقلت سلعة (بيت ⇄ مخزن)", "moveGoods"], ["ضاعت / تالفة", "lost"]], (v) => F[v]()),
    bought: () => pickItem((it) => askNum("كم قطعة؟", (n) => askNum("بكم القطعة؟ (دج)", (p) => ask("أين وضعتها؟", [["البيت", "home"], ["المخزن (dhd)", "wh"]], (loc) =>
      ask("دفعت من أين؟", accOpts(DA_ACC).concat([["لم أدفع من مالي", "none"]]), (k) =>
        finish("اشتريت " + n + " " + it.name + " × " + da(p) + " · في " + LOCN[loc], (touch) => { const t = touch("items", it); t[loc] += n; t.cost = p; if (k !== "none") ST[k] = num(ST[k]) - n * p; }))), it.cost || undefined)), { allowNew: true }),
    ship: () => pickItem((it) => askNum("كم قطعة خرجت للتوصيل؟", (n) => ask("خرجت من أين؟", [["المخزن (dhd)", "wh"], ["البيت", "home"]], (loc) =>
      finish("أرسلت " + n + " " + it.name + " من " + LOCN[loc], (touch) => { const t = touch("items", it); t[loc] -= n; t.transit += n; })))),
    sold: () => pickItem((it) => askNum("كم قطعة تسلّمها الزبون؟", (n) => askNum("كم ستعطيك dhd عنها؟ (المجموع بالدينار)", (a) => maybeCamp(it, (c) =>
      finish("بِعت " + n + " " + it.name + " · +" + da(a) + " عند dhd", (touch) => { takeOut(touch("items", it), n); ST.dhd = num(ST.dhd) + a; if (c) { const x = touch("camps", c); x.delivered = num(x.delivered) + n; } }))),
      undefined, "صافي ما ستحوّله dhd بعد التخفيض والرسوم. إن لم تعرفه بالضبط، اكتب تقريباً وصحّحه لاحقاً.")),
    ret: () => pickItem((it) => askNum("كم قطعة رجعت؟", (n) => ask("الطرد خرج من أين؟", [["البيت (−50 دج لكل طرد)", "home"], ["المخزن (مجاني)", "wh"]], (from) =>
      ask("وأين هي الآن؟", [["رجعت لنفس المكان", "back"], ["عالقة عند dhd", "stuck"]], (now) =>
        finish(n + " " + it.name + " مرتجعة · " + (now === "back" ? "رجعت إلى " + LOCN[from] : "عالقة عند dhd") + (from === "home" ? " · −" + da(RETURN_FEE * n) : ""), (touch) => {
          const t = touch("items", it); takeOut(t, n, from);
          if (now === "back") t[from] += n; else t.stuck.push({ d: today(), n });
          if (from === "home") ST.dhd = num(ST.dhd) - RETURN_FEE * n;
        }))))),
    stuckIn: () => pickItem((it) => askNum("كم قطعة وصلتك؟", (n) => ask("أين وضعتها؟", [["البيت", "home"], ["المخزن", "wh"], ["تالفة / ضاعت", "lost"]], (to) =>
      finish("وصلني " + n + " " + it.name + " مرتجع · " + (to === "lost" ? "تالفة" : "إلى " + LOCN[to]), (touch) => { const t = touch("items", it); takeStuck(t, n); if (to !== "lost") t[to] += n; })), String(stuckN(it))),
      { filter: (i) => stuckN(i) > 0, count: stuckN, q: "أي منتج وصلك مرتجعه؟", empty: "لا يوجد مرتجع عالق." }),
    moveGoods: () => pickItem((it) => ask("من أين إلى أين؟", [["البيت ← المخزن", "home>wh"], ["المخزن ← البيت", "wh>home"]], (dir) => {
      const [from, to] = dir.split(">");
      askNum("كم قطعة؟", (n) => finish("نقلت " + n + " " + it.name + " من " + LOCN[from] + " إلى " + LOCN[to], (touch) => { const t = touch("items", it); t[from] -= n; t[to] += n; }));
    })),
    lost: () => pickItem((it) => askNum("كم قطعة؟", (n) => ask("من أين؟", [["البيت", "home"], ["المخزن", "wh"], ["في التوصيل", "transit"]], (loc) =>
      finish(n + " " + it.name + " ضاعت أو تالفة", (touch) => { touch("items", it)[loc] -= n; })))),

    // ----- campaigns -----
    camp: () => ask("ماذا حدث في الحملات؟", [["صرفت إعلانات", "ads"], ["حملة جديدة", "newCamp"], ["نتائج حملة الشراكة", "pres"], ["تسوية مع الشريك", "settle"], ["أوقفت حملة", "stop"]], (v) => F[v]()),
    newCamp: () => ask("أي نوع؟", Object.entries(CTYPE).map(([k, v]) => [v, k]), (type) => {
      if (type === "partner") {
        const partners = [...new Set(CAMPS.filter((c) => c.type === "partner").map((c) => c.partner).filter(Boolean))];
        askText("اسم الشريك؟", partners, (partner) => askText("اسم المنتج؟", [], (pn) => askNum("سعر البيع؟ (دج)", (sell) => askNum("سعر الشراء؟ (دج)", (buy) => askNum("نصيبك من الربح؟ (%)", (share) =>
          finish("حملة شراكة جديدة: " + pn + " مع " + partner, (touch) => { touch("camps", { id: uid(), type, name: pn + " · " + partner, partner, sell, buy, share, adUsd: 0, orders: 0, delivered: 0, settled: 0, start: today(), status: "active" }); }), "50")))));
      } else {
        pickItem((it) => askNum("بكم تبيع القطعة؟ (دج)", (price) =>
          finish("حملة جديدة: " + it.name + " · " + CTYPE[type], (touch) => {
            const t = touch("items", it); t.price = price;
            touch("camps", { id: uid(), type, item: it.id, name: it.name + (type === "test" ? " · تست" : ""), adUsd: 0, orders: 0, delivered: 0, start: today(), status: "active" });
          }), it.price || undefined, "سعر البيع العادي. منه ومن سعر الشراء نحسب نقطة التعادل."), { allowNew: true });
      }
    }),
    ads: () => pickCamp((c) => askNum("كم USDT صرفت؟", (u) => askNum("كم طلبية جاءت؟", (o) => {
      const x = Object.assign({}, c, { adUsd: num(c.adUsd) + u, orders: num(c.orders) + o }); const s = cstats(x);
      finish("إعلانات " + c.name + ": " + ut(u) + " · " + o + " طلبية" + (s.cpo !== null && s.be !== null ? " · الطلبية " + usd(s.cpo) + " / تعادل " + usd(s.be) + " → " + s.v[1] : ""), (touch) => { const t = touch("camps", c); t.adUsd = x.adUsd; t.orders = x.orders; ST.bybit = num(ST.bybit) - u; });
    }, "0", "من مدير الإعلانات. اكتب 0 إن لم تأتِ طلبيات."))),
    pres: () => pickCamp((c) => askNum("كم طلبية تسلّمها الزبائن؟", (n) => finish(c.name + ": +" + n + " مُسلَّمة", (touch) => { const t = touch("camps", c); t.delivered = num(t.delivered) + n; })), (c) => c.type === "partner"),
    settle: () => pickCamp((c) => { const s = cstats(c);
      ask("ماذا حدث؟", [["استلمت منه", "in"], ["دفعت له", "out"]], (dir) => askNum("كم؟ (دج)", (a) => ask(dir === "in" ? "أين وضعته؟" : "من أين دفعت؟", accOpts(DA_ACC), (k) =>
        finish((dir === "in" ? "استلمت من " : "دفعت لـ ") + (c.partner || "الشريك") + " " + da(a), (touch) => { const t = touch("camps", c); t.settled = num(t.settled) + (dir === "in" ? a : -a); ST[k] = num(ST[k]) + (dir === "in" ? a : -a); })),
        String(Math.round(Math.abs(s.owes))), s.owes >= 0 ? "الشريك مدين لك الآن بـ " + da(s.owes) : "أنت مدين للشريك بـ " + da(-s.owes)));
    }, (c) => c.type === "partner"),
    stop: () => pickCamp((c) => finish("أوقفت حملة " + c.name, (touch) => { touch("camps", c).status = "closed"; })),

    // ----- USDT -----
    usdt: () => ask("ماذا حدث للـ USDT؟", [["اشتريت USDT", "buy"], ["صرفت إعلانات", "ads"], ["دين USDT", "udebt"]], (v) => F[v]()),
    buy: () => askNum("كم دينار دفعت؟", (d) => ask("من أين؟", accOpts(DA_ACC), (k) => askNum("كم USDT وصلك؟", (u) =>
      finish("اشتريت " + ut(u) + " بـ " + da(d) + " (" + Math.round(d / u) + " دج للدولار)", () => { ST[k] = num(ST[k]) - d; ST.bybit = num(ST.bybit) + u; ST.rate = d / u; })))),
    udebt: () => debtFlow("USDT"),

    // ----- fix -----
    fix: () => ask("أي رقم تريد تصحيحه؟", [["كاش", "cash"], ["مخبأ البيت", "stash"], ["CCP", "ccp"], ["عند dhd", "dhd"], ["Bybit", "bybit"], ["سلعة منتج", "item"], ["سعر الدولار", "rate"]], (k) => {
      if (k === "item") pickItem((it) => askNum(it.name + " في البيت؟", (h) => askNum(it.name + " في المخزن؟", (w) => askNum(it.name + " في التوصيل؟", (tr) =>
        finish("صحّحت " + it.name + ": بيت " + h + " · مخزن " + w + " · توصيل " + tr, (touch) => { const t = touch("items", it); t.home = h; t.wh = w; t.transit = tr; }), String(it.transit)), String(it.wh)), String(it.home)), { allowNew: true });
      else askNum(k === "rate" ? "كم دينار للدولار الآن؟" : "كم " + ACC[k] + " الآن بالضبط؟", (v) => finish("صحّحت " + (k === "rate" ? "سعر الدولار" : ACC[k]) + " إلى " + (k === "bybit" ? ut(v) : k === "rate" ? v + " دج" : da(v)), () => { ST[k] = v; }), String(Math.round(num(ST[k]) * 100) / 100));
    })
  };

  $("go").onclick = () => { W.stack = []; F.root(); };
  $("newCampBtn").onclick = () => { W.stack = []; F.newCamp(); };
  $("adsBtn").onclick = () => { W.stack = []; F.ads(); };
  document.addEventListener("keydown", (e) => { if (e.key === "Escape") close(); });
  render(); init();
})();
</script>

</body>
</html>
