<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Strict Mode Playground</title>
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<style>
  /* ===== Apple Human Interface Guidelines Design System ===== */
  :root {
    --s1: 4px;
    --s2: 8px;
    --s3: 12px;
    --s4: 16px;
    --s5: 20px;
    --s6: 24px;
    --s8: 32px;
    --s10: 40px;
    --s12: 48px;

    --r-xs: 6px;
    --r-sm: 8px;
    --r-md: 10px;
    --r-lg: 14px;
    --r-xl: 18px;
    --r-pill: 9999px;

    /* Palette */
    --bg: #F5F5F7;
    --surface: #FFFFFF;
    --surface-subtle: #FAFAFC;
    --surface-secondary: #F2F2F7;
    --surface-glass: rgba(255, 255, 255, 0.78);

    --ink: #1D1D1F;
    --ink-secondary: #515154;
    --mute: #86868B;

    --line: rgba(0, 0, 0, 0.08);
    --line-strong: rgba(0, 0, 0, 0.14);

    /* Primary Accent (Apple Blue) */
    --accent: #0071E3;
    --accent-hover: #0077ED;
    --accent-active: #0068D1;
    --accent-bg: rgba(0, 113, 227, 0.1);
    --accent-ring: rgba(0, 113, 227, 0.28);

    /* Semantic: Mint / Success (Strict Guarded / Clean) */
    --mint: #EAF8EF;
    --mint-line: rgba(52, 199, 89, 0.35);
    --mint-strong: rgba(52, 199, 89, 0.16);
    --mint-ink: #15682E;
    --mint-dot: #34C759;

    /* Semantic: Amber / Warning (Silent Corruption / Loose) */
    --amber: #FFF8EB;
    --amber-line: rgba(245, 158, 11, 0.38);
    --amber-strong: rgba(245, 158, 11, 0.16);
    --amber-ink: #92540A;
    --amber-dot: #FF9F0A;

    /* Semantic: Error / Danger */
    --err: #D70015;
    --err-bg: #FFF2F4;
    --err-line: rgba(215, 0, 21, 0.25);

    /* Shadows */
    --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.03);
    --shadow-lg: 0 16px 40px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.04);

    /* Typography */
    --font-sans: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    --font-mono: "SF Mono", ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;

    --bar-h: 180px;
    --ease: cubic-bezier(0.25, 0.1, 0.25, 1);
  }

  *, *::before, *::after { box-sizing: border-box; }
  [v-cloak] { display: none; }
  html { font-size: 16px; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
  body {
    margin: 0;
    background: var(--bg);
    color: var(--ink);
    font-family: var(--font-sans);
    font-size: 15px;
    line-height: 1.5;
    letter-spacing: -0.01em;
  }

  .mono { font-family: var(--font-mono); font-size: 0.92em; }
  .cap { font-size: 13px; color: var(--mute); font-weight: 400; }
  .h1 { font-size: 22px; font-weight: 600; line-height: 1.25; letter-spacing: -0.022em; margin: 0; color: var(--ink); }
  .h2 { font-size: 18px; font-weight: 600; line-height: 1.3; letter-spacing: -0.018em; margin: 0; color: var(--ink); }

  .wrap { max-width: 1400px; margin: 0 auto; padding-left: var(--s6); padding-right: var(--s6); }
  .row { display: flex; align-items: center; gap: var(--s4); flex-wrap: wrap; }
  .grow { flex: 1; min-width: 0; }

  button {
    cursor: pointer;
    font: inherit;
    border: 1px solid transparent;
    background: none;
    color: inherit;
    border-radius: var(--r-sm);
    transition: background-color 180ms var(--ease), border-color 180ms var(--ease), color 180ms var(--ease), box-shadow 180ms var(--ease), opacity 180ms var(--ease);
  }
  button:disabled { opacity: 0.45; cursor: not-allowed; }

  /* ===== Sticky Translucent Top Bar (macOS / iCloud Header) ===== */
  .modebar {
    position: sticky;
    top: 0;
    z-index: 30;
    background: var(--surface-glass);
    backdrop-filter: saturate(180%) blur(20px);
    -webkit-backdrop-filter: saturate(180%) blur(20px);
    border-bottom: 1px solid var(--line);
    transition: background-color 220ms var(--ease);
  }
  .modebar .wrap {
    position: relative;
    padding-top: var(--s4);
    padding-bottom: var(--s4);
    display: flex;
    flex-direction: column;
    gap: var(--s3);
  }
  .topbar-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--s4);
  }
  .brand-group {
    display: flex;
    align-items: baseline;
    gap: var(--s3);
    flex-wrap: wrap;
  }
  .db-pill {
    display: inline-flex;
    align-items: center;
    padding: 2px 10px;
    border-radius: var(--r-pill);
    background: var(--surface-secondary);
    border: 1px solid var(--line);
    font-size: 12px;
    font-weight: 500;
    color: var(--ink-secondary);
  }
  .toolbar-row {
    display: flex;
    align-items: center;
    gap: var(--s3);
    flex-wrap: wrap;
  }
  .lbl {
    width: 76px;
    flex: none;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.04em;
    color: var(--mute);
    text-transform: uppercase;
  }

  /* macOS Segmented Control */
  .seg-wrap {
    display: inline-flex;
    gap: 2px;
    padding: 3px;
    border-radius: var(--r-md);
    background: rgba(118, 118, 128, 0.12);
    flex-wrap: wrap;
  }
  .seg {
    display: flex;
    align-items: baseline;
    gap: var(--s2);
    padding: 6px 14px;
    border-radius: 7px;
    font-size: 13px;
    font-weight: 500;
    color: var(--ink-secondary);
    line-height: 1.25;
  }
  .seg small { font-size: 11.5px; color: var(--mute); font-weight: 400; }
  .seg:hover:not(.on) { color: var(--ink); background: rgba(255, 255, 255, 0.45); }
  .seg.on {
    background: var(--surface);
    color: var(--ink);
    font-weight: 600;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 1px rgba(0, 0, 0, 0.06);
  }
  .seg.on small { color: var(--accent); }

  .adv {
    padding: 6px 14px;
    background: var(--surface);
    border: 1px solid var(--line-strong);
    border-radius: var(--r-sm);
    font-size: 13px;
    font-weight: 500;
    color: var(--ink);
    box-shadow: var(--shadow-sm);
  }
  .adv:hover { background: var(--surface-subtle); }
  .adv.on {
    background: var(--accent-bg);
    border-color: var(--accent);
    color: var(--accent);
    font-weight: 600;
  }

  /* SQL Mode Inspector Strip */
  .mode-stack {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--s2);
  }
  @media (max-width: 960px) {
    .mode-stack { grid-template-columns: 1fr; }
  }
  .modeline {
    min-height: 36px;
    padding: 6px 12px;
    background: rgba(255, 255, 255, 0.72);
    border: 1px solid var(--line);
    border-radius: var(--r-sm);
    display: flex;
    align-items: center;
    gap: var(--s2);
  }
  .modeline .mono { font-size: 12.5px; overflow-wrap: anywhere; line-height: 1.45; }
  .tk { color: var(--ink-secondary); }
  .tk-strict {
    background: var(--mint-strong);
    color: var(--mint-ink);
    border-radius: 4px;
    padding: 1px 6px;
    font-weight: 600;
  }
  .diff { margin-left: var(--s2); white-space: normal; transition: opacity 250ms var(--ease); }
  .diff.fade { opacity: 0; }
  .plus, .minus {
    font: 600 11.5px var(--font-mono);
    border-radius: 4px;
    padding: 1px 6px;
    margin-right: var(--s1);
    display: inline-block;
  }
  .plus { color: var(--mint-ink); background: var(--mint); border: 1px solid var(--mint-line); }
  .minus { color: var(--err); background: var(--err-bg); border: 1px solid var(--err-line); text-decoration: line-through; }
  .live {
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: var(--r-pill);
    background: var(--mint-dot);
    margin-left: 4px;
    vertical-align: middle;
  }
  .badge {
    font-weight: 600;
    font-size: 11.5px;
    letter-spacing: 0.03em;
    padding: 3px 10px;
    border-radius: var(--r-pill);
    white-space: nowrap;
    border: 1px solid;
  }
  .badge.on { background: var(--mint); color: var(--mint-ink); border-color: var(--mint-line); }
  .badge.off { background: var(--amber); color: var(--amber-ink); border-color: var(--amber-line); }

  /* Popover Drawer for Custom Session Modes */
  .drawer {
    position: absolute;
    left: var(--s6);
    right: var(--s6);
    top: calc(100% + 6px);
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: saturate(180%) blur(24px);
    -webkit-backdrop-filter: saturate(180%) blur(24px);
    border: 1px solid var(--line-strong);
    border-radius: var(--r-lg);
    padding: var(--s6);
    box-shadow: var(--shadow-lg);
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--s8);
    z-index: 40;
  }
  @media (max-width: 768px) {
    .drawer { grid-template-columns: 1fr; gap: var(--s4); }
  }
  .swrow {
    display: flex;
    align-items: center;
    gap: var(--s3);
    padding: 6px 0;
    cursor: pointer;
    font-size: 13.5px;
  }
  .code {
    background: var(--surface-secondary);
    border: 1px solid var(--line);
    border-radius: var(--r-md);
    padding: var(--s4);
    font-size: 13px;
    line-height: 1.55;
    color: var(--ink);
    white-space: pre-wrap;
    overflow-wrap: anywhere;
    margin-top: var(--s2);
  }

  /* ===== Apple iOS / macOS Toggle Switch ===== */
  input.sw {
    appearance: none;
    -webkit-appearance: none;
    width: 36px;
    height: 20px;
    border-radius: var(--r-pill);
    background: rgba(120, 120, 128, 0.24);
    border: none;
    position: relative;
    flex: none;
    cursor: pointer;
    outline: none;
    transition: background-color 200ms var(--ease);
  }
  input.sw::after {
    content: "";
    position: absolute;
    top: 2px;
    left: 2px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #FFFFFF;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
    transition: transform 200ms var(--ease);
  }
  input.sw:checked { background: var(--accent); }
  input.sw:checked::after { transform: translateX(16px); }
  input.sw:focus-visible { box-shadow: 0 0 0 3px var(--accent-ring); }

  /* ===== Main Workspace Layout ===== */
  .layout {
    display: block;
    padding-top: var(--s6);
    padding-bottom: var(--s12);
  }

  /* Sidebar Navigation (macOS Settings style) */
  .scn-col {
    display: flex;
    gap: var(--s4);
    overflow-x: auto;
    margin-bottom: var(--s6);
    padding-bottom: var(--s2);
  }
  .grp {
    flex: none;
    display: flex;
    flex-direction: column;
    gap: 2px;
  }
  .grp-name {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--mute);
    padding: var(--s1) var(--s3);
    white-space: nowrap;
  }
  .scn {
    display: flex;
    gap: var(--s3);
    align-items: center;
    width: 100%;
    text-align: left;
    padding: 8px 12px;
    border-radius: var(--r-sm);
    white-space: nowrap;
    background: transparent;
    border: 1px solid transparent;
    font-size: 14px;
    font-weight: 500;
    color: var(--ink-secondary);
  }
  .scn .n {
    width: 22px;
    height: 22px;
    line-height: 22px;
    font-size: 12px;
    text-align: center;
    border-radius: var(--r-xs);
    background: rgba(0, 0, 0, 0.05);
    color: var(--mute);
    font-weight: 600;
    flex: none;
    transition: background-color 180ms var(--ease), color 180ms var(--ease);
  }
  .scn:hover:not(.on) {
    background: rgba(0, 0, 0, 0.04);
    color: var(--ink);
  }
  .scn.on {
    background: var(--accent);
    color: #FFFFFF;
    font-weight: 500;
    box-shadow: 0 1px 3px rgba(0, 113, 227, 0.25);
  }
  .scn.on .n {
    background: rgba(255, 255, 255, 0.22);
    color: #FFFFFF;
  }

  .stage {
    display: flex;
    flex-direction: column;
    gap: var(--s6);
    min-width: 0;
  }

  @media (min-width: 1024px) {
    .layout {
      display: grid;
      grid-template-columns: 260px minmax(0, 1fr);
      gap: var(--s8);
      align-items: start;
    }
    .scn-col {
      flex-direction: column;
      gap: var(--s5);
      overflow: visible;
      margin: 0;
      padding: var(--s3);
      background: rgba(255, 255, 255, 0.6);
      border: 1px solid var(--line);
      border-radius: var(--r-lg);
      position: sticky;
      top: calc(var(--bar-h) + var(--s4));
      max-height: calc(100vh - var(--bar-h) - var(--s6));
      overflow-y: auto;
    }
  }

  /* ===== Cards & Forms ===== */
  .card {
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--r-lg);
    padding: var(--s6);
    box-shadow: var(--shadow-md);
  }
  .card-header {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .fields {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: var(--s4) var(--s6);
    margin-top: var(--s5);
  }
  @media (max-width: 640px) {
    .fields { grid-template-columns: 1fr; }
  }
  .field {
    display: flex;
    flex-direction: column;
    gap: 6px;
    transition: opacity 180ms var(--ease);
  }
  .field.off { opacity: 0.45; }
  .field-head {
    display: flex;
    align-items: center;
    gap: var(--s2);
  }
  .field-head label {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    color: var(--mute);
    cursor: pointer;
    user-select: none;
  }
  .inp {
    width: 100%;
    background: var(--surface-subtle);
    border: 1px solid var(--line-strong);
    border-radius: var(--r-sm);
    padding: 9px 12px;
    font: 14px var(--font-mono);
    color: var(--ink);
    transition: border-color 180ms var(--ease), box-shadow 180ms var(--ease), background-color 180ms var(--ease);
  }
  .inp:focus {
    outline: none;
    background: var(--surface);
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-ring);
  }
  .inp:disabled {
    text-decoration: line-through;
    background: var(--surface-secondary);
    color: var(--mute);
  }

  /* Buttons */
  .actions {
    display: flex;
    align-items: center;
    gap: var(--s3);
    margin-top: var(--s6);
    padding-top: var(--s4);
    border-top: 1px solid var(--line);
    flex-wrap: wrap;
  }
  .btn-main {
    background: var(--accent);
    color: #FFFFFF;
    border-color: transparent;
    border-radius: var(--r-md);
    font-size: 14px;
    font-weight: 500;
    padding: 9px 20px;
    box-shadow: 0 1px 2px rgba(0, 113, 227, 0.2);
  }
  .btn-main:hover:not(:disabled) { background: var(--accent-hover); }
  .btn-main:active:not(:disabled) { background: var(--accent-active); }

  .btn-soft {
    background: var(--surface);
    color: var(--ink);
    border-color: var(--line-strong);
    border-radius: var(--r-md);
    font-size: 14px;
    font-weight: 500;
    padding: 9px 18px;
    box-shadow: var(--shadow-sm);
  }
  .btn-soft:hover:not(:disabled) { background: var(--surface-subtle); }

  .btn-quiet {
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 500;
    background: var(--surface);
    border-color: var(--line-strong);
    border-radius: var(--r-sm);
    color: var(--ink-secondary);
    box-shadow: var(--shadow-sm);
  }
  .btn-quiet:hover:not(:disabled) { background: var(--surface-subtle); color: var(--ink); }

  .btn-main kbd, .btn-soft kbd {
    font-family: var(--font-sans);
    font-size: 12px;
    font-weight: 400;
    opacity: 0.75;
    margin-left: 4px;
  }

  /* ===== Results & Comparison Stage ===== */
  .summary {
    font-size: 16px;
    font-weight: 600;
    color: var(--ink);
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--r-md);
    padding: var(--s3) var(--s4);
    box-shadow: var(--shadow-sm);
  }
  .res-grid {
    display: grid;
    gap: var(--s6);
    grid-template-columns: minmax(0, 1fr);
  }
  .res-grid.two {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  @media (max-width: 900px) {
    .res-grid.two { grid-template-columns: 1fr; }
  }
  .res {
    display: flex;
    flex-direction: column;
    gap: var(--s4);
  }
  .res-head {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: var(--s3);
    padding-bottom: var(--s3);
    border-bottom: 1px solid var(--line);
  }

  .banner {
    border: 1px solid;
    border-radius: var(--r-md);
    padding: var(--s3) var(--s4);
    font-size: 14.5px;
    font-weight: 500;
    line-height: 1.45;
  }
  .banner.amber {
    background: var(--amber);
    color: var(--amber-ink);
    border-color: var(--amber-line);
  }
  .banner.mint {
    background: var(--mint);
    color: var(--mint-ink);
    border-color: var(--mint-line);
  }
  .errline {
    margin-top: 6px;
    padding-top: 6px;
    border-top: 1px solid rgba(0, 0, 0, 0.06);
    color: var(--err);
    font: 500 12.5px var(--font-mono);
    overflow-wrap: anywhere;
  }

  /* Clean Apple Data Tables */
  .tbl-wrap {
    border: 1px solid var(--line);
    border-radius: var(--r-md);
    overflow: hidden;
    background: var(--surface);
  }
  .tbl {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
  }
  .tbl th {
    font-size: 11.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--mute);
    padding: 9px 12px;
    background: var(--surface-subtle);
    border-bottom: 1px solid var(--line);
  }
  .tbl td {
    padding: 9px 12px;
    font: 13.5px var(--font-mono);
    color: var(--ink);
    border-top: 1px solid var(--line);
  }
  .tbl tbody tr:first-child td { border-top: none; }
  .tbl tbody tr:hover td { background: rgba(0, 0, 0, 0.015); }
  .tbl tr.chg td, .tbl tr.sus td, .tbl td.chg {
    background: var(--amber) !important;
  }
  .tbl .sent-chg { text-decoration: line-through; color: var(--mute); }
  .tbl .stored-chg { font-weight: 600; color: var(--amber-ink); }

  /* Pipeline Indicator */
  .pipe {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px 10px;
    font-size: 13px;
    padding: 8px 12px;
    background: var(--surface-subtle);
    border: 1px solid var(--line);
    border-radius: var(--r-sm);
  }
  .pipe .step { display: inline-flex; align-items: center; gap: 6px; }
  .pipe .dot { width: 7px; height: 7px; border-radius: 50%; }
  .dot.mint { background: var(--mint-dot); }
  .dot.amber { background: var(--amber-dot); }

  /* 4-Layer Detailed Breakdown */
  .layer-list {
    display: flex;
    flex-direction: column;
    gap: var(--s2);
  }
  .layer-item {
    border: 1px solid var(--line);
    border-radius: var(--r-sm);
    padding: 10px 12px;
    background: var(--surface-subtle);
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .layer-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--s2);
    font-size: 13px;
  }
  .layer-title {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 600;
    color: var(--ink);
  }
  .layer-badge {
    font-family: var(--font-mono);
    font-size: 11.5px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: var(--r-pill);
  }
  .layer-badge.mint { background: var(--mint); color: var(--mint-ink); border: 1px solid var(--mint-line); }
  .layer-badge.amber { background: var(--amber); color: var(--amber-ink); border: 1px solid var(--amber-line); }
  .layer-desc {
    font-size: 13px;
    color: var(--ink-secondary);
    line-height: 1.45;
  }
  .layer-code {
    margin: 2px 0 0;
    padding: 6px 10px;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: var(--r-xs);
    font: 12px/1.45 var(--font-mono);
    color: var(--ink);
    white-space: pre-wrap;
    overflow-wrap: anywhere;
  }
  .layer-code.err {
    color: var(--err);
    background: var(--err-bg);
    border-color: var(--err-line);
  }

  /* Disclosure Details */
  details {
    border: 1px solid var(--line);
    border-radius: var(--r-sm);
    margin-top: 6px;
    background: var(--surface-subtle);
    overflow: hidden;
  }
  details summary {
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    color: var(--ink-secondary);
    list-style: none;
    padding: 8px 12px;
    user-select: none;
    transition: background-color 150ms var(--ease);
  }
  details summary:hover { background: rgba(0, 0, 0, 0.03); color: var(--ink); }
  details summary::-webkit-details-marker { display: none; }
  details summary::before { content: "▸ "; color: var(--mute); }
  details[open] summary::before { content: "▾ "; }
  details[open] summary { border-bottom: 1px solid var(--line); background: var(--surface); }
  details .body { padding: var(--s3); background: var(--surface); }

  .empty {
    padding: var(--s10) var(--s6);
    text-align: center;
    font-size: 14.5px;
    color: var(--mute);
    border: 1px solid var(--line);
    border-radius: var(--r-lg);
    background: var(--surface);
    box-shadow: var(--shadow-sm);
  }
  .sk { background: var(--surface-secondary); border-radius: var(--r-sm); }

  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { transition: none !important; }
  }
