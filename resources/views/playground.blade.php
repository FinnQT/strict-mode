<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Strict Mode Playground</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Nunito', 'ui-sans-serif', 'system-ui', 'sans-serif'] } } } }</script>
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<style>
  /* ===== Token thiết kế: khoảng cách 4/8/12/16/24/32/48; góc vuông bo nhẹ 4px ===== */
  :root {
    --s1: 4px; --s2: 8px; --s3: 12px; --s4: 16px; --s6: 24px; --s8: 32px; --s12: 48px;
    --r: 4px;
    --bg: #F1F3F6; --ink: #111827; --mute: #4B5563; --soft: #F1F5F9; --head: #E8ECF1;
    --line: #B8C1CE; --line-strong: #8794A6;
    --accent: #1D4ED8; --accent-dark: #1E3A8A; --accent-bg: #E3ECFE;
    --amber: #FEF0C7; --amber-line: #E3B341; --amber-strong: #FCE08A; --amber-ink: #6B3A0C;
    --mint: #D8F5E8; --mint-line: #4DBF94; --mint-strong: #A7F0CF; --mint-ink: #065F46;
    --err: #BE123C;
    --bar-h: 230px;
  }
  [v-cloak] { display: none; }
  html { font-size: 16px; }
  body { background: var(--bg); color: var(--ink); font: 17px/1.5 Nunito, ui-sans-serif, system-ui, sans-serif; }
  .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
  .cap { font-size: 15px; color: var(--mute); }
  .h1 { font-size: 28px; font-weight: 800; line-height: 1.2; }
  .h2 { font-size: 20px; font-weight: 800; line-height: 1.3; }
  .wrap { max-width: 1440px; margin: 0 auto; padding-left: var(--s6); padding-right: var(--s6); }
  .row { display: flex; align-items: center; gap: var(--s4); }
  .grow { flex: 1; min-width: 0; }
  button { cursor: pointer; font: inherit; border: 1px solid transparent; background: none; color: inherit; border-radius: var(--r); }

  /* ===== Thanh mode ===== */
  .modebar { position: sticky; top: 0; z-index: 20; transition: background-color .25s ease; border-bottom: 1px solid var(--line-strong); }
  .modebar .wrap { position: relative; padding-top: var(--s4); padding-bottom: var(--s4); display: flex; flex-direction: column; gap: var(--s3); }
  .lbl { width: 84px; flex: none; font-size: 14px; font-weight: 800; letter-spacing: .06em; color: var(--mute); text-transform: uppercase; }
  .seg-wrap { display: flex; gap: var(--s1); padding: var(--s1); border-radius: 6px; background: #fff; border: 1px solid var(--line-strong); }
  .seg { display: flex; flex-direction: column; align-items: flex-start; padding: var(--s2) var(--s4); line-height: 1.25; transition: background-color .25s ease; }
  .seg small { font-size: 14px; color: var(--mute); }
  .seg:hover { background: var(--soft); border-color: var(--line); }
  .seg.on { background: var(--accent-bg); border-color: var(--accent); font-weight: 800; box-shadow: inset 4px 0 0 var(--accent); padding-left: 20px; }
  .seg.on small { color: var(--accent-dark); }
  .adv { padding: var(--s2) var(--s4); background: #fff; border-color: var(--line-strong); font-weight: 700; }
  .adv:hover { background: var(--soft); }
  .adv.on { background: var(--accent-bg); border-color: var(--accent); color: var(--accent-dark); }
  .modeline { min-height: 44px; padding: var(--s2) var(--s3); background: rgba(255,255,255,.75); border: 1px solid var(--line); border-radius: var(--r); }
  .modeline .mono { font-size: 16px; overflow-wrap: anywhere; }
  .tk { color: #374151; }
  .tk-strict { background: var(--mint-strong); color: var(--mint-ink); border-radius: 2px; padding: 0 var(--s1); font-weight: 700; }
  .diff { transition: opacity .7s ease; margin-left: var(--s3); white-space: normal; }
  .diff.fade { opacity: 0; }
  .plus, .minus { font: 700 15px ui-monospace, Menlo, Consolas, monospace; border-radius: 2px; padding: 0 var(--s2); margin-right: var(--s2); display: inline-block; }
  .plus { color: #047857; background: var(--mint); border: 1px solid var(--mint-line); }
  .minus { color: #E11D48; background: #FFE4E6; border: 1px solid #FDA4AF; text-decoration: line-through; }
  .live { display: inline-block; width: 8px; height: 8px; border-radius: 2px; background: #10B981; margin-left: var(--s1); vertical-align: middle; }
  .badge { font-weight: 900; font-size: 17px; padding: var(--s1) var(--s4); border-radius: var(--r); white-space: nowrap; border: 1px solid; }
  .badge.on { background: var(--mint-strong); color: var(--mint-ink); border-color: var(--mint-line); }
  .badge.off { background: var(--amber-strong); color: var(--amber-ink); border-color: var(--amber-line); }
  .mascot { font-size: 26px; line-height: 1; }
  .drawer { position: absolute; left: var(--s6); right: var(--s6); top: 100%; background: #fff; border: 1px solid var(--line-strong); border-radius: 6px; padding: var(--s6); box-shadow: 0 8px 24px rgba(15,23,42,.15); display: grid; grid-template-columns: 1fr 1fr; gap: var(--s8); z-index: 30; }
  .swrow { display: flex; align-items: center; gap: var(--s3); padding: var(--s1) 0; cursor: pointer; }
  .code { background: var(--soft); border: 1px solid var(--line); border-radius: var(--r); padding: var(--s4); font-size: 15px; white-space: pre-wrap; overflow-wrap: anywhere; margin-top: var(--s2); }

  /* ===== Switch tự style (vuông bo nhẹ) ===== */
  input.sw { appearance: none; -webkit-appearance: none; width: 38px; height: 22px; border-radius: 4px; background: #CBD5E1; border: 1px solid var(--line-strong); position: relative; flex: none; cursor: pointer; transition: background-color .2s ease; outline: none; }
  input.sw::after { content: ""; position: absolute; top: 2px; left: 2px; width: 16px; height: 16px; border-radius: 2px; background: #fff; box-shadow: 0 0 0 1px var(--line-strong); transition: transform .2s ease; }
  input.sw:checked { background: var(--accent); border-color: var(--accent-dark); }
  input.sw:checked::after { transform: translateX(16px); }
  input.sw:focus-visible { box-shadow: 0 0 0 3px rgba(29,78,216,.3); }

  /* ===== Bố cục ===== */
  .layout { display: block; padding-top: var(--s8); padding-bottom: var(--s12); }
  .scn-col { display: flex; gap: var(--s4); overflow-x: auto; margin-bottom: var(--s6); padding-bottom: var(--s2); }
  .grp { flex: none; display: flex; flex-direction: column; gap: var(--s2); }
  .grp-name { font-size: 14px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: var(--mute); padding: var(--s1) 0; white-space: nowrap; }
  .scn { display: flex; gap: var(--s3); align-items: baseline; width: 100%; text-align: left; padding: var(--s2) var(--s3); white-space: nowrap; background: #fff; border-color: var(--line); }
  .scn .n { width: 22px; color: var(--mute); font-weight: 700; flex: none; }
  .scn:hover { background: var(--soft); border-color: var(--line-strong); }
  .scn.on { background: var(--accent-bg); border-color: var(--accent); box-shadow: inset 4px 0 0 var(--accent); font-weight: 800; padding-left: 16px; }
  .stage { display: flex; flex-direction: column; gap: var(--s8); min-width: 0; }
  @media (min-width: 1280px) {
    .layout { display: grid; grid-template-columns: 300px minmax(0, 1fr); gap: var(--s8); align-items: start; }
    .scn-col { flex-direction: column; gap: var(--s6); overflow: visible; margin: 0; padding: 0;
      position: sticky; top: calc(var(--bar-h) + var(--s4)); max-height: calc(100vh - var(--bar-h) - var(--s6)); overflow-y: auto; }
  }

  .card { background: #fff; border: 1px solid var(--line); border-radius: 6px; padding: var(--s6); box-shadow: 0 1px 2px rgba(15,23,42,.06); }
  .fields { display: grid; grid-template-columns: 1fr 1fr; gap: var(--s4) var(--s6); margin-top: var(--s6); }
  .field { display: flex; flex-direction: column; gap: var(--s1); transition: opacity .2s ease; }
  .field.off { opacity: .45; }
  .field-head { display: flex; align-items: center; gap: var(--s2); }
  .field-head label { margin-left: auto; display: flex; align-items: center; gap: var(--s2); font-size: 15px; color: var(--mute); cursor: pointer; }
  .inp { width: 100%; background: #fff; border: 1px solid var(--line-strong); border-radius: var(--r); padding: var(--s3) var(--s4); font: 17px ui-monospace, Menlo, Consolas, monospace; color: var(--ink); }
  .inp:focus { outline: 2px solid var(--accent); outline-offset: 0; border-color: var(--accent); }
  .inp:disabled { text-decoration: line-through; background: var(--soft); }
  .actions { display: flex; align-items: center; gap: var(--s4); margin-top: var(--s6); flex-wrap: wrap; }
  .btn-main { background: var(--accent); color: #fff; border-color: var(--accent-dark); font-weight: 800; padding: var(--s3) var(--s8); }
  .btn-main:hover { background: var(--accent-dark); }
  .btn-soft { background: #fff; border-color: var(--line-strong); font-weight: 700; padding: var(--s3) var(--s6); }
  .btn-soft:hover { background: var(--soft); }
  .btn-quiet { padding: var(--s2) var(--s4); font-weight: 700; background: #fff; border-color: var(--line-strong); }
  .btn-quiet:hover { background: var(--soft); }
  button:disabled { opacity: .5; cursor: default; }
  .btn-main kbd, .btn-soft kbd { font: inherit; font-size: 14px; font-weight: 600; opacity: .8; }

  /* ===== Kết quả ===== */
  .summary { font-size: 20px; font-weight: 800; line-height: 1.4; }
  .res-grid { display: grid; gap: var(--s6); grid-template-columns: minmax(0, 1fr); }
  .res-grid.two { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); }
  .res { display: flex; flex-direction: column; gap: var(--s4); }
  .banner { border: 1px solid; border-radius: var(--r); padding: var(--s4) var(--s6); font-size: 19px; font-weight: 800; line-height: 1.4; animation: slideIn .22s ease-out; }
  .banner.amber { background: var(--amber); color: var(--amber-ink); border-color: var(--amber-line); box-shadow: inset 4px 0 0 var(--amber-line); }
  .banner.mint { background: var(--mint); color: var(--mint-ink); border-color: var(--mint-line); box-shadow: inset 4px 0 0 var(--mint-line); }
  .errline { margin-top: var(--s2); color: var(--err); font: 600 15px ui-monospace, Menlo, Consolas, monospace; overflow-wrap: anywhere; }
  .tbl { width: 100%; border-collapse: collapse; border: 1px solid var(--line); }
  .tbl th { text-align: left; font-size: 14px; font-weight: 800; color: var(--mute); padding: var(--s2) var(--s3); background: var(--head); border-bottom: 1px solid var(--line-strong); }
  .tbl td { padding: var(--s2) var(--s3); font: 16px ui-monospace, Menlo, Consolas, monospace; border-top: 1px solid #E2E8F0; }
  .tbl tr.chg td, .tbl tr.sus td, .tbl td.chg { background: var(--amber) !important; border-top-color: var(--amber-strong); }
  .tbl .sent-chg { text-decoration: line-through; color: var(--mute); }
  .tbl .stored-chg { font-weight: 800; color: var(--amber-ink); }
  .tbl tr.fresh td { animation: flashRow .25s ease-out 1; }
  .pipe { display: flex; flex-wrap: wrap; align-items: center; gap: var(--s2) var(--s3); font-size: 15px; }
  .pipe .step { display: inline-flex; align-items: center; gap: var(--s2); }
  .pipe .dot { width: 10px; height: 10px; border-radius: 2px; }
  .dot.mint { background: #10B981; } .dot.amber { background: #F59E0B; }
  details { border: 1px solid var(--line); border-radius: var(--r); margin-top: var(--s2); background: #fff; }
  details summary { cursor: pointer; font-size: 15px; font-weight: 700; color: var(--mute); list-style: none; padding: var(--s2) var(--s3); background: var(--soft); border-radius: var(--r); }
  details summary:hover { background: var(--head); }
  details summary::-webkit-details-marker { display: none; }
  details summary::before { content: "▸ "; }
  details[open] summary::before { content: "▾ "; }
  details .body { padding: var(--s3); }
  .empty { padding: var(--s8) 0; text-align: center; font-size: 17px; color: var(--mute); border: 1px dashed var(--line-strong); border-radius: 6px; background: rgba(255,255,255,.5); }
  .sk { background: var(--head); border-radius: var(--r); }


  /* ===== Chút dễ thương: nút kiểu "sticker" đổ bóng cứng, thẻ nhấc nhẹ, mặt kaomoji ===== */
  .card { box-shadow: 4px 4px 0 #D3DAE5; transition: transform .2s ease, box-shadow .2s ease; }
  .res.card:hover { transform: translate(-2px, -2px); box-shadow: 6px 6px 0 #C3CCDA; }
  .res.t-amber { border-top: 4px solid var(--amber-line); }
  .res.t-mint { border-top: 4px solid var(--mint-line); }
  .btn-main, .btn-soft, .btn-quiet, .adv { box-shadow: 2px 2px 0 var(--line-strong); transition: transform .15s ease, box-shadow .15s ease, background-color .2s ease; }
  .btn-main { box-shadow: 3px 3px 0 var(--accent-dark); }
  .btn-main:hover:not(:disabled), .btn-soft:hover:not(:disabled), .btn-quiet:hover, .adv:hover { transform: translate(-1px, -1px); }
  .btn-main:active:not(:disabled), .btn-soft:active:not(:disabled), .btn-quiet:active, .adv:active { transform: translate(2px, 2px); box-shadow: 0 0 0 transparent; }
  .scn .n { background: var(--head); border-radius: 3px; text-align: center; width: 24px; height: 24px; line-height: 24px; font-size: 14px; }
  .scn.on .n { background: var(--accent); color: #fff; }
  .scn:hover:not(.on) { transform: translateX(2px); }
  .scn { transition: transform .15s ease, background-color .15s ease; }
  .face { font-weight: 700; margin-right: var(--s3); white-space: nowrap; }
  .banner .face { display: inline-block; font-size: 22px; }
  .mascot { display: inline-block; animation: wiggle .25s ease-out 1; }
  @keyframes wiggle { 0% { transform: rotate(-12deg) scale(.8); } 60% { transform: rotate(8deg) scale(1.1); } 100% { transform: none; } }

  /* ===== Chuyển động (tối đa 3 loại, <= 250ms) ===== */
  @keyframes slideIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: none; } }
  @keyframes flashRow { from { background-color: var(--amber-strong); } to { background-color: transparent; } }
  .flash { animation: flashRow .25s ease-out 1; }
  @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } }
</style>
</head>
<body>
@verbatim
<div id="app" v-cloak>

  <!-- ============ THANH MODE (sticky) ============ -->
  <header class="modebar" ref="bar" :style="{ backgroundColor: barBg }">
    <div class="wrap">
      <div class="row">
        <h1 class="h1">🍓 Strict Mode Playground</h1>
        <span class="cap mono">{{ dbLabel }}</span>
        <button class="btn-quiet" style="margin-left:auto" @click="reset">Reset (R)</button>
      </div>

      <div class="row">
        <span class="lbl">Laravel</span>
        <div class="seg-wrap">
          <button v-for="m in MODES" :key="m.id" class="seg" :class="{ on: mode === m.id }" @click="pickMode(m.id)">
            <span>{{ m.title }}</span><small class="mono">{{ m.sub }}</small>
          </button>
        </div>
        <button class="adv" :class="{ on: customOpen || mode === 'custom' }" @click="customOpen = !customOpen">Nâng cao ▾</button>
        <span v-if="fatal" class="cap" style="color:var(--err)">{{ fatal }}</span>
      </div>

      <div class="row modeline" v-if="effective" :key="'g' + flash.g" :class="{ flash: flash.g > 0 }">
        <span class="lbl">Global <i class="live" title="tự cập nhật mỗi 2 giây"></i></span>
        <div class="grow"><span class="mono"><template v-for="(t, i) in tok(effective.global_mode)" :key="t"><span v-if="i">, </span><span :class="t.startsWith('STRICT_') ? 'tk-strict' : 'tk'">{{ t }}</span></template></span><span v-if="diffs.g" class="diff" :class="{ fade: diffs.g.fade }"><span v-for="a in diffs.g.added" :key="'a' + a" class="plus">+{{ a }}</span><span v-for="m in diffs.g.removed" :key="'r' + m" class="minus">−{{ m }}</span></span></div>
      </div>
      <div class="row modeline" v-if="effective" :key="'s' + flash.s" :class="{ flash: flash.s > 0 }">
        <span class="lbl">Session</span>
        <div class="grow"><span class="mono"><template v-for="(t, i) in tok(effective.session_mode)" :key="t"><span v-if="i">, </span><span :class="t.startsWith('STRICT_') ? 'tk-strict' : 'tk'">{{ t }}</span></template></span><span v-if="diffs.s" class="diff" :class="{ fade: diffs.s.fade }"><span v-for="a in diffs.s.added" :key="'a' + a" class="plus">+{{ a }}</span><span v-for="m in diffs.s.removed" :key="'r' + m" class="minus">−{{ m }}</span></span></div>
        <span class="badge" :class="effective.is_strict ? 'on' : 'off'">{{ effective.is_strict ? 'STRICT ON' : 'STRICT OFF' }}</span>
        <span class="mascot" :key="effective.is_strict">{{ effective.is_strict ? '🧐' : '😴' }}</span>
      </div>

      <div v-if="customOpen" class="drawer">
        <div>
          <div class="cap">Mode tự chọn (áp lên SESSION)</div>
          <label v-for="m in ALL_MODES" :key="m" class="swrow"><input type="checkbox" class="sw" :value="m" v-model="customModes" @change="pickCustom"><span class="mono">{{ m }}</span></label>
        </div>
        <div>
          <div class="cap">Config đang áp dụng</div>
          <pre class="code mono">{{ cfgText }}</pre>
        </div>
      </div>
    </div>
  </header>

  <main class="wrap layout">
    <!-- ============ DANH SÁCH KỊCH BẢN ============ -->
    <nav class="scn-col">
      <div v-for="g in GROUPS" :key="g.name" class="grp">
        <div class="grp-name">{{ g.name }}</div>
        <button v-for="s in g.items" :key="s.id" class="scn" :class="{ on: sc.id === s.id }" @click="select(s)">
          <span class="n">{{ s.n }}</span><span>{{ s.title }}</span>
        </button>
      </div>
    </nav>

    <!-- ============ SÂN KHẤU ============ -->
    <div class="stage">
      <section class="card">
        <h2 class="h2">{{ sc.title }}</h2>
        <div class="cap" style="font-size:17px">{{ sc.hint }}</div>

        <div v-if="Object.keys(form).length" class="fields">
          <div v-for="(f, k) in form" :key="k" class="field" :class="{ off: !f.send }">
            <div class="field-head">
              <span class="mono" style="font-weight:700">{{ LABELS[k][0] }}</span>
              <span class="cap mono">{{ LABELS[k][1] }}</span>
              <label>gửi <input type="checkbox" class="sw" v-model="f.send"></label>
            </div>
            <input class="inp" v-model="f.value" :disabled="!f.send">
          </div>
        </div>
        <pre v-else class="code mono" style="font-size:16px;margin-top:var(--s6)">{{ sc.fixedSql }}</pre>

        <div class="actions">
          <button class="btn-main" :disabled="busy" @click="run">Chạy <kbd>(Enter)</kbd></button>
          <button class="btn-soft" :disabled="busy" @click="compare">So sánh 2 chế độ <kbd>(C)</kbd></button>
          <span class="cap">{{ sc.compare[0].label }} và {{ sc.compare[1].label }}</span>
        </div>
      </section>

      <section>
        <div v-if="!results.length && !busy" class="empty"><div class="face mono" style="font-size:28px">(•‿•)</div>Chọn kịch bản rồi bấm Chạy</div>
        <div v-if="results.length === 2 && !busy" class="summary" style="margin-bottom:var(--s6)">{{ summary }}</div>

        <div v-if="results.length || busy" class="res-grid" :class="{ two: expected > 1 }">
          <div v-for="r in results" :key="r.key" class="card res" :class="'t-' + verdict(r).tone">
            <div>
              <div class="h2">{{ r.label }}</div>
              <div class="cap mono">{{ r.data.effective.is_strict ? 'STRICT ON' : 'STRICT OFF' }} · {{ r.data.elapsed_ms }} ms</div>
            </div>

            <div class="banner" :class="verdict(r).tone">
              <span class="face mono">{{ face(r) }}</span>
              {{ verdict(r).text }}
              <div v-if="verdict(r).err" class="errline">{{ verdict(r).err }}</div>
            </div>

            <div v-if="r.data.error" style="color:var(--mute)">Không ghi gì.</div>
            <div v-else-if="r.data.rows" style="overflow-x:auto">
              <table class="tbl">
                <thead><tr><th v-for="c in cols(r.data.rows)" :key="c">{{ c }}</th></tr></thead>
                <tbody><tr v-for="(row, i) in r.data.rows" :key="i"><td v-for="c in cols(r.data.rows)" :key="c" :class="{ chg: r.sc === 'myisam' && c === 'level' && Number(row[c]) === 127 }">{{ fmt(row[c]) }}</td></tr></tbody>
              </table>
              <div v-if="!r.data.rows.length" class="cap">(bảng trống)</div>
            </div>
            <table v-else class="tbl">
              <thead><tr><th>Cột</th><th>Gửi vào</th><th>Lưu thành</th></tr></thead>
              <tbody>
                <tr v-for="k in fieldKeys(r)" :key="k" :class="{ chg: changed(r, k) }">
                  <td>{{ k }}</td>
                  <td :class="{ 'sent-chg': changed(r, k) }">{{ fmt(r.data.sent[k], true) }}</td>
                  <td :class="{ 'stored-chg': changed(r, k) }">{{ fmt(r.data.stored[k]) }}</td>
                </tr>
              </tbody>
            </table>

            <div class="pipe">
              <template v-for="(p, i) in pipe(r)" :key="i">
                <span v-if="i" class="cap">→</span>
                <span class="step"><i class="dot" :class="p.tone"></i><span class="cap">{{ PIPE_NAMES[i] }}</span><span>{{ p.text }}</span></span>
              </template>
            </div>

            <div>
              <details>
                <summary>SQL đã chạy</summary>
                <div class="body"><pre class="code mono" style="margin:0">{{ r.data.sql }}</pre></div>
              </details>
              <details>
                <summary>SHOW WARNINGS ({{ r.data.warnings.length }}) · Laravel không tự chạy bước này</summary>
                <div class="body">
                  <table v-if="r.data.warnings.length" class="tbl">
                    <tr v-for="(w, i) in r.data.warnings" :key="i"><td>{{ w.level }}</td><td>{{ w.code }}</td><td style="font-family:inherit">{{ w.message }}</td></tr>
                  </table>
                  <div v-else class="cap">Không có warning.</div>
                </div>
              </details>
            </div>
          </div>

          <div v-for="n in pending" :key="'sk' + n" class="card res">
            <div class="sk" style="height:28px;width:40%"></div>
            <div class="sk" style="height:64px"></div>
            <div class="sk" style="height:120px"></div>
          </div>
        </div>
      </section>

      <section class="card">
        <div class="row" style="align-items:baseline">
          <h2 class="h2">Bảng products</h2>
          <span class="cap">{{ tables.products.length }} dòng, {{ suspiciousCount }} đáng ngờ</span>
        </div>
        <div style="overflow-x:auto;margin-top:var(--s3)">
          <table class="tbl">
            <thead><tr><th v-for="c in PCOLS" :key="c">{{ c }}</th><th></th></tr></thead>
            <tbody>
              <tr v-for="p in tables.products" :key="p.id" :class="{ sus: bugs(p).length, fresh: newIds.includes(p.id) }">
                <td v-for="c in PCOLS" :key="c">{{ fmt(p[c]) }}</td>
                <td style="font-family:inherit;font-size:14px;color:var(--amber-ink);font-weight:700">{{ bugs(p).length ? 'đáng ngờ: ' + bugs(p).join(', ') : '' }}</td>
              </tr>
            </tbody>
          </table>
          <div v-if="!tables.products.length" class="cap" style="padding:var(--s3)">Bảng trống. Bấm Reset (R) để nạp dữ liệu mẫu.</div>
        </div>

        <div v-if="sc.id === 'myisam'" style="margin-top:var(--s6)">
          <h2 class="h2">Bảng log_myisam</h2>
          <table class="tbl" style="width:auto;margin-top:var(--s3)">
            <thead><tr><th>id</th><th>level</th><th></th></tr></thead>
            <tbody>
              <tr v-for="l in tables.log_myisam" :key="l.id" :class="{ sus: Number(l.level) === 127 }">
                <td>{{ l.id }}</td><td>{{ l.level }}</td>
                <td style="font-family:inherit;font-size:14px;color:var(--amber-ink);font-weight:700">{{ Number(l.level) === 127 ? 'đáng ngờ: bị cắt về 127' : '' }}</td>
              </tr>
            </tbody>
          </table>
          <div v-if="!tables.log_myisam.length" class="cap" style="padding:var(--s3) 0">Bảng trống.</div>
        </div>
      </section>
    </div>
  </main>
</div>

<script>
const { createApp } = Vue;
const ALL_MODES = ['STRICT_TRANS_TABLES', 'STRICT_ALL_TABLES', 'NO_ZERO_IN_DATE', 'NO_ZERO_DATE', 'ERROR_FOR_DIVISION_BY_ZERO', 'ONLY_FULL_GROUP_BY'];
const MODES = [
  { id: 'loose', title: 'Dễ tính', sub: "'strict' => false" },
  { id: 'strict', title: 'Khó tính', sub: "'strict' => true" },
  { id: 'server', title: 'Theo server', sub: 'kế thừa GLOBAL' },
];
const LABELS = {
  name: ['name', 'VARCHAR(10)'], price: ['price', 'INT'], stock: ['stock', 'TINYINT'],
  sale_date: ['sale_date', 'DATE'], note: ['note', 'NOT NULL, không DEFAULT'], divisor: ['ratio = 10 /', 'số chia'],
};
const PCOLS = ['id', 'name', 'price', 'stock', 'sale_date', 'ratio', 'note'];
const PIPE_NAMES = ['MariaDB', 'PDO', 'Laravel', 'Ứng dụng'];

// Cặp chế độ so sánh
const LOOSE = { label: 'Dễ tính', mode: 'loose' };
const STRICT = { label: 'Khó tính', mode: 'strict' };
const custom = (modes) => ({ label: modes.join(' + '), mode: 'custom', modes });
const base = (o = {}) => ({ name: 'Ban phim', price: '100000', stock: '10', sale_date: '2026-01-15', note: 'Hang moi', ...o });

const SC = [
  { id: 'too_long', title: 'Chuỗi quá dài', hint: 'name là VARCHAR(10) mà gửi 21 ký tự.', fields: base({ name: 'Ban phim co khong day' }), compare: [LOOSE, STRICT] },
  { id: 'text_in_int', title: 'Chữ vào cột số', hint: "price là INT nhưng gửi 'abc'.", fields: base({ price: 'abc' }), compare: [LOOSE, STRICT] },
  { id: 'out_of_range', title: 'Số vượt giới hạn', hint: 'stock là TINYINT (tối đa 127) mà gửi 999.', fields: base({ stock: '999' }), compare: [LOOSE, STRICT] },
  { id: 'bad_date', title: 'Ngày không tồn tại', hint: 'Ngày 30/02/2026 có tồn tại không?', fields: base({ sale_date: '2026-02-30' }), compare: [LOOSE, STRICT] },
  { id: 'missing_not_null', title: 'Thiếu cột NOT NULL', hint: 'Không gửi cột note (NOT NULL, không có DEFAULT).', fields: base(), omit: ['note'], compare: [LOOSE, STRICT] },
  { id: 'combo', title: 'Combo 5 lỗi', hint: 'Một câu INSERT chứa cả 5 lỗi ở trên.', fields: base({ name: 'Ban phim co khong day', price: 'abc', stock: '999', sale_date: '2026-02-30' }), omit: ['note'], compare: [LOOSE, STRICT] },
  { id: 'zero_date', title: 'Ngày 0000-00-00', hint: 'Chỉ STRICT_TRANS_TABLES có chặn được không?', fields: base({ sale_date: '0000-00-00' }), compare: [custom(['STRICT_TRANS_TABLES']), { label: 'Preset strict', mode: 'strict' }] },
  { id: 'div_zero', title: 'Chia cho 0', hint: 'Thiếu ERROR_FOR_DIVISION_BY_ZERO thì 10/0 sẽ ra gì?', fields: { divisor: '0' }, compare: [custom(['STRICT_TRANS_TABLES']), custom(['STRICT_TRANS_TABLES', 'ERROR_FOR_DIVISION_BY_ZERO'])] },
  { id: 'group_by', title: 'ONLY_FULL_GROUP_BY', hint: 'Chọn cột note không nằm trong GROUP BY.', fields: {}, fixedSql: 'SELECT name, note, SUM(stock) AS stock FROM products GROUP BY name', compare: [LOOSE, STRICT] },
  { id: 'myisam', title: 'Bảng MyISAM', hint: 'Bảng không có transaction: lỗi giữa chừng thì sao?', fields: {}, fixedSql: "TRUNCATE TABLE log_myisam;\nINSERT INTO log_myisam VALUES (1,1),(2,999),(3,3)", compare: [custom(['STRICT_TRANS_TABLES']), custom(['STRICT_ALL_TABLES'])] },
  { id: 'insert_ignore', title: 'Lối thoát INSERT IGNORE', hint: 'insertOrIgnore sinh INSERT IGNORE nên bỏ qua strict.', fields: base({ name: 'Tai nghe chong on', stock: '999' }), compare: [LOOSE, STRICT] },
];
SC.forEach((s, i) => { s.n = i + 1; });
const GROUPS = [
  { name: 'Khối 1 · Lỗi dữ liệu', items: SC.slice(0, 6) },
  { name: 'Khối 2 · Mode đi kèm', items: SC.slice(6, 9) },
  { name: 'Khối 3 · Ngoại lệ', items: SC.slice(9) },
];
const quote = (v) => (v === null ? 'NULL' : `'${v}'`);

createApp({
  data() {
    return {
      MODES, ALL_MODES, GROUPS, LABELS, PCOLS, PIPE_NAMES,
      mode: 'strict', lastStd: 'strict', autoCustom: false, customModes: ['STRICT_TRANS_TABLES'], customOpen: false,
      effective: null, statusReq: 0,
      diffs: { g: null, s: null }, flash: { g: 0, s: 0 },
      sc: SC[0], form: {}, results: [], expected: 1, busy: false, k: 0, fatal: '',
      tables: { products: [], log_myisam: [] }, newIds: [], loaded: false,
    };
  },
  computed: {
    barBg() { return this.effective ? (this.effective.is_strict ? '#E4F7EE' : '#FEF4DF') : '#FFFFFF'; },
    dbLabel() {
      const v = this.effective ? this.effective.version : '';
      if (!v) return '...';
      return (/mariadb/i.test(v) ? 'MariaDB ' : 'MySQL ') + v.split('-')[0];
    },
    pending() { return this.busy ? Math.max(0, this.expected - this.results.length) : 0; },
    suspiciousCount() { return this.tables.products.filter((p) => this.bugs(p).length).length; },
    cfgText() {
      if (!this.effective) return '';
      const c = this.effective.connection;
      return `// config/database.php\n'${c}' => [\n    'driver' => '${c}',\n    ...\n    ${this.effective.config_snippet}\n],`;
    },
    summary() {
      const [a, b] = this.results;
      return `${a.label} ${this.outcome(a)}. ${b.label} ${this.outcome(b)}.`;
    },
  },
  methods: {
    // ---------- API ----------
    async api(path, opts = {}) {
      try {
        const res = await fetch('/api/' + path, { headers: { Accept: 'application/json', 'Content-Type': 'application/json' }, ...opts });
        return await res.json();
      } catch (e) { this.fatal = 'Không gọi được API'; throw e; }
    },
    modeQuery() {
      const q = new URLSearchParams({ mode: this.mode });
      this.customModes.forEach((m) => q.append('modes[]', m));
      return q.toString();
    },
    // ---------- Trạng thái mode + chênh lệch ----------
    tok(s) { return s ? s.split(',') : []; },
    applyDiff(key, oldS, newS) {
      const o = new Set(this.tok(oldS)), n = new Set(this.tok(newS));
      const added = [...n].filter((x) => !o.has(x)), removed = [...o].filter((x) => !n.has(x));
      if (!added.length && !removed.length) return;
      this.diffs[key] = { added, removed, fade: false };
      this.flash[key]++;
      clearTimeout(this._t[key + 'f']); clearTimeout(this._t[key + 'x']);
      this._t[key + 'f'] = setTimeout(() => { if (this.diffs[key]) this.diffs[key].fade = true; }, 3300);
      this._t[key + 'x'] = setTimeout(() => { this.diffs[key] = null; }, 4000);
    },
    async refreshStatus() {
      const n = ++this.statusReq;
      let d;
      try { d = await this.api('status?' + this.modeQuery()); } catch (e) { return; }
      if (n !== this.statusReq) return; // bỏ phản hồi cũ
      if (d.global_mode === undefined) { this.fatal = d.message || 'Lỗi API'; return; }
      this.fatal = '';
      if (this.effective) {
        this.applyDiff('g', this.effective.global_mode, d.global_mode);
        this.applyDiff('s', this.effective.session_mode, d.session_mode);
      }
      this.effective = d;
    },
    setMode(m) {
      if (m !== 'custom') this.lastStd = m;
      this.mode = m;
      this.refreshStatus();
    },
    pickMode(m) { this.autoCustom = false; this.setMode(m); },
    pickCustom() { this.autoCustom = false; this.setMode('custom'); },
    // ---------- Dữ liệu ----------
    async loadProducts(highlight = true) {
      const t = await this.api('products');
      const old = new Set(this.tables.products.map((p) => p.id));
      this.newIds = highlight && this.loaded ? t.products.filter((p) => !old.has(p.id)).map((p) => p.id) : [];
      this.tables = t; this.loaded = true;
    },
    async reset() {
      this.tables = await this.api('reset', { method: 'POST' });
      this.newIds = []; this.results = [];
    },
    // ---------- Kịch bản ----------
    select(s) {
      this.sc = s; this.results = [];
      this.form = {};
      for (const [k, v] of Object.entries(s.fields)) this.form[k] = { value: v, send: !(s.omit || []).includes(k) };
      const c = s.compare[0];
      if (c.mode === 'custom') { // kịch bản cần mode tùy chỉnh: tự áp preset của nó
        if (this.mode !== 'custom') this.lastStd = this.mode;
        this.customModes = [...c.modes]; this.autoCustom = true; this.setMode('custom');
      } else if (this.autoCustom) {
        this.autoCustom = false; this.setMode(this.lastStd);
      }
    },
    payload() {
      const p = {};
      for (const [k, f] of Object.entries(this.form)) if (f.send) p[k] = f.value;
      return p;
    },
    async exec(cfg) {
      const data = await this.api('run', { method: 'POST', body: JSON.stringify({ scenario: this.sc.id, mode: cfg.mode, modes: cfg.modes || [], payload: this.payload() }) });
      this.results.push({ key: ++this.k, label: cfg.label, sc: this.sc.id, data });
      await this.loadProducts();
    },
    async go(list) { // chạy tuần tự, không song song
      if (this.busy) return;
      this.busy = true; this.results = []; this.expected = list.length;
      try { for (const cfg of list) await this.exec(cfg); } catch (e) { /* fatal đã hiển thị */ }
      this.busy = false;
    },
    run() {
      const m = MODES.find((x) => x.id === this.mode);
      return this.go([{ label: m ? m.title : this.customModes.join(' + ') || '(không mode)', mode: this.mode, modes: this.customModes }]);
    },
    compare() { return this.go(this.sc.compare); },
    // ---------- Diễn giải kết quả ----------
    changed(r, k) {
      const s = r.data.stored;
      if (!Object.keys(s).length) return false;
      return k in r.data.sent ? String(r.data.sent[k]) !== String(s[k]) : s[k] !== null;
    },
    fieldKeys(r) { return PCOLS.filter((k) => k !== 'id' && (k in r.data.sent || k in r.data.stored)); },
    changedKeys(r) { return this.fieldKeys(r).filter((k) => this.changed(r, k)); },
    verdict(r) {
      const d = r.data, n = this.changedKeys(r).length, w = d.warnings.length;
      if (d.error) {
        const err = `ERROR ${d.error.code} (${d.error.sqlstate}): ${d.error.message}`;
        if (r.sc === 'myisam' && d.rows && d.rows.length) return { tone: 'amber', text: 'Lỗi giữa chừng: MyISAM không rollback, các dòng trước đó đã nằm trong bảng.', err };
        return { tone: 'mint', text: r.sc === 'group_by' ? 'Đã chặn: truy vấn bị từ chối, không có kết quả mơ hồ.' : 'Đã chặn: không có gì được ghi vào bảng.', err };
      }
      if (r.sc === 'group_by') return { tone: 'amber', text: 'Truy vấn vẫn chạy: cột note lấy từ một dòng bất kỳ trong nhóm, kết quả không xác định.' };
      if (n || w) {
        const parts = [];
        if (n) parts.push(`${n} ô thay đổi`);
        if (w) parts.push(`${w} warning`);
        return { tone: 'amber', text: `Dữ liệu bị sửa âm thầm: ${parts.join(', ')}, Laravel không hay biết.` };
      }
      if (r.sc === 'zero_date') return { tone: 'amber', text: 'Ghi thành công, nhưng ngày 0000-00-00 không hợp lệ vẫn lọt vào bảng.' };
      return { tone: 'mint', text: 'Ghi thành công, dữ liệu khớp những gì đã gửi.' };
    },
    face(r) {
      const v = this.verdict(r);
      return v.tone === 'amber' ? '(⊙_⊙;)' : v.err ? '(•̀ᴗ•́)و' : '(＾▽＾)';
    },
    outcome(r) {
      const d = r.data;
      if (d.error) {
        if (r.sc === 'myisam' && d.rows.length) return `báo lỗi ${d.error.code} nhưng đã ghi dở ${d.rows.map((x) => `(${x.id},${x.level})`).join(' ')}`;
        return `từ chối (ERROR ${d.error.code}) và không ghi gì`;
      }
      if (r.sc === 'myisam') return `ghi ${d.rows.map((x) => `(${x.id},${x.level})`).join(' ')}`;
      if (r.sc === 'group_by') return `vẫn chạy, trả ${d.rows.length} dòng`;
      const ks = this.changedKeys(r);
      if (ks.length === 1) return `lưu ${ks[0]} = ${quote(d.stored[ks[0]])}`;
      if (ks.length > 1) return `sửa âm thầm ${ks.length} ô`;
      if (d.warnings.length) return `ghi kèm ${d.warnings.length} warning`;
      return 'ghi đúng dữ liệu đã gửi';
    },
    pipe(r) {
      const d = r.data;
      if (d.error) return [`ERROR ${d.error.code}`, 'PDOException', 'QueryException', 'HTTP 500'].map((text) => ({ text, tone: 'mint' }));
      if (d.warnings.length) return [`Warning ${d.warnings[0].code}`, 'không báo gì', 'không báo gì', 'chạy tiếp với dữ liệu sai'].map((text) => ({ text, tone: 'amber' }));
      return ['OK', 'OK', 'OK', 'OK'].map((text) => ({ text, tone: 'mint' }));
    },
    cols(rows) { return rows.length ? Object.keys(rows[0]) : []; },
    fmt(v, sent) { return v === undefined ? (sent ? '(không gửi)' : '') : v === null ? 'NULL' : v === '' ? "''" : v; },
    bugs(p) {
      const b = [];
      if (Number(p.price) === 0) b.push('price=0');
      if (p.sale_date === '0000-00-00') b.push('ngày 0000');
      if (Number(p.stock) === 127) b.push('stock=127');
      if ((p.name || '').length === 10) b.push('name bị cắt');
      return b;
    },
  },
  created() { this._t = {}; },
  mounted() {
    this.select(SC[0]);
    this.refreshStatus();
    this.loadProducts();
    // Tự phát hiện thay đổi GLOBAL từ HeidiSQL (tạm dừng khi tab ẩn)
    setInterval(() => { if (!document.hidden) this.refreshStatus(); }, 2000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) this.refreshStatus(); });
    // Chiều cao thanh mode để cột trái sticky đúng vị trí
    new ResizeObserver(() => document.documentElement.style.setProperty('--bar-h', this.$refs.bar.offsetHeight + 'px')).observe(this.$refs.bar);
    // Phím tắt cho người trình bày
    window.addEventListener('keydown', (e) => {
      if (e.ctrlKey || e.metaKey || e.altKey || /INPUT|TEXTAREA|SELECT/.test(e.target.tagName)) return;
      const i = SC.indexOf(this.sc);
      if (/^[1-9]$/.test(e.key)) this.select(SC[+e.key - 1]);
      else if (e.key === 'ArrowDown') { e.preventDefault(); this.select(SC[Math.min(i + 1, SC.length - 1)]); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); this.select(SC[Math.max(i - 1, 0)]); }
      else if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') this.run();
      else if (e.key.toLowerCase() === 'c') this.compare();
      else if (e.key.toLowerCase() === 'r') this.reset();
    });
  },
}).mount('#app');
</script>
@endverbatim
</body>
</html>
