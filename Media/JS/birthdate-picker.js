(() => {
  const picker = document.querySelector('[data-birthdate-picker]');
  if (!picker) return;

  const input = picker.querySelector('#birthdate');
  const trigger = picker.querySelector('[data-calendar-toggle]');
  const valueLabel = picker.querySelector('[data-date-value]');
  const popover = picker.querySelector('[data-calendar-popover]');
  const daysGrid = picker.querySelector('[data-calendar-days]');
  const monthSelect = picker.querySelector('[data-calendar-select="month"]');
  const yearSelect = picker.querySelector('[data-calendar-select="year"]');
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const minYear = today.getFullYear() - 120;
  let viewYear = today.getFullYear();
  let viewMonth = today.getMonth();
  let calendarOpen = false;

  const monthNames = new Intl.DateTimeFormat('es-ES', { month: 'long' });
  const dateNames = new Intl.DateTimeFormat('es-ES', { day: 'numeric', month: 'long', year: 'numeric' });
  const displayDate = new Intl.DateTimeFormat('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
  const pad = (number) => String(number).padStart(2, '0');
  const dateValue = (year, month, day) => `${year}-${pad(month + 1)}-${pad(day)}`;

  function buildOptions(select, values, currentValue, onChoose) {
    const menu = select.querySelector('[data-select-menu]');
    values.forEach(({ value, label }) => {
      const option = document.createElement('button');
      option.type = 'button';
      option.className = 'calendar-select-option';
      option.setAttribute('role', 'option');
      option.dataset.value = String(value);
      option.textContent = label;
      option.addEventListener('click', () => {
        onChoose(Number(value));
        closeSelectMenus();
        renderCalendar();
        select.querySelector('[data-select-toggle]').focus();
      });
      menu.append(option);
    });
    updateSelect(select, currentValue);
  }

  function updateSelect(select, value) {
    const option = select.querySelector(`[data-value="${value}"]`);
    select.querySelector('[data-select-value]').textContent = option?.textContent ?? String(value);
    select.querySelectorAll('[data-value]').forEach((item) => {
      item.setAttribute('aria-selected', String(item === option));
      item.classList.toggle('is-selected', item === option);
    });
  }

  function closeSelectMenus() {
    picker.querySelectorAll('[data-calendar-select]').forEach((select) => {
      select.querySelector('[data-select-toggle]').setAttribute('aria-expanded', 'false');
      select.querySelector('[data-select-menu]').hidden = true;
    });
  }

  function buildSelect(select) {
    const button = select.querySelector('[data-select-toggle]');
    const menu = select.querySelector('[data-select-menu]');
    button.addEventListener('click', () => {
      const opening = menu.hidden;
      closeSelectMenus();
      if (!opening) return;
      menu.hidden = false;
      button.setAttribute('aria-expanded', 'true');
      const selected = menu.querySelector('[aria-selected="true"]');
      if (selected) {
        selected.scrollIntoView({ block: 'nearest' });
        selected.focus({ preventScroll: true });
      }
    });
    menu.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        closeSelectMenus();
        button.focus();
      }
    });
  }

  buildSelect(monthSelect);
  buildSelect(yearSelect);
  buildOptions(monthSelect, Array.from({ length: 12 }, (_, month) => ({
    value: month,
    label: monthNames.format(new Date(today.getFullYear(), month, 1)),
  })), viewMonth, (month) => { viewMonth = month; });
  buildOptions(yearSelect, Array.from({ length: today.getFullYear() - minYear + 1 }, (_, offset) => ({
    value: today.getFullYear() - offset,
    label: String(today.getFullYear() - offset),
  })), viewYear, (year) => { viewYear = year; });

  function renderCalendar() {
    updateSelect(monthSelect, viewMonth);
    updateSelect(yearSelect, viewYear);
    daysGrid.replaceChildren();

    const firstWeekday = (new Date(viewYear, viewMonth, 1).getDay() + 6) % 7;
    const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
    const selected = input.value;
    const count = Math.ceil((firstWeekday + daysInMonth) / 7) * 7;

    for (let cell = 0; cell < count; cell += 1) {
      const day = cell - firstWeekday + 1;
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'calendar-day';
      if (day < 1 || day > daysInMonth) {
        button.classList.add('is-empty');
        button.disabled = true;
        button.tabIndex = -1;
        button.setAttribute('aria-hidden', 'true');
      } else {
        const date = new Date(viewYear, viewMonth, day);
        const value = dateValue(viewYear, viewMonth, day);
        button.textContent = String(day);
        button.setAttribute('role', 'gridcell');
        button.setAttribute('aria-label', dateNames.format(date));
        button.setAttribute('aria-selected', String(value === selected));
        if (value === selected) button.classList.add('is-selected');
        if (date.getTime() === today.getTime()) button.classList.add('is-today');
        if (date > today) button.disabled = true;
        button.addEventListener('click', () => {
          input.value = value;
          valueLabel.textContent = displayDate.format(date);
          valueLabel.classList.remove('is-placeholder');
          closeCalendar(true);
        });
      }
      daysGrid.append(button);
    }

    picker.querySelector('[data-calendar-prev]').disabled = viewYear === minYear && viewMonth === 0;
    picker.querySelector('[data-calendar-next]').disabled = viewYear === today.getFullYear() && viewMonth === today.getMonth();
  }

  function positionPopover() {
    if (!calendarOpen) return;
    const anchor = trigger.getBoundingClientRect();
    const width = Math.min(330, window.innerWidth - 24);
    popover.style.width = `${width}px`;
    const panel = popover.getBoundingClientRect();
    const below = window.innerHeight - anchor.bottom;
    const above = anchor.top;
    const top = below >= panel.height + 10 || below >= above
      ? Math.min(anchor.bottom + 8, window.innerHeight - panel.height - 12)
      : Math.max(12, anchor.top - panel.height - 8);
    const left = Math.max(12, Math.min(anchor.left, window.innerWidth - width - 12));
    popover.style.top = `${top}px`;
    popover.style.left = `${left}px`;
  }

  function openCalendar() {
    renderCalendar();
    calendarOpen = true;
    closeSelectMenus();
    popover.classList.add('is-open');
    if (typeof popover.showPopover === 'function') {
      try {
        popover.showPopover();
      } catch {
        picker.classList.add('calendar-open-fallback');
      }
    } else {
      picker.classList.add('calendar-open-fallback');
    }
    trigger.setAttribute('aria-expanded', 'true');
    requestAnimationFrame(positionPopover);
  }

  function closeCalendar(returnFocus = false) {
    if (!calendarOpen) return;
    calendarOpen = false;
    closeSelectMenus();
    if (typeof popover.hidePopover === 'function' && popover.matches(':popover-open')) {
      popover.hidePopover();
    } else {
      picker.classList.remove('calendar-open-fallback');
    }
    popover.classList.remove('is-open');
    trigger.setAttribute('aria-expanded', 'false');
    popover.style.removeProperty('top');
    popover.style.removeProperty('left');
    popover.style.removeProperty('width');
    if (returnFocus) trigger.focus();
  }

  trigger.addEventListener('click', () => {
    if (calendarOpen) closeCalendar();
    else openCalendar();
  });
  picker.querySelector('[data-calendar-prev]').addEventListener('click', () => {
    viewMonth -= 1;
    if (viewMonth < 0) { viewMonth = 11; viewYear -= 1; }
    renderCalendar();
  });
  picker.querySelector('[data-calendar-next]').addEventListener('click', () => {
    viewMonth += 1;
    if (viewMonth > 11) { viewMonth = 0; viewYear += 1; }
    renderCalendar();
  });
  picker.querySelector('[data-calendar-clear]').addEventListener('click', () => {
    input.value = '';
    valueLabel.textContent = 'Selecciona una fecha';
    valueLabel.classList.add('is-placeholder');
    closeCalendar(true);
  });
  picker.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape' || !calendarOpen) return;
    const openMenu = picker.querySelector('[data-select-menu]:not([hidden])');
    if (openMenu) {
      const owner = openMenu.closest('[data-calendar-select]');
      closeSelectMenus();
      owner.querySelector('[data-select-toggle]').focus();
    } else {
      closeCalendar(true);
    }
  });
  document.addEventListener('click', (event) => {
    if (!picker.contains(event.target) && calendarOpen) closeCalendar();
  });
  window.addEventListener('resize', positionPopover);
  window.addEventListener('scroll', positionPopover, true);

  valueLabel.classList.add('is-placeholder');
})();
