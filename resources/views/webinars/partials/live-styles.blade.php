<style>
    .meet-shell { --meet-bg:#202124; --meet-panel:#fff; --meet-muted:#9aa0a6; min-height:calc(100vh - 150px); }
    .meet-heading { display:flex; align-items:center; justify-content:space-between; gap:1rem; }
    .meet-heading h4 { color:#202124; font-weight:600; }.meet-heading small{color:#6b7280}
    .live-dot { background:#d93025;color:#fff;border-radius:999px;padding:.35rem .7rem;font-size:.72rem;font-weight:700;letter-spacing:.06em; }
    .live-dot span{display:inline-block;width:7px;height:7px;background:#fff;border-radius:50%;margin-right:5px;animation:livePulse 1.4s infinite}
    .meet-status{font-size:.8rem;color:#5f6368}.meet-workspace{display:flex;gap:14px;min-height:64vh}
    .meet-stage-wrap{position:relative;flex:1;min-width:0;background:var(--meet-bg);border-radius:18px;overflow:hidden;box-shadow:0 5px 25px rgba(0,0,0,.15)}
    .meet-grid{height:64vh;padding:12px;display:grid;grid-template-columns:repeat(auto-fit,minmax(270px,1fr));grid-auto-rows:minmax(220px,1fr);gap:10px;align-content:center}
    .meet-tile{position:relative;background:#3c4043;border-radius:14px;overflow:hidden;min-height:220px}.meet-player{width:100%;height:100%}
    .meet-camera-off{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.8rem;background:linear-gradient(145deg,#3c4043,#2d3033);color:#e8eaed;font-weight:600}.meet-camera-off i{width:72px;height:72px;border-radius:50%;display:grid;place-items:center;background:#4b4f52;color:#fff;font-size:1.65rem}.meet-camera-off span{font-size:.9rem;letter-spacing:.01em}
    .meet-tile video{object-fit:cover!important}.meet-name{position:absolute;left:14px;bottom:12px;color:#fff;font-size:.82rem;text-shadow:0 1px 4px #000;z-index:3}
    .meet-mic-state{position:absolute;right:12px;top:12px;width:34px;height:34px;border-radius:50%;display:grid;place-items:center;background:rgba(32,33,36,.75);color:#fff;z-index:3}
    .participant-menu{position:absolute;right:12px;bottom:10px;z-index:4}.participant-menu .dropdown-menu{font-size:.85rem}
    .meet-waiting{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;color:#e8eaed;padding:2rem}
    .meet-waiting p{color:#bdc1c6}.waiting-avatar{width:90px;height:90px;border-radius:50%;display:grid;place-items:center;background:#3c4043;font-size:2rem;margin-bottom:1rem}
    .meet-chat{width:330px;flex:0 0 330px;background:var(--meet-panel);border:1px solid #e5e7eb;border-radius:16px;display:flex!important;flex-direction:column;overflow:hidden;box-shadow:0 4px 18px rgba(0,0,0,.08)}
    .meet-chat.d-none{display:none!important}.chat-header{padding:1rem;display:flex;justify-content:space-between;border-bottom:1px solid #eee}.chat-messages{flex:1;overflow-y:auto;padding:1rem;max-height:calc(64vh - 130px)}
    .chat-empty{text-align:center;color:#9ca3af;padding:2rem 0}.chat-item{margin-bottom:1rem}.chat-meta{font-size:.75rem;color:#6b7280;margin-bottom:.2rem}.chat-meta strong{color:#202124;margin-right:.4rem}.chat-text{font-size:.88rem;color:#303134;white-space:pre-wrap;overflow-wrap:anywhere}
    .chat-form{display:flex;gap:.5rem;padding:.75rem;border-top:1px solid #eee;background:#f8f9fa}.chat-form textarea{flex:1;resize:none;border:0;background:#eef0f2;border-radius:18px;padding:.65rem .8rem;outline:0}.chat-form button{border:0;background:transparent;color:#a9005d;font-size:1.1rem}
    .meet-bottom-bar{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;padding:14px 4px 0}.meeting-info{font-size:.82rem;color:#3c4043}.meeting-info span{display:inline-block;height:18px;border-left:1px solid #dadce0;margin:0 10px;vertical-align:middle}
    .meet-controls,.meet-actions{display:flex;align-items:center;gap:10px}.meet-actions{justify-content:flex-end}.control-button,.action-button{border:0;width:48px;height:48px;border-radius:50%;display:grid;place-items:center;background:#3c4043;color:#fff;transition:.15s;position:relative}.action-button{background:transparent;color:#3c4043}
    .control-button:hover{background:#4f5357}.control-button.off{background:#d93025}.control-button.hangup{width:66px;border-radius:28px;background:#d93025}.control-button:disabled{opacity:.5}.action-button span{position:absolute;right:0;top:0;background:#5f6368;color:#fff;border-radius:10px;font-size:.65rem;padding:1px 5px}
    .meeting-toast{position:fixed;left:50%;bottom:95px;transform:translateX(-50%);background:#303134;color:#fff;padding:.7rem 1rem;border-radius:6px;z-index:1090;box-shadow:0 4px 14px rgba(0,0,0,.25)}
    @keyframes livePulse{50%{opacity:.35}}@media(max-width:991px){.meet-chat{position:fixed;right:12px;top:90px;bottom:90px;z-index:1080;width:min(360px,calc(100vw - 24px))}.chat-messages{max-height:none}.meet-bottom-bar{grid-template-columns:1fr}.meet-actions{display:none}.meet-grid{height:58vh;grid-template-columns:1fr}.meet-workspace{min-height:58vh}}
</style>
