(() => {
  const mount = document.querySelector('[data-site-footer]');
  const script = document.currentScript;
  if (!mount || !script) return;

  const assetRoot = new URL('../', script.src);
  fetch(new URL('../HTML/site-footer.html', script.src))
    .then((response) => {
      if (!response.ok) throw new Error('No se pudo cargar el footer.');
      return response.text();
    })
    .then((html) => {
      mount.innerHTML = html;
      const inPhp = /\/php\/[^/]+$/i.test(window.location.pathname);
      const root = inPhp ? '../' : './';
      const links = {
        home: `${root}Index.html`,
        experience: `${root}Index.html#experiencia`,
        principles: `${root}Index.html#principios`,
        login: `${root}php/sesion.php`,
        register: `${root}php/sesion.php?mode=register`,
        terms: `${root}terminos.html`,
        github: 'https://github.com/CristianGR-DEV/Secure-social-platform',
      };
      mount.querySelectorAll('[data-site-link]').forEach((link) => {
        const target = link.dataset.siteLink;
        if (links[target]) link.href = links[target];
      });
      const icon = mount.querySelector('[data-site-icon]');
      if (icon) icon.src = new URL('SVG/corebite-mark.svg', assetRoot).href;
      mount.classList.add('site-footer--ready');
    })
    .catch((error) => {
      console.error(error);
      mount.setAttribute('hidden', '');
    });
})();
