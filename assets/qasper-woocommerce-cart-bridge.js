(function () {
  const cfg = window.QasperWooCommerceBridge;
  if (!cfg || !cfg.iframeOrigin || !cfg.restUrl || !cfg.cartBridgeNonce) return;

  function findQasperFrame() {
    const frames = document.querySelectorAll('iframe');
    for (const frame of frames) {
      try {
        if (new URL(frame.src).origin === cfg.iframeOrigin) return frame;
      } catch {
        continue;
      }
    }
    return null;
  }

  function isValidMessage(data) {
    return (
      data &&
      typeof data === 'object' &&
      data.type === 'qasper:woocommerce:add-to-cart' &&
      data.nonce === cfg.cartBridgeNonce &&
      typeof data.intentToken === 'string' &&
      data.intentToken.length > 0 &&
      data.intentToken.length <= 2048
    );
  }

  window.addEventListener('message', async function (event) {
    if (event.origin !== cfg.iframeOrigin) return;
    const frame = findQasperFrame();
    if (!frame || event.source !== frame.contentWindow) return;
    if (!isValidMessage(event.data)) return;

    const response = await fetch(cfg.restUrl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': cfg.nonce || '',
      },
      body: JSON.stringify({
        nonce: event.data.nonce,
        intentToken: event.data.intentToken,
      }),
    }).catch(function () {
      return null;
    });

    const payload = response ? await response.json().catch(function () { return {}; }) : {};
    frame.contentWindow.postMessage(
      {
        type: 'qasper:woocommerce:add-to-cart-result',
        requestId: event.data.requestId || null,
        ok: Boolean(response && response.ok && payload.success),
        cartUrl: payload.cartUrl || null,
      },
      cfg.iframeOrigin
    );
  });
})();
