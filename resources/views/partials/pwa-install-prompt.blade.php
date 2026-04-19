<div id="pwaInstallPrompt" class="hidden fixed bottom-4 right-4 z-[99999] max-w-sm rounded-xl border border-slate-700 bg-slate-900/95 p-4 text-white shadow-2xl backdrop-blur">
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm font-semibold">Install TrackFlow App</p>
            <p class="mt-1 text-xs text-slate-300">Faster access with app-like experience from your home screen.</p>
            <p id="pwaIosHint" class="mt-2 hidden text-xs text-amber-300">On iPhone: tap Share and then Add to Home Screen.</p>
        </div>
        <button id="pwaDismissButton" type="button" class="text-slate-300 hover:text-white" aria-label="Dismiss install prompt">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <button id="pwaInstallButton" type="button" class="mt-3 inline-flex w-full items-center justify-center rounded-lg bg-teal-500 px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-teal-400">
        <i class="fas fa-download mr-2"></i>Install App
    </button>
</div>
<script src="{{ asset('js/pwa-install.js') }}" defer></script>
