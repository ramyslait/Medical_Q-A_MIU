// Dark mode toggling with persistence and no changes to existing code
(function () {
  var STORAGE_KEY = "mediqa_theme"; // values: "light" | "dark"

  function getStoredTheme() {
    try {
      return localStorage.getItem(STORAGE_KEY);
    } catch (e) {
      return null;
    }
  }

  function storeTheme(theme) {
    try {
      localStorage.setItem(STORAGE_KEY, theme);
    } catch (e) {}
  }

  function applyTheme(theme) {
    var html = document.documentElement;
    var body = document.body;
    if (theme === "dark") {
      // Ensure CSS is present before applying classes to prevent white flash
      ensureDarkStylesheet();
      html.classList.add("dark");
      body.classList.add("dark");
    } else {
      html.classList.remove("dark");
      body.classList.remove("dark");
    }
    updateToggleIcon(theme === "dark");
  }

  function ensureDarkStylesheet() {
    if (!document.getElementById("dark-css")) {
      var link = document.createElement("link");
      link.id = "dark-css";
      link.rel = "stylesheet";
      link.href = "css/dark.css";
      document.head.appendChild(link);
    }
  }

  function updateToggleIcon(isDark) {
    var btn = document.getElementById("theme-toggle");
    if (!btn) return;
    btn.setAttribute("aria-pressed", String(isDark));
    btn.innerHTML = isDark
      ? '<i class="fas fa-moon"></i>'
      : '<i class="fas fa-sun"></i>';
    btn.title = isDark ? "Switch to light mode" : "Switch to dark mode";
  }

  function detectSystemPref() {
    try {
      return window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches
        ? "dark"
        : "light";
    } catch (e) {
      return "light";
    }
  }

  function init() {
    ensureLightCtaOverride();
    var stored = getStoredTheme();
    var initial = stored || detectSystemPref();
    applyTheme(initial);

    var btn = document.getElementById("theme-toggle");
    if (btn) {
      btn.addEventListener("click", function () {
        var next = (document.documentElement.classList.contains("dark")) ? "light" : "dark";
        storeTheme(next);
        applyTheme(next);
      });
    }

    // React to system preference changes
    if (window.matchMedia) {
      var mq = window.matchMedia("(prefers-color-scheme: dark)");
      if (mq.addEventListener) {
        mq.addEventListener("change", function (e) {
          var storedTheme = getStoredTheme();
          if (!storedTheme) {
            applyTheme(e.matches ? "dark" : "light");
          }
        });
      }
    }
  }

  function ensureLightCtaOverride() {
    if (document.getElementById("light-cta-override")) return;
    var css =
      ':root:not(.dark) .nav-cta{background-color:var(--primary-color);color:#ffffff!important;}' +
      ':root:not(.dark) .nav-cta:hover,:root:not(.dark) .nav-cta.active{background-color:var(--primary-dark);color:#ffffff!important;}' +
      ':root:not(.dark) .stats .stat-item .stat-number{color:#ffffff!important;display:inline-block!important;margin-bottom:0.5rem;font-weight:700;padding-bottom:6px!important;border-bottom:2px solid rgba(255,255,255,0.9)!important;}';
    var style = document.createElement("style");
    style.id = "light-cta-override";
    style.appendChild(document.createTextNode(css));
    document.head.appendChild(style);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();


