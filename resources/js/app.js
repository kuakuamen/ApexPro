import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js')
            .catch((error) => console.error('Falha ao registrar o service worker:', error));
    });
}

let deferredInstallPrompt = null;
let installBanner = null;
let installDialog = null;

window.addEventListener('beforeinstallprompt', (event) => {
    event.preventDefault();
    deferredInstallPrompt = event;
});

function isAppInstalled() {
    return window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;
}

function hideInstallPrompt() {
    installBanner?.remove();
    installDialog?.remove();
    installBanner = null;
    installDialog = null;
}

function showInstallInstructions(isIOS) {
    if (!installDialog) return;

    const instructions = isIOS
        ? '<p>No Safari, toque em <strong>Compartilhar</strong> (ícone do quadrado com seta para cima) e depois em <strong>Adicionar à Tela de Início</strong>.</p><p>Por fim, toque em <strong>Adicionar</strong>.</p>'
        : '<p>Abra o menu do navegador <strong>⋮</strong> e toque em <strong>Instalar app</strong> ou <strong>Adicionar à tela inicial</strong>.</p>';

    installDialog.innerHTML = `
        <div class="apexpro-install-dialog-card" role="dialog" aria-modal="true" aria-labelledby="apexpro-install-title">
            <button class="apexpro-install-close" type="button" aria-label="Fechar">×</button>
            <h2 id="apexpro-install-title">Instale o ApexPro</h2>
            ${instructions}
            <button class="apexpro-install-done" type="button">Entendi</button>
        </div>
    `;

    installDialog.querySelector('.apexpro-install-close').addEventListener('click', () => installDialog.close());
    installDialog.querySelector('.apexpro-install-done').addEventListener('click', () => installDialog.close());
    installDialog.showModal();
}

async function promptToInstall(isIOS) {
    if (deferredInstallPrompt) {
        deferredInstallPrompt.prompt();
        const choice = await deferredInstallPrompt.userChoice;
        deferredInstallPrompt = null;

        if (choice?.outcome === 'accepted') {
            hideInstallPrompt();
            return;
        }
    }

    showInstallInstructions(isIOS);
}

function showInstallBanner() {
    if (isAppInstalled() || localStorage.getItem('apexpro-install-prompt-dismissed') === '1') return;

    const userAgent = navigator.userAgent;
    const isIOS = /iPhone|iPad|iPod/i.test(userAgent)
        || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    const isMobile = navigator.userAgentData?.mobile
        || /Android|iPhone|iPad|iPod|Mobile/i.test(userAgent)
        || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

    if (!isMobile) return;

    installBanner = document.createElement('aside');
    installBanner.className = 'apexpro-install-banner';
    installBanner.setAttribute('aria-label', 'Instalar ApexPro');
    installBanner.innerHTML = `
        <div class="apexpro-install-copy">
            <strong>Instale o ApexPro</strong>
            <span>Acesso rápido pela tela inicial do celular.</span>
        </div>
        <button class="apexpro-install-action" type="button">Instalar</button>
        <button class="apexpro-install-dismiss" type="button" aria-label="Dispensar aviso">×</button>
    `;

    installDialog = document.createElement('dialog');
    installDialog.className = 'apexpro-install-dialog';
    installDialog.addEventListener('click', (event) => {
        if (event.target === installDialog) installDialog.close();
    });

    const styles = document.createElement('style');
    styles.textContent = `
        .apexpro-install-banner { position:fixed; z-index:9999; left:12px; right:12px; bottom:calc(12px + env(safe-area-inset-bottom)); display:flex; align-items:center; gap:12px; padding:14px; border:1px solid rgba(34,211,238,.35); border-radius:16px; background:#0f1a2e; color:#f8fafc; box-shadow:0 12px 36px rgba(0,0,0,.38); font:inherit; }
        .apexpro-install-copy { display:flex; flex:1; min-width:0; flex-direction:column; gap:3px; }
        .apexpro-install-copy strong { font-size:14px; }
        .apexpro-install-copy span { color:#cbd5e1; font-size:12px; line-height:1.35; }
        .apexpro-install-action, .apexpro-install-done { border:0; border-radius:10px; padding:10px 14px; background:#06b6d4; color:#082f49; font:inherit; font-size:13px; font-weight:700; cursor:pointer; white-space:nowrap; }
        .apexpro-install-dismiss, .apexpro-install-close { border:0; background:transparent; color:#cbd5e1; font:inherit; font-size:24px; line-height:1; cursor:pointer; }
        .apexpro-install-dialog { width:min( calc(100% - 32px), 380px ); padding:0; border:0; border-radius:18px; background:transparent; color:#f8fafc; }
        .apexpro-install-dialog::backdrop { background:rgba(2,6,23,.72); }
        .apexpro-install-dialog-card { position:relative; padding:24px; border:1px solid rgba(34,211,238,.3); border-radius:18px; background:#0f1a2e; box-shadow:0 18px 50px rgba(0,0,0,.45); font:inherit; }
        .apexpro-install-dialog-card h2 { margin:0 32px 16px 0; font-size:20px; }
        .apexpro-install-dialog-card p { color:#cbd5e1; font-size:15px; line-height:1.55; }
        .apexpro-install-close { position:absolute; top:16px; right:16px; }
        .apexpro-install-done { width:100%; margin-top:8px; }
    `;

    document.head.append(styles);
    document.body.append(installBanner, installDialog);

    installBanner.querySelector('.apexpro-install-action').addEventListener('click', () => promptToInstall(isIOS));
    installBanner.querySelector('.apexpro-install-dismiss').addEventListener('click', () => {
        localStorage.setItem('apexpro-install-prompt-dismissed', '1');
        hideInstallPrompt();
    });
}

window.addEventListener('appinstalled', hideInstallPrompt);

window.addEventListener('load', () => {
    try {
        showInstallBanner();
    } catch (error) {
        console.error('Falha ao exibir a opção de instalação do ApexPro:', error);
    }
});
