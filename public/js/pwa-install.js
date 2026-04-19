(() => {
  const isLocalhost = ["localhost", "127.0.0.1"].includes(window.location.hostname);
  const isSecure = window.location.protocol === "https:" || isLocalhost;

  if (!isSecure || !("serviceWorker" in navigator)) {
    return;
  }

  window.addEventListener("load", () => {
    navigator.serviceWorker.register("/sw.js").catch((error) => {
      console.warn("Service worker registration failed", error);
    });
  });

  const root = document.documentElement;
  const container = document.getElementById("pwaInstallPrompt");
  const installButton = document.getElementById("pwaInstallButton");
  const dismissButton = document.getElementById("pwaDismissButton");
  const iosHelp = document.getElementById("pwaIosHint");
  const autoHideDelayMs = 10000;
  let autoHideTimer = null;

  if (!container || !installButton || !dismissButton) {
    return;
  }

  const hidePrompt = (persist = true) => {
    if (autoHideTimer) {
      clearTimeout(autoHideTimer);
      autoHideTimer = null;
    }
    container.classList.add("hidden");
    if (persist) {
      localStorage.setItem("pwaPromptDismissed", "1");
    }
  };

  const showPromptTemporarily = () => {
    if (localStorage.getItem("pwaPromptDismissed") === "1") {
      return;
    }

    container.classList.remove("hidden");

    if (autoHideTimer) {
      clearTimeout(autoHideTimer);
    }

    autoHideTimer = window.setTimeout(() => {
      hidePrompt(false);
    }, autoHideDelayMs);
  };

  if (window.matchMedia("(display-mode: standalone)").matches || window.navigator.standalone) {
    hidePrompt(false);
    return;
  }

  if (localStorage.getItem("pwaPromptDismissed") === "1") {
    hidePrompt(false);
    return;
  }

  let deferredPrompt = null;

  window.addEventListener("beforeinstallprompt", (event) => {
    event.preventDefault();
    deferredPrompt = event;
    showPromptTemporarily();
  });

  installButton.addEventListener("click", async () => {
    if (!deferredPrompt) {
      const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent);
      if (isIos && iosHelp) {
        iosHelp.classList.remove("hidden");
        showPromptTemporarily();
      }
      return;
    }

    deferredPrompt.prompt();
    const choiceResult = await deferredPrompt.userChoice;

    if (choiceResult.outcome === "accepted") {
      hidePrompt(true);
    }

    deferredPrompt = null;
  });

  dismissButton.addEventListener("click", () => {
    hidePrompt(true);
  });

  window.addEventListener("appinstalled", () => {
    hidePrompt(true);
    deferredPrompt = null;
  });

  // Let users bring the prompt back from browser console for support cases.
  root.showPwaInstallPrompt = () => {
    localStorage.removeItem("pwaPromptDismissed");
    showPromptTemporarily();
  };
})();
