(function (global) {
  "use strict";

  var PAGES_HOST = /github\.io|gitlab\.io/i;
  var LOCAL_HOST = /^(localhost|127\.0\.0\.1)$/i;
  var injectedUrl = "__APP_URL__";
  if (injectedUrl.indexOf("__APP") !== -1) {
    injectedUrl = "";
  }

  function detectBaseHref() {
    if (location.protocol === "file:") {
      return "./";
    }
    if (PAGES_HOST.test(location.hostname)) {
      var match = location.pathname.match(/^(\/[^/]+\/)/);
      return match ? match[1] : "/";
    }
    var dir = location.pathname.replace(/[^/]*$/, "");
    return dir || "/";
  }

  function setBase() {
    var href = detectBaseHref();
    var base = document.querySelector("base[data-vitrine]");
    if (!base) {
      base = document.createElement("base");
      base.setAttribute("data-vitrine", "");
      document.head.insertBefore(base, document.head.firstChild);
    }
    base.href = href;
  }

  function isPagesSite() {
    return PAGES_HOST.test(location.hostname);
  }

  function isLocalDev() {
    return LOCAL_HOST.test(location.hostname) || location.protocol === "file:";
  }

  function normalizeUrl(raw) {
    if (!raw) {
      return "";
    }
    var trimmed = String(raw).trim().replace(/\/$/, "");
    if (!/^https?:\/\//i.test(trimmed)) {
      return "";
    }
    if (PAGES_HOST.test(trimmed)) {
      return "";
    }
    return trimmed;
  }

  function parseProductionUrl(raw) {
    if (!raw) {
      return "";
    }
    raw = raw.replace(/^\uFEFF/, "");
    var lines = raw.split("\n");
    for (var i = 0; i < lines.length; i++) {
      var line = lines[i].trim();
      if (!line || line.charAt(0) === "#") {
        continue;
      }
      var url = normalizeUrl(line);
      if (url) {
        return url;
      }
    }
    return "";
  }

  function getInjectedUrl() {
    return normalizeUrl(injectedUrl);
  }

  function resolveAppUrl(callback) {
    var url = getInjectedUrl();
    if (url) {
      callback(url);
      return;
    }

    fetch("production-url", { cache: "no-store" })
      .then(function (response) {
        return response.ok ? response.text() : "";
      })
      .then(function (raw) {
        url = parseProductionUrl(raw);
        if (url) {
          callback(url);
          return;
        }
        if (isLocalDev()) {
          callback(location.port === "8080" ? location.origin : "http://127.0.0.1:8000");
          return;
        }
        callback("");
      })
      .catch(function () {
        if (isLocalDev()) {
          callback(location.port === "8080" ? location.origin : "http://127.0.0.1:8000");
        } else {
          callback("");
        }
      });
  }

  function finishRedirect(url) {
    if (url) {
      location.replace(url + "/connexion");
      return;
    }
    document.documentElement.classList.add("vitrine-show-fallback");
  }

  function redirectToConnexion() {
    var sync = getInjectedUrl();
    if (sync) {
      location.replace(sync + "/connexion");
      return;
    }

    if (!isPagesSite()) {
      fetch("/connexion", { method: "HEAD", cache: "no-store" })
        .then(function (response) {
          if (response.ok) {
            location.replace("/connexion");
            return;
          }
          resolveAppUrl(finishRedirect);
        })
        .catch(function () {
          resolveAppUrl(finishRedirect);
        });
      return;
    }

    resolveAppUrl(finishRedirect);
  }

  setBase();

  if (document.documentElement.getAttribute("data-vitrine-redirect") === "connexion") {
    redirectToConnexion();
  } else if (!isPagesSite()) {
    fetch("/connexion", { method: "HEAD", cache: "no-store" })
      .then(function (response) {
        if (!response.ok) {
          return;
        }
        document.querySelectorAll('a[href="connexion.html"]').forEach(function (link) {
          link.setAttribute("href", "/connexion");
        });
      })
      .catch(function () {});
  }

  global.EduSphereVitrine = {
    detectBaseHref: detectBaseHref,
    isPagesSite: isPagesSite,
    isLocalDev: isLocalDev,
    resolveAppUrl: resolveAppUrl,
    redirectToConnexion: redirectToConnexion,
    getInjectedUrl: getInjectedUrl
  };
})(window);
