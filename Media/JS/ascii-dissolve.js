(() => {
  const headings = [...document.querySelectorAll('[data-ascii-reveal]')];
  const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
  if (!headings.length || motion.matches) return;

  const symbols = '.:;+*#@';
  const active = new Set();
  let frame = 0;

  function hash(index) {
    let value = (index + 1) * 2654435761;
    value ^= value >>> 16;
    value = Math.imul(value, 2246822519);
    value ^= value >>> 13;
    return value >>> 0;
  }

  function wrapTextNodes(root) {
    const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
    const nodes = [];
    while (walker.nextNode()) nodes.push(walker.currentNode);
    const letters = [];

    nodes.forEach((node) => {
      const fragment = document.createDocumentFragment();
      Array.from(node.nodeValue).forEach((character) => {
        if (/\s/.test(character)) {
          fragment.append(document.createTextNode(character));
          return;
        }
        const letter = document.createElement('span');
        letter.className = 'ascii-title-char';
        letter.dataset.final = character;
        letter.textContent = symbols[hash(letters.length) % symbols.length];
        letters.push(letter);
        fragment.append(letter);
      });
      node.replaceWith(fragment);
    });
    return letters;
  }

  function finish(state) {
    state.heading.classList.remove('ascii-title-active');
    state.original.replaceWith(...state.original.childNodes);
    state.clone.remove();
    active.delete(state);
  }

  function play(heading) {
    if (heading.dataset.asciiRevealDone || motion.matches) return;
    heading.dataset.asciiRevealDone = 'true';

    const original = document.createElement('span');
    original.className = 'ascii-title-original';
    const sourceNodes = [...heading.childNodes];
    sourceNodes.forEach((node) => original.append(node));

    const clone = document.createElement('span');
    clone.className = 'ascii-title-clone';
    clone.setAttribute('aria-hidden', 'true');
    sourceNodes.forEach((node) => clone.append(node.cloneNode(true)));
    const letters = wrapTextNodes(clone);

    heading.append(original, clone);
    heading.classList.add('ascii-title-active');

    const order = [...letters].sort((a, b) => hash(letters.indexOf(a)) - hash(letters.indexOf(b)));
    const stagger = Math.min(32, 1250 / Math.max(order.length, 1));
    const now = performance.now();
    order.forEach((letter, index) => {
      letter.dataset.start = String(index * stagger);
    });
    active.add({ heading, original, clone, letters, started: now, stagger, duration: 460 });
    if (!frame) frame = requestAnimationFrame(render);
  }

  function render(timestamp) {
    frame = 0;
    for (const state of active) {
      let complete = true;
      state.letters.forEach((letter, index) => {
        if (letter.classList.contains('is-settled')) return;
        const elapsed = timestamp - state.started - Number(letter.dataset.start);
        if (elapsed < 0) {
          complete = false;
          return;
        }
        letter.classList.add('is-scrambling');
        if (elapsed < state.duration) {
          complete = false;
          const cycle = Math.floor(elapsed / 42);
          letter.textContent = symbols[hash(index + cycle * 7) % symbols.length];
          return;
        }
        letter.textContent = letter.dataset.final;
        letter.classList.remove('is-scrambling');
        letter.classList.add('is-settled');
      });
      if (complete) finish(state);
    }
    if (active.size) frame = requestAnimationFrame(render);
  }

  const observer = 'IntersectionObserver' in window
    ? new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          observer.unobserve(entry.target);
          play(entry.target);
        });
      }, { threshold: .14, rootMargin: '0px 0px -6% 0px' })
    : null;

  if (observer) headings.forEach((heading) => observer.observe(heading));
  else headings.forEach(play);

  motion.addEventListener?.('change', () => {
    if (!motion.matches) return;
    observer?.disconnect();
    [...active].forEach(finish);
  });
})();
