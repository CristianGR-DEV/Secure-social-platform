(() => {
  const canvas = document.querySelector('[data-ascii-background]');
  if (!canvas) return;

  const context = canvas.getContext('2d', { alpha: true });
  if (!context) return;

  const errorMode = canvas.dataset.asciiBackground === 'error';
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const glyphs = errorMode ? ' .,:;+=*#@' : ' .:-=+*#@';
  const palette = ['#ac9bdb', '#8f83c8', '#586bd1', '#c3b1ed'];
  const cellWidth = errorMode ? 13 : 15;
  const cellHeight = errorMode ? 19 : 21;
  let width = 0;
  let height = 0;
  let columns = 0;
  let rows = 0;
  let running = false;
  let lastFrame = 0;

  function getPageHeight() {
    return Math.max(window.innerHeight, document.documentElement.scrollHeight);
  }

  function resize() {
    const ratio = Math.min(window.devicePixelRatio || 1, 1.5);
    width = document.documentElement.clientWidth || window.innerWidth;
    height = getPageHeight();
    canvas.width = Math.round(width * ratio);
    canvas.height = Math.round(height * ratio);
    canvas.style.width = `${width}px`;
    canvas.style.height = `${height}px`;
    context.setTransform(ratio, 0, 0, ratio, 0, 0);
    columns = Math.ceil(width / cellWidth);
    rows = Math.ceil(height / cellHeight);
    if (reducedMotion.matches) drawFrame(0);
    else start();
  }

  function drawFrame(time) {
    context.clearRect(0, 0, width, height);
    context.font = '12px ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace';
    context.textBaseline = 'middle';

    for (let row = 0; row < rows; row += 1) {
      const y = row * cellHeight + 4;
      const ny = y / Math.max(height, 1);
      for (let column = 0; column < columns; column += 1) {
        const x = column * cellWidth + 3;
        const nx = x / Math.max(width, 1);
        const wave = Math.sin(nx * 9 + ny * 3.2 - time)
          + Math.cos(ny * 8 - nx * 4 + time * .72)
          + Math.sin((nx + ny) * 12 + time * .46);
        const energy = (wave + 3) / 6;
        if (energy < (errorMode ? .4 : .48)) continue;

        const edge = errorMode
          ? Math.min(1, .68 + Math.abs(nx - .5) * .5 + Math.abs(ny - .5) * .2)
          : Math.min(1, .32 + Math.abs(nx - .5) * 1.1 + Math.abs(ny - .5) * .18);
        const alpha = errorMode
          ? (.07 + (energy - .4) * .34) * edge
          : (.09 + (energy - .48) * .34) * edge;
        const glyphIndex = Math.min(glyphs.length - 1, Math.floor(energy * glyphs.length));
        const colorIndex = Math.floor((nx * 2 + ny + energy) * palette.length) % palette.length;
        context.globalAlpha = alpha;
        context.fillStyle = palette[colorIndex];
        context.fillText(glyphs[glyphIndex], x, y);
      }
    }
    context.globalAlpha = 1;
  }

  function start() {
    if (running || document.hidden) return;
    running = true;
    requestAnimationFrame(draw);
  }

  function draw(timestamp) {
    if (document.hidden || reducedMotion.matches) {
      running = false;
      if (reducedMotion.matches) drawFrame(0);
      return;
    }
    if (timestamp - lastFrame >= 42) {
      lastFrame = timestamp;
      drawFrame(timestamp * .00022);
    }
    requestAnimationFrame(draw);
  }

  document.addEventListener('visibilitychange', () => {
    if (document.hidden) running = false;
    else if (reducedMotion.matches) drawFrame(0);
    else start();
  });
  window.addEventListener('resize', resize, { passive: true });
  const pageSizeObserver = new ResizeObserver(() => {
    const pageHeight = getPageHeight();
    if (Math.abs(pageHeight - height) > 1) resize();
  });
  pageSizeObserver.observe(document.body);
  reducedMotion.addEventListener?.('change', () => {
    if (reducedMotion.matches) {
      running = false;
      drawFrame(0);
    } else {
      start();
    }
  });

  resize();
})();
