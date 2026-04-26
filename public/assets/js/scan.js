(function () {
  const video = document.getElementById('scanner-video');
  const status = document.getElementById('scanner-status');

  if (!video || !status || !window.EvitaScanner) {
    return;
  }

  window.EvitaScanner.startQrScanner({
    video,
    onResult: (rawValue) => {
      status.textContent = 'QR detectado. Redirigiendo...';

      // El QR puede traer URL completa o token puro; normalizamos ambos.
      let token = rawValue;
      try {
        const parsed = new URL(rawValue);
        token = parsed.searchParams.get('token') || rawValue;
      } catch (_err) {
        token = rawValue.replace(/^token=/, '');
      }

      const target = '/admin/scan.php?token=' + encodeURIComponent(token.trim());
      window.location.href = target;
    },
    onError: (message) => {
      status.textContent = message + ' Usá búsqueda manual por token o DNI.';
    },
  }).then(() => {
    status.textContent = 'Escáner activo. Apuntá al QR del cliente.';
  });
})();
