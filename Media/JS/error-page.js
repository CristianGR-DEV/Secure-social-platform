(() => {
  const scan = document.querySelector('[data-ascii-scan]');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const positions = [...Array(7).keys(), ...Array.from({ length: 5 }, (_, index) => 5 - index)];
  let positionIndex = 0;
  let lastFrame = 0;
  let running = false;

  function draw() {
    const position = positions[positionIndex % positions.length];
    const cells = Array(7).fill('·');
    cells[position] = '█';
    scan.textContent = cells.join('');
    positionIndex += 1;
  }

  function animate(timestamp) {
    if (!running || document.hidden || reducedMotion.matches) return;
    if (timestamp - lastFrame >= 115) {
      draw();
      lastFrame = timestamp;
    }
    requestAnimationFrame(animate);
  }

  function start() {
    if (!scan || running || document.hidden || reducedMotion.matches) return;
    running = true;
    requestAnimationFrame(animate);
  }

  document.addEventListener('visibilitychange', () => {
    if (document.hidden) running = false;
    else start();
  });
  reducedMotion.addEventListener?.('change', () => {
    if (reducedMotion.matches) {
      running = false;
      if (scan) scan.textContent = '█······';
    } else {
      start();
    }
  });

  start();
})();