</style>
</head>
<body>
@verbatim
<div id="app" v-cloak>

  <!-- ============ THANH MODE (sticky macOS Translucent Header) ============ -->
  <header class="modebar" ref="bar">
    <div class="wrap">
      <div class="topbar-head">
        <div class="brand-group">
          <h1 class="h1">Strict Mode Playground</h1>
          <span class="db-pill mono">{{ dbLabel }}</span>
        </div>
        <button class="btn-quiet" @click="reset">Reset (R)</button>
      </div>

      <div class="toolbar-row">
        <span class="lbl">Laravel</span>
        <div class="seg-wrap">
          <button v-for="m in MODES" :key="m.id" class="seg" :class="{ on: mode === m.id }" @click="pickMode(m.id)">
            <span>{{ m.title }}</span><small class="mono">{{ m.sub }}</small>
          </button>
        </div>
        <button class="adv" :class="{ on: customOpen || mode === 'custom' }" @click="customOpen = !customOpen">Nâng cao ▾</button>
        <span v-if="fatal" class="cap" style="color:var(--err);font-weight:500">{{ fatal }}</span>
      </div>

      <div class="mode-stack" v-if="effective">
        <div class="modeline">
          <span class="lbl">Global <i class="live" title="tự cập nhật mỗi 2 giây"></i></span>
          <div class="grow">
            <span class="mono">
              <template v-for="(t, i) in tok(effective.global_mode)" :key="t"><span v-if="i">, </span><span :class="t.startsWith('STRICT_') ? 'tk-strict' : 'tk'">{{ t }}</span></template>
            </span>
            <span v-if="diffs.g" class="diff" :class="{ fade: diffs.g.fade }">
              <span v-for="a in diffs.g.added" :key="'a' + a" class="plus">+{{ a }}</span>
              <span v-for="m in diffs.g.removed" :key="'r' + m" class="minus">−{{ m }}</span>
            </span>
          </div>
        </div>
        <div class="modeline">
          <span class="lbl">Session</span>
          <div class="grow">
            <span class="mono">
              <template v-for="(t, i) in tok(effective.session_mode)" :key="t"><span v-if="i">, </span><span :class="t.startsWith('STRICT_') ? 'tk-strict' : 'tk'">{{ t }}</span></template>
            </span>
            <span v-if="diffs.s" class="diff" :class="{ fade: diffs.s.fade }">
              <span v-for="a in diffs.s.added" :key="'a' + a" class="plus">+{{ a }}</span>
              <span v-for="m in diffs.s.removed" :key="'r' + m" class="minus">−{{ m }}</span>
            </span>
          </div>
          <span class="badge" :class="effective.is_strict ? 'on' : 'off'">{{ effective.is_strict ? 'STRICT ON' : 'STRICT OFF' }}</span>
        </div>
      </div>

      <div v-if="customOpen" class="drawer">
        <div>
          <div class="cap" style="font-weight:600;margin-bottom:var(--s2)">Mode tự chọn (áp lên SESSION)</div>
          <label v-for="m in ALL_MODES" :key="m" class="swrow">
            <input type="checkbox" class="sw" :value="m" v-model="customModes" @change="pickCustom">
            <span class="mono">{{ m }}</span>
          </label>
        </div>
        <div>
          <div class="cap" style="font-weight:600;margin-bottom:var(--s2)">Config đang áp dụng</div>
          <pre class="code mono">{{ cfgText }}</pre>
        </div>
      </div>
    </div>
  </header>

  <main class="wrap layout">
    <!-- ============ DANH SÁCH KỊCH BẢN (Sidebar) ============ -->
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
        <div class="card-header">
          <h2 class="h2">{{ sc.title }}</h2>
          <div class="cap" style="font-size:14px">{{ sc.hint }}</div>
        </div>

        <div v-if="Object.keys(form).length" class="fields">
          <div v-for="(f, k) in form" :key="k" class="field" :class="{ off: !f.send }">
            <div class="field-head">
              <span class="mono" style="font-weight:600;color:var(--ink)">{{ LABELS[k][0] }}</span>
              <span class="cap mono">{{ LABELS[k][1] }}</span>
              <label>gửi <input type="checkbox" class="sw" v-model="f.send"></label>
            </div>
            <input class="inp" v-model="f.value" :disabled="!f.send">
          </div>
        </div>
        <pre v-else class="code mono" style="font-size:13.5px;margin-top:var(--s5)">{{ sc.fixedSql }}</pre>

        <div class="actions">
          <button class="btn-main" :disabled="busy" @click="run">Chạy <kbd>(Enter)</kbd></button>
          <button class="btn-soft" :disabled="busy" @click="compare">So sánh 2 chế độ <kbd>(C)</kbd></button>
          <span class="cap">{{ sc.compare[0].label }} và {{ sc.compare[1].label }}</span>
        </div>
      </section>

      <section>
        <div v-if="!results.length && !busy" class="empty">Chọn kịch bản rồi bấm Chạy để kiểm tra hành vi của cơ sở dữ liệu</div>
        <div v-if="results.length === 2 && !busy" class="summary" style="margin-bottom:var(--s5)">{{ summary }}</div>

        <div v-if="results.length || busy" class="res-grid" :class="{ two: expected > 1 }">
          <div v-for="r in results" :key="r.key" class="card res">
            <div class="res-head">
              <div class="h2">{{ r.label }}</div>
              <div class="cap mono">{{ r.data.effective.is_strict ? 'STRICT ON' : 'STRICT OFF' }} · {{ r.data.elapsed_ms }} ms</div>
            </div>

            <div class="banner" :class="verdict(r).tone">
              {{ verdict(r).text }}
              <div v-if="verdict(r).err" class="errline">{{ verdict(r).err }}</div>
            </div>

            <div v-if="r.data.error" class="cap" style="font-size:13.5px">Không có bản ghi nào được lưu vào bảng.</div>
            <div v-else-if="r.data.rows" class="tbl-wrap" style="overflow-x:auto">
              <table class="tbl">
                <thead><tr><th v-for="c in cols(r.data.rows)" :key="c">{{ c }}</th></tr></thead>
                <tbody>
                  <tr v-for="(row, i) in r.data.rows" :key="i">
                    <td v-for="c in cols(r.data.rows)" :key="c" :class="{ chg: r.sc === 'myisam' && c === 'level' && Number(row[c]) === 127 }">{{ fmt(row[c]) }}</td>
                  </tr>
                </tbody>
              </table>
              <div v-if="!r.data.rows.length" class="cap" style="padding:var(--s3)">(bảng trống)</div>
            </div>
            <div v-else class="tbl-wrap">
              <table class="tbl">
                <thead><tr><th>Cột</th><th>Gửi vào</th><th>Lưu thành</th></tr></thead>
                <tbody>
                  <tr v-for="k in fieldKeys(r)" :key="k" :class="{ chg: changed(r, k) }">
                    <td>{{ k }}</td>
                    <td :class="{ 'sent-chg': changed(r, k) }">{{ fmt(r.data.sent[k], true) }}</td>
                    <td :class="{ 'stored-chg': changed(r, k) }">{{ fmt(r.data.stored[k]) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="pipe">
              <template v-for="(p, i) in pipe(r)" :key="i">
                <span v-if="i" class="cap">→</span>
                <span class="step"><i class="dot" :class="p.tone"></i><span class="cap">{{ PIPE_NAMES[i] }}:</span><span style="font-weight:500">{{ p.text }}</span></span>
              </template>
            </div>

            <div class="layer-list">
              <div v-for="(l, i) in layers(r)" :key="i" class="layer-item">
                <div class="layer-head">
                  <span class="layer-title">
                    <i class="dot" :class="l.tone" style="width:7px;height:7px;border-radius:50%;display:inline-block"></i>
                    {{ i + 1 }}. {{ l.name }}
                  </span>
                  <span class="layer-badge" :class="l.tone">{{ l.badge }}</span>
                </div>
                <div class="layer-desc">{{ l.desc }}</div>
                <pre v-if="l.detail" class="layer-code" :class="{ err: l.isErr }">{{ l.detail }}</pre>
              </div>
            </div>

            <div>
              <details>
                <summary>SQL đã chạy</summary>
                <div class="body"><pre class="code mono" style="margin:0">{{ r.data.sql }}</pre></div>
              </details>
              <details>
                <summary>SHOW WARNINGS ({{ r.data.warnings.length }}) · Laravel không tự chạy bước này</summary>
                <div class="body">
                  <div v-if="r.data.warnings.length" class="tbl-wrap">
                    <table class="tbl">
                      <tr v-for="(w, i) in r.data.warnings" :key="i"><td>{{ w.level }}</td><td>{{ w.code }}</td><td style="font-family:var(--font-sans)">{{ w.message }}</td></tr>
                    </table>
                  </div>
                  <div v-else class="cap">Không có warning.</div>
                </div>
              </details>
            </div>
          </div>

          <div v-for="n in pending" :key="'sk' + n" class="card res">
            <div class="sk" style="height:24px;width:40%"></div>
            <div class="sk" style="height:56px"></div>
            <div class="sk" style="height:110px"></div>
          </div>
        </div>
      </section>

      <section class="card">
        <div class="row" style="align-items:baseline;justify-content:space-between">
          <h2 class="h2">Bảng products</h2>
          <span class="cap">{{ tables.products.length }} dòng · {{ suspiciousCount }} đáng ngờ</span>
        </div>
        <div class="tbl-wrap" style="overflow-x:auto;margin-top:var(--s4)">
          <table class="tbl">
            <thead><tr><th v-for="c in PCOLS" :key="c">{{ c }}</th><th>Ghi chú kiểm tra</th></tr></thead>
            <tbody>
              <tr v-for="p in tables.products" :key="p.id" :class="{ sus: bugs(p).length }">
                <td v-for="c in PCOLS" :key="c">{{ fmt(p[c]) }}</td>
                <td style="font-family:var(--font-sans);font-size:13px;color:var(--amber-ink);font-weight:500">{{ bugs(p).length ? 'Đáng ngờ: ' + bugs(p).join(', ') : '' }}</td>
              </tr>
            </tbody>
          </table>
          <div v-if="!tables.products.length" class="cap" style="padding:var(--s4)">Bảng trống. Bấm Reset (R) để nạp dữ liệu mẫu.</div>
        </div>

        <div v-if="sc.id === 'myisam'" style="margin-top:var(--s6)">
          <h2 class="h2">Bảng log_myisam</h2>
          <div class="tbl-wrap" style="margin-top:var(--s3);display:inline-block;min-width:280px">
            <table class="tbl">
              <thead><tr><th>id</th><th>level</th><th>Ghi chú</th></tr></thead>
              <tbody>
                <tr v-for="l in tables.log_myisam" :key="l.id" :class="{ sus: Number(l.level) === 127 }">
                  <td>{{ l.id }}</td><td>{{ l.level }}</td>
                  <td style="font-family:var(--font-sans);font-size:13px;color:var(--amber-ink);font-weight:500">{{ Number(l.level) === 127 ? 'Đáng ngờ: bị cắt về 127' : '' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
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
    layers(r) {
      const d = r.data;
      const e = d.error;
      const w = d.warnings || [];
      const dbName = this.dbLabel.split(' ')[0] || 'MariaDB';
      const conn = d.effective ? d.effective.connection : 'mariadb';
      if (e) {
        const pdoMsg = e.pdo_message || `SQLSTATE[${e.sqlstate}]: General error: ${e.code} ${e.message}`;
        const lrvMsg = e.laravel_message || `${pdoMsg} (Connection: ${conn}, SQL: ${d.sql})`;
        return [
          {
            name: dbName,
            badge: `ERROR ${e.code}`,
            tone: 'mint',
            isErr: true,
            desc: r.sc === 'myisam' && d.rows && d.rows.length
              ? 'Strict mode từ chối dòng lỗi, nhưng vì engine MyISAM không hỗ trợ transaction nên các dòng trước đó không được rollback.'
              : 'Strict mode chặn câu lệnh ngay tại tầng database và hủy thao tác ghi.',
            detail: `ERROR ${e.code} (${e.sqlstate}): ${e.message}`,
          },
          {
            name: 'PDO (PHP Driver)',
            badge: 'PDOException',
            tone: 'mint',
            isErr: true,
            desc: 'Driver PDO (ATTR_ERRMODE = ERRMODE_EXCEPTION) nhận gói tin lỗi từ DB và ném ngoại lệ.',
            detail: `PDOException: ${pdoMsg}`,
          },
          {
            name: 'Laravel Framework',
            badge: 'QueryException',
            tone: 'mint',
            isErr: true,
            desc: 'Illuminate\\Database\\Connection bắt PDOException, bọc thêm tên connection và câu SQL rồi ném QueryException.',
            detail: `Illuminate\\Database\\QueryException: ${lrvMsg}`,
          },
          {
            name: 'Ứng dụng',
            badge: 'HTTP 500',
            tone: 'mint',
            isErr: true,
            desc: 'Exception Handler dừng luồng xử lý, trả HTTP 500 và ghi log lỗi — ngăn ứng dụng xử lý tiếp trên dữ liệu sai.',
            detail: null,
          },
        ];
      }
      if (w.length) {
        const wLines = w.map((x) => `${x.level} ${x.code}: ${x.message}`).join('\n');
        const chg = this.changedKeys(r);
        const storedSummary = chg.length
          ? chg.map((k) => `${k}: ${quote(d.sent[k])} → ${quote(d.stored[k])}`).join(', ')
          : `affected = ${d.affected}`;
        return [
          {
            name: dbName,
            badge: `Warning ${w[0].code}${w.length > 1 ? ` (+${w.length - 1})` : ''}`,
            tone: 'amber',
            isErr: false,
            desc: 'Không có strict mode (hoặc dùng INSERT IGNORE): DB tự ép kiểu / cắt dữ liệu cho vừa cột, vẫn trả OK kèm warning trong bộ đệm.',
            detail: wLines,
          },
          {
            name: 'PDO (PHP Driver)',
            badge: 'không báo gì',
            tone: 'amber',
            isErr: false,
            desc: 'PDO chỉ ném exception khi DB trả mã ERROR. Với Warning, execute() vẫn trả về true như một câu lệnh hợp lệ hoàn toàn.',
            detail: `PDOStatement::execute() => true (rowCount: ${d.affected})`,
          },
          {
            name: 'Laravel Framework',
            badge: 'không báo gì',
            tone: 'amber',
            isErr: false,
            desc: 'Laravel không tự chạy SHOW WARNINGS sau câu lệnh, nên Eloquent / Query Builder coi như ghi thành công 100%.',
            detail: `Không có exception · Dữ liệu đã lưu: ${storedSummary}`,
          },
          {
            name: 'Ứng dụng',
            badge: 'chạy tiếp với dữ liệu sai',
            tone: 'amber',
            isErr: false,
            desc: 'Controller trả HTTP 200 OK. Sai lệch dữ liệu âm thầm đi vào hệ thống mà không có bất kỳ log lỗi hay cảnh báo nào.',
            detail: null,
          },
        ];
      }
      const note = r.sc === 'group_by'
        ? 'SQL_MODE không bật ONLY_FULL_GROUP_BY: DB tự chọn giá trị cột không nằm trong GROUP BY mà không báo lỗi hay warning.'
        : r.sc === 'zero_date'
          ? 'Chỉ có STRICT_TRANS_TABLES mà thiếu NO_ZERO_DATE: DB cho phép lưu ngày 0000-00-00 mà không sinh warning.'
          : 'Dữ liệu hợp lệ, thực thi thành công không có lỗi hay cảnh báo.';
      const tone = (r.sc === 'group_by' || r.sc === 'zero_date') ? 'amber' : 'mint';
      return [
        { name: dbName, badge: 'OK', tone, isErr: false, desc: note, detail: `0 errors, 0 warnings` },
        { name: 'PDO (PHP Driver)', badge: 'OK', tone, isErr: false, desc: 'Thực thi câu lệnh thành công, không có ngoại lệ.', detail: `PDOStatement::execute() => true` },
        { name: 'Laravel Framework', badge: 'OK', tone, isErr: false, desc: 'Nhận kết quả bình thường từ PDO.', detail: null },
        { name: 'Ứng dụng', badge: 'OK', tone, isErr: false, desc: 'Hoàn tất request với HTTP 200 OK.', detail: null },
      ];
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
