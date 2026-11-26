<style>
    * { box-sizing: border-box; }
    body { margin: 0; min-height: 100vh; background: #f1f5f9; color: #0f172a; font: 16px/1.5 system-ui, sans-serif; }
    a { color: #0369a1; }
    header.topbar { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px; padding: 16px max(24px, calc((100% - 1040px) / 2)); background: white; border-bottom: 1px solid #e2e8f0; }
    header.topbar .brand { color: #0369a1; font-size: 1.2rem; font-weight: 700; text-decoration: none; }
    header.topbar nav { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; }
    header.topbar nav a { padding: 8px 12px; border-radius: 8px; color: #334155; text-decoration: none; }
    header.topbar nav a:hover, header.topbar nav a.active { background: #e0f2fe; color: #075985; }
    header.topbar form { margin: 0; }
    main.page { max-width: 1040px; margin: 40px auto; padding: 0 24px; }
    section.card { padding: 28px; border: 1px solid #e2e8f0; border-radius: 14px; background: white; }
    section.card + section.card { margin-top: 24px; }
    h1 { margin: 0 0 8px; font-size: 1.8rem; }
    h2 { margin: 0 0 12px; font-size: 1.25rem; }
    p.lead { margin: 0 0 24px; color: #475569; }
    .btn { display: inline-block; padding: 10px 16px; border: 0; border-radius: 8px; background: #0369a1; color: white; font: inherit; font-weight: 700; text-decoration: none; cursor: pointer; }
    .btn:hover { background: #075985; }
    .btn-light { border: 1px solid #cbd5e1; background: white; color: #0f172a; font-weight: 400; }
    .btn-light:hover { background: #f8fafc; }
    .alert { margin-bottom: 20px; padding: 12px 16px; border-radius: 10px; border: 1px solid; }
    .alert-success { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
    .alert-error { background: #fef2f2; border-color: #fecaca; color: #991b1b; }
    .toolbar { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; margin-bottom: 20px; }
    .search { display: flex; gap: 8px; }
    select, input[type="text"], input[type="search"], input[type="email"], input[type="password"], input[type="date"] { padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font: inherit; }
    input:focus { outline: 3px solid #bae6fd; border-color: #0284c7; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
    th { background: #f8fafc; color: #475569; font-size: .85rem; text-transform: uppercase; letter-spacing: .03em; }
    td.amount, th.amount { text-align: right; }
    .empty { padding: 24px; text-align: center; color: #64748b; }
    .badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: .85rem; font-weight: 600; }
    .badge-paid { background: #dcfce7; color: #166534; }
    .badge-outstanding { background: #fef9c3; color: #854d0e; }
    .badge-overdue { background: #fee2e2; color: #991b1b; }
    .badge-draft { background: #e2e8f0; color: #334155; }
    .table-wrap { overflow-x: auto; }
    .visually-hidden { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); white-space: nowrap; }
    .pagination { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 20px; }
</style>
