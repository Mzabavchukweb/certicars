:root{--lg-blue:#0066ff;--lg-ink:#0a0a0a;--lg-muted:#475569;--lg-line:#e5edfa;--lg-bg:#f6f8fc}
.lg-hero{background:linear-gradient(180deg,#0a1432 0%,#12204a 100%);color:#fff;padding:56px 0 44px}
.lg-in{max-width:860px;margin:0 auto;padding:0 24px}
.lg-hero .lg-eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11px;font-weight:800;letter-spacing:1.8px;text-transform:uppercase;color:#7fb0ff;margin-bottom:14px}
.lg-hero h1{font-size:38px;font-weight:900;letter-spacing:-.7px;margin:0 0 10px;line-height:1.1}
.lg-hero p{font-size:14.5px;color:rgba(255,255,255,.7);margin:0}
.lg-body{background:#fff;padding:48px 0 72px}
.lg-updated{display:inline-flex;align-items:center;gap:8px;font-size:12.5px;color:#64748b;background:var(--lg-bg);border:1px solid var(--lg-line);border-radius:50px;padding:7px 14px;margin-bottom:28px}
.lg-updated svg{width:14px;height:14px;stroke:var(--lg-blue);fill:none;stroke-width:2}
.lg-card{background:#fff}
.lg-body h2{font-size:19px;font-weight:800;color:var(--lg-ink);letter-spacing:-.3px;margin:34px 0 12px;padding-top:8px}
.lg-body h2:first-of-type{margin-top:0}
.lg-body h3{font-size:15.5px;font-weight:800;color:var(--lg-ink);margin:22px 0 8px}
.lg-body p{font-size:14.5px;color:#334155;line-height:1.75;margin:0 0 14px}
.lg-body ul{margin:0 0 16px;padding-left:0;list-style:none;display:flex;flex-direction:column;gap:9px}
.lg-body ul li{position:relative;padding-left:22px;font-size:14.5px;color:#334155;line-height:1.65}
.lg-body ul li::before{content:'';position:absolute;left:2px;top:9px;width:6px;height:6px;border-radius:50%;background:var(--lg-blue)}
.lg-body a{color:var(--lg-blue);text-decoration:none;font-weight:600}
.lg-body a:hover{text-decoration:underline}
.lg-note{background:var(--lg-bg);border:1px solid var(--lg-line);border-left:3px solid var(--lg-blue);border-radius:10px;padding:16px 18px;margin:20px 0}
.lg-note p{margin:0;font-size:14px;color:#3f4d63}
.lg-admin{background:#f8fafc;border:1px solid var(--lg-line);border-radius:14px;padding:20px 22px;margin:6px 0 26px}
.lg-admin dl{margin:0;display:grid;grid-template-columns:180px 1fr;gap:8px 16px}
.lg-admin dt{font-size:13px;font-weight:700;color:#64748b}
.lg-admin dd{margin:0;font-size:13.5px;color:var(--lg-ink);font-weight:600}
.lg-toc{background:#f8fafc;border:1px solid var(--lg-line);border-radius:14px;padding:18px 22px;margin:0 0 30px}
.lg-toc strong{display:block;font-size:12px;font-weight:800;letter-spacing:.4px;text-transform:uppercase;color:#64748b;margin-bottom:10px}
.lg-toc ol{margin:0;padding-left:20px;display:flex;flex-direction:column;gap:6px}
.lg-toc ol li{font-size:13.5px}
.lg-toc a{color:#334155;font-weight:600;text-decoration:none}
.lg-toc a:hover{color:var(--lg-blue)}
@media(max-width:640px){
    .lg-hero{padding:40px 0 32px}
    .lg-hero h1{font-size:28px}
    .lg-admin dl{grid-template-columns:1fr;gap:2px 0}
    .lg-admin dt{margin-top:8px}
}
