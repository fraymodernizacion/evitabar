(function (window) {
  async function startQrScanner(options) {
    const video = options.video;
    const onResult = options.onResult;
    const onError = options.onError || function () {};

    if (!('mediaDevices' in navigator) || !('getUserMedia' in navigator.mediaDevices)) {
      onError('Tu navegador no soporta cámara para escaneo.');
      return;
    }

    if (!('BarcodeDetector' in window)) {
      onError('Tu navegador no soporta escaneo QR nativo.');
      return;
    }

    let stream;

    try {
      stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: { ideal: 'environment' } },
        audio: false,
      });

      video.srcObject = stream;
      await video.play();

      const detector = new BarcodeDetector({ formats: ['qr_code'] });
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');

      const loop = async () => {
        if (!video.srcObject) {
          return;
        }

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        try {
          const barcodes = await detector.detect(canvas);
          if (barcodes.length > 0 && barcodes[0].rawValue) {
            onResult(barcodes[0].rawValue);
            stop();
            return;
          }
        } catch (err) {
          onError('Error al escanear QR.');
        }

        window.requestAnimationFrame(loop);
      };

      window.requestAnimationFrame(loop);
    } catch (err) {
      onError('No se pudo acceder a la cámara. Revisá permisos.');
    }

    function stop() {
      if (video.srcObject) {
        const tracks = video.srcObject.getTracks();
        tracks.forEach((t) => t.stop());
      }
      video.srcObject = null;
    }

    return { stop };
  }

  window.EvitaScanner = { startQrScanner };
})(window);
