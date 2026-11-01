/* ASCEND · Recorrido E2E
   Lógica de presentación: escenas de apertura, carrusel de capturas,
   desplazamiento automático de páginas largas, visor y atajos. */

(function () {
  'use strict';

  var slides = window.ASCEND_SLIDES || [];
  var chapters = window.ASCEND_CHAPTERS || [];
  var TOTAL = slides.length;

  var $ = function (selector, root) {
    return (root || document).querySelector(selector);
  };

  var body = document.body;
  var sceneStartup = $('#sceneStartup');
  var sceneAscend = $('#sceneAscend');
  var deck = $('#deck');
  var topbar = $('#topbar');
  var chapterLabel = $('#chapterLabel');
  var progressFill = $('#progressFill');
  var progressBar = $('.progress');
  var slideCounter = $('#slideCounter');
  var chapterNav = $('#chapterNav');
  var stage = $('#stage');
  var slideCopy = $('#slideCopy');
  var slideChapter = $('#slideChapter');
  var slideStep = $('#slideStep');
  var slideTitle = $('#slideTitle');
  var slideLead = $('#slideLead');
  var slidePoints = $('#slidePoints');
  var factLabel = $('#factLabel');
  var factValue = $('#factValue');
  var slideRoute = $('#slideRoute');
  var slideCaption = $('#slideCaption');
  var slideImage = $('#slideImage');
  var shotViewport = $('#shotViewport');
  var browser = $('#browser');
  var finale = $('#finale');
  var btnPausePan = $('#btnPausePan');
  var btnPrev = $('#btnPrev');
  var btnNext = $('#btnNext');
  var btnPlay = $('#btnPlay');
  var btnFull = $('#btnFull');
  var btnHelp = $('#btnHelp');
  var btnZoom = $('#btnZoom');
  var lightbox = $('#lightbox');
  var lightboxImg = $('#lightboxImg');
  var lightboxCaption = $('#lightboxCaption');
  var lightboxScroll = $('#lightboxScroll');
  var lightboxDownload = $('#lightboxDownload');
  var helpOverlay = $('#helpOverlay');
  var btnCloseHelp = $('#btnCloseHelp');
  var btnStart = $('#btnStart');
  var btnSkipIntro = $('#btnSkipIntro');
  var btnBeginDeck = $('#btnBeginDeck');
  var btnVideoDeck = $('#btnVideoDeck');
  var btnRestart = $('#btnRestart');
  var btnLastShot = $('#btnLastShot');

  var params = new URLSearchParams(window.location.search);
  var prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var manualIntro = params.get('intro') === 'manual' || prefersReduced;

  var state = {
    current: 0,
    started: false,
    scene: 'startup',
    playing: false,
    panPaused: false,
    panDuration: 0,
    zoom: 1,
    introTimer: null,
    advanceTimer: null,
    resizeTimer: null,
    lastFocus: null,
    touchStart: null,
    swipedAt: 0
  };

  function pad(n) {
    return String(n).padStart(2, '0');
  }

  function chapterFor(n) {
    for (var i = 0; i < chapters.length; i += 1) {
      if (n >= chapters[i].from && n <= chapters[i].to) return chapters[i];
    }
    return chapters[0];
  }

  function srcFor(slide) {
    return 'capturas/' + slide.file;
  }

  function clearIntroTimer() {
    if (state.introTimer) {
      clearTimeout(state.introTimer);
      state.introTimer = null;
    }
  }

  function clearAdvanceTimer() {
    if (state.advanceTimer) {
      clearTimeout(state.advanceTimer);
      state.advanceTimer = null;
    }
  }

  /* ---------------------------------------------------------------------
     Escenas
     --------------------------------------------------------------------- */

  function showScene(name) {
    state.scene = name;
    body.dataset.scene = name;
    sceneStartup.hidden = name !== 'startup';
    sceneAscend.hidden = name !== 'ascend';
    deck.hidden = name !== 'deck';
    topbar.hidden = name === 'startup';

    if (name === 'ascend') {
      chapterLabel.textContent = 'Presentación';
      slideCounter.textContent = 'ASCEND';
      progressFill.style.width = '0%';
      setProgressAria(0);
    }

    if (name === 'deck') {
      window.requestAnimationFrame(function () {
        if (state.current >= TOTAL) return;
        setupPan();
      });
    }
  }

  function toAscend(manual) {
    clearIntroTimer();
    clearAdvanceTimer();
    showScene('ascend');
    if (!manualIntro && !manual) {
      state.introTimer = window.setTimeout(function () {
        startDeck(false, true);
      }, 9200);
    }
  }

  function startDeck(play, keepSlide) {
    clearIntroTimer();
    var wasStarted = state.started;
    state.started = true;
    showScene('deck');

    if (!wasStarted || !keepSlide) {
      goToSlide(0, true);
    } else {
      render();
    }

    window.scrollTo(0, 0);
    if (play) setPlaying(true);
    if (!play && state.playing) setPlaying(false);
  }

  /* ---------------------------------------------------------------------
     Navegación
     --------------------------------------------------------------------- */

  function setProgress(percent) {
    var value = Math.max(0, Math.min(100, percent));
    progressFill.style.width = value.toFixed(2) + '%';
    setProgressAria(value);
  }

  function setProgressAria(value) {
    if (progressBar) progressBar.setAttribute('aria-valuenow', String(Math.round(value)));
  }

  function goToSlide(index, updateHash) {
    var next = Math.max(0, Math.min(TOTAL, index));
    state.current = next;
    state.started = true;
    if (state.scene !== 'deck') showScene('deck');
    state.panPaused = false;
    render();
    if (updateHash !== false) {
      var hash = next >= TOTAL ? '#fin' : '#s' + (next + 1);
      if (window.location.hash !== hash) {
        window.history.replaceState(null, '', hash);
      }
    }
    window.scrollTo({ top: 0, behavior: prefersReduced ? 'auto' : 'smooth' });
  }

  function nextSlide() {
    if (state.scene === 'startup') {
      toAscend(true);
      if (window.location.hash !== '#ascend') {
        window.history.replaceState(null, '', '#ascend');
      }
      return;
    }
    if (state.scene === 'ascend') {
      startDeck(false, false);
      return;
    }
    if (state.current >= TOTAL) return;
    goToSlide(state.current + 1, true);
  }

  function prevSlide() {
    if (state.scene === 'startup') return;
    if (state.scene === 'ascend') {
      showScene('startup');
      return;
    }
    if (state.current <= 0) {
      clearAdvanceTimer();
      if (state.playing) setPlaying(false);
      showScene('ascend');
      var hash = '#ascend';
      if (window.location.hash !== hash) window.history.replaceState(null, '', hash);
      return;
    }
    goToSlide(state.current - 1, true);
  }

  /* ---------------------------------------------------------------------
     Render
     --------------------------------------------------------------------- */

  function render() {
    var index = state.current;
    var isFinale = index >= TOTAL;

    stage.classList.toggle('is-finale', isFinale);
    slideCopy.hidden = isFinale;
    browser.hidden = isFinale;
    finale.hidden = !isFinale;

    updateRail(index);

    if (isFinale) {
      clearAdvanceTimer();
      chapterLabel.textContent = 'Cierre';
      slideCounter.textContent = 'FIN / ' + pad(TOTAL);
      setProgress(100);
      btnPrev.disabled = false;
      btnNext.disabled = true;
      document.title = 'Fin del recorrido · ASCEND';
      if (state.playing) setPlaying(false);
      return;
    }

    var slide = slides[index];
    var chapter = chapterFor(slide.n);
    var chapterIndex = chapters.indexOf(chapter) + 1;

    slideChapter.textContent = 'Capítulo ' + chapterIndex + ' · ' + chapter.short;
    slideStep.textContent = 'Paso ' + pad(slide.n) + ' / ' + pad(TOTAL);
    slideTitle.textContent = slide.title;
    slideLead.textContent = slide.lead;
    factLabel.textContent = slide.fact[0];
    factValue.textContent = slide.fact[1];
    slideRoute.textContent = 'localhost:8080/' + slide.route;
    slideCaption.textContent = pad(slide.n) + ' / ' + pad(TOTAL) + ' · ' + slide.caption;
    chapterLabel.textContent = chapter.label;
    slideCounter.textContent = pad(slide.n) + ' / ' + pad(TOTAL);
    setProgress((slide.n / TOTAL) * 100);

    slidePoints.textContent = '';
    slide.points.forEach(function (point) {
      var li = document.createElement('li');
      li.textContent = point;
      slidePoints.appendChild(li);
    });

    btnPrev.disabled = false;
    btnNext.disabled = false;

    /* Imagen y desplazamiento */
    state.panDuration = 0;
    state.panPaused = false;
    btnPausePan.hidden = true;
    btnPausePan.textContent = 'Pausar recorrido';
    shotViewport.classList.remove('is-panning', 'is-tall', 'is-paused', 'is-error');
    slideImage.style.transform = '';
    slideImage.dataset.slide = String(slide.n);
    slideImage.alt = slide.title + '. Captura ' + slide.n + ' del recorrido E2E de ASCEND.';

    var loaded = false;
    var onLoad = function () {
      if (slideImage.dataset.slide !== String(slide.n)) return;
      loaded = true;
      window.requestAnimationFrame(setupPan);
    };

    slideImage.onload = onLoad;
    slideImage.onerror = function () {
      if (slideImage.dataset.slide !== String(slide.n)) return;
      shotViewport.classList.add('is-error');
      btnPausePan.hidden = true;
    };
    slideImage.src = srcFor(slide);
    if (slideImage.complete && slideImage.naturalWidth > 0) {
      onLoad();
    }

    /* Reiniciar animaciones de entrada */
    slideCopy.classList.remove('is-entering');
    browser.classList.remove('is-entering');
    void slideCopy.offsetWidth;
    slideCopy.classList.add('is-entering');
    browser.classList.add('is-entering');

    document.title = pad(slide.n) + ' · ' + slide.title + ' — ASCEND';

    preload(index + 1);
    if (state.playing) scheduleAdvance();
    else clearAdvanceTimer();
  }

  function updateRail(index) {
    var slide = index < TOTAL ? slides[index] : null;
    var buttons = chapterNav.querySelectorAll('.rail-chapter');
    for (var i = 0; i < buttons.length; i += 1) {
      var button = buttons[i];
      var chapter = chapters[i];
      var current = slide && slide.n >= chapter.from && slide.n <= chapter.to;
      var done = index >= chapter.to;
      button.classList.toggle('is-current', Boolean(current));
      button.classList.toggle('is-done', Boolean(done) && !current);
      if (current) button.setAttribute('aria-current', 'true');
      else button.removeAttribute('aria-current');
    }
  }

  function preload(index) {
    if (index >= slides.length) return;
    var preloadImage = new Image();
    preloadImage.src = srcFor(slides[index]);
  }

  /* ---------------------------------------------------------------------
     Paneo de capturas largas
     --------------------------------------------------------------------- */

  function setupPan() {
    if (state.scene !== 'deck') return;
    if (state.current >= TOTAL) return;
    var viewportHeight = shotViewport.clientHeight;
    var imageHeight = slideImage.getBoundingClientRect().height;
    if (!imageHeight || !viewportHeight) return;

    var delta = Math.round(imageHeight - viewportHeight);
    var isTall = delta > 8;
    shotViewport.classList.toggle('is-tall', isTall);
    shotViewport.classList.remove('is-panning', 'is-paused');
    btnPausePan.hidden = true;
    state.panDuration = 0;

    if (delta < 24) return;

    var duration = Math.max(7, Math.min(26, delta / 150));
    shotViewport.style.setProperty('--pan', '-' + delta + 'px');
    shotViewport.style.setProperty('--pan-duration', duration.toFixed(2) + 's');
    btnPausePan.hidden = false;
    btnPausePan.textContent = 'Pausar recorrido';
    void shotViewport.offsetWidth;
    shotViewport.classList.add('is-panning');
    state.panDuration = duration;

    if (state.playing) scheduleAdvance();
  }

  function togglePan() {
    if (btnPausePan.hidden) return;
    state.panPaused = !state.panPaused;
    shotViewport.classList.toggle('is-paused', state.panPaused);
    btnPausePan.textContent = state.panPaused ? 'Reanudar recorrido' : 'Pausar recorrido';
  }

  /* ---------------------------------------------------------------------
     Modo video
     --------------------------------------------------------------------- */

  function setPlaying(on) {
    state.playing = Boolean(on);
    btnPlay.classList.toggle('is-active', state.playing);
    btnPlay.setAttribute('aria-label', state.playing ? 'Pausar modo video' : 'Activar modo video');
    btnPlay.setAttribute('title', state.playing ? 'Pausar modo video (P)' : 'Modo video (P)');
    var use = btnPlay.querySelector('use');
    if (use) use.setAttribute('href', state.playing ? '#i-pause' : '#i-play');

    if (state.playing) {
      if (state.current >= TOTAL) {
        state.current = 0;
        render();
        var hash = '#s1';
        if (window.location.hash !== hash) window.history.replaceState(null, '', hash);
      }
      scheduleAdvance();
    } else {
      clearAdvanceTimer();
    }
  }

  function scheduleAdvance() {
    clearAdvanceTimer();
    if (!state.playing || state.scene !== 'deck') return;
    var delay = state.panDuration ? state.panDuration * 1000 + 1800 : 8000;
    state.advanceTimer = window.setTimeout(function () {
      if (!state.playing) return;
      if (state.current >= TOTAL) {
        setPlaying(false);
        return;
      }
      goToSlide(state.current + 1, true);
    }, delay);
  }

  function togglePlaying() {
    if (state.scene !== 'deck') startDeck(true, false);
    else setPlaying(!state.playing);
  }

  /* ---------------------------------------------------------------------
     Visor de captura completa
     --------------------------------------------------------------------- */

  function applyZoom() {
    lightboxImg.style.setProperty('--zoom', (state.zoom * 100).toFixed(0) + '%');
  }

  function openLightbox() {
    if (state.current >= TOTAL) return;
    var slide = slides[state.current];
    var src = srcFor(slide);
    state.lastFocus = document.activeElement;
    lightboxImg.src = src;
    lightboxImg.alt = slide.title + '. Captura completa ' + slide.n + '.';
    lightboxCaption.textContent = pad(slide.n) + ' / ' + pad(TOTAL) + ' · ' + slide.title;
    lightboxDownload.href = src;
    lightboxDownload.setAttribute('download', slide.file);
    state.zoom = 1;
    applyZoom();
    lightbox.hidden = false;
    body.style.overflow = 'hidden';
    lightboxScroll.scrollTop = 0;
    $('#lightboxClose').focus();
  }

  function closeLightbox() {
    if (lightbox.hidden) return;
    lightbox.hidden = true;
    body.style.overflow = '';
    if (state.lastFocus && typeof state.lastFocus.focus === 'function') state.lastFocus.focus();
  }

  function zoomLightbox(delta) {
    state.zoom = Math.max(1, Math.min(2.4, state.zoom + delta));
    applyZoom();
  }

  /* ---------------------------------------------------------------------
     Ayuda
     --------------------------------------------------------------------- */

  function openHelp() {
    if (!lightbox.hidden) closeLightbox();
    helpOverlay.hidden = false;
    body.style.overflow = 'hidden';
    btnCloseHelp.focus();
  }

  function closeHelp() {
    if (helpOverlay.hidden) return;
    helpOverlay.hidden = true;
    body.style.overflow = '';
    btnHelp.focus();
  }

  function toggleHelp() {
    if (helpOverlay.hidden) openHelp();
    else closeHelp();
  }

  /* ---------------------------------------------------------------------
     Pantalla completa
     --------------------------------------------------------------------- */

  function toggleFullscreen() {
    var root = document.documentElement;
    try {
      if (!document.fullscreenElement) {
        if (root.requestFullscreen) root.requestFullscreen().catch(function () {});
      } else if (document.exitFullscreen) {
        document.exitFullscreen().catch(function () {});
      }
    } catch (error) {
      /* Sin permisos de pantalla completa: no se interrumpe la presentación. */
    }
  }

  /* ---------------------------------------------------------------------
     Hash y arranque
     --------------------------------------------------------------------- */

  function readHash() {
    var hash = window.location.hash || '';
    if (hash === '#ascend') {
      toAscend(true);
      return true;
    }
    if (hash === '#inicio' || hash === '#startup') {
      clearIntroTimer();
      clearAdvanceTimer();
      showScene('startup');
      return true;
    }
    if (hash === '#fin') {
      state.started = true;
      showScene('deck');
      goToSlide(TOTAL, false);
      return true;
    }
    var match = hash.match(/^#s(\d+)$/);
    if (match) {
      var n = parseInt(match[1], 10);
      if (n >= 1 && n <= TOTAL) {
        state.started = true;
        showScene('deck');
        goToSlide(n - 1, false);
        return true;
      }
    }
    return false;
  }

  function init() {
    chapters.forEach(function (chapter, i) {
      var button = document.createElement('button');
      button.type = 'button';
      button.className = 'rail-chapter';
      button.dataset.chapter = chapter.id;
      button.innerHTML =
        '<span class="rail-range">' + chapter.range + '</span>' +
        '<span class="rail-label">' + chapter.label + '</span>' +
        '<span class="rail-count">' + (chapter.to - chapter.from + 1) + ' capturas</span>';
      button.addEventListener('click', function () {
        startDeck(false, true);
        goToSlide(chapter.from - 1, true);
      });
      chapterNav.appendChild(button);
    });

    if (!readHash()) {
      showScene('startup');
      if (!manualIntro) {
        state.introTimer = window.setTimeout(function () {
          toAscend(false);
        }, 7200);
      }
    }

    if (state.started) showScene('deck');
  }

  /* ---------------------------------------------------------------------
     Eventos
     --------------------------------------------------------------------- */

  btnStart.addEventListener('click', function () { toAscend(true); });
  btnSkipIntro.addEventListener('click', function () { startDeck(false, false); });
  btnBeginDeck.addEventListener('click', function () { startDeck(false, false); });
  btnVideoDeck.addEventListener('click', function () { startDeck(true, false); });
  btnPrev.addEventListener('click', prevSlide);
  btnNext.addEventListener('click', nextSlide);
  btnPlay.addEventListener('click', togglePlaying);
  btnFull.addEventListener('click', toggleFullscreen);
  btnHelp.addEventListener('click', toggleHelp);
  btnCloseHelp.addEventListener('click', closeHelp);
  btnZoom.addEventListener('click', openLightbox);
  btnPausePan.addEventListener('click', togglePan);
  btnRestart.addEventListener('click', function () {
    setPlaying(false);
    goToSlide(0, true);
  });
  btnLastShot.addEventListener('click', function () {
    setPlaying(false);
    goToSlide(TOTAL - 1, true);
  });

  /* Navegación por clic universal: pensada para un mando a distancia que
     emula el botón izquierdo del mouse. Toda la pantalla avanza; el borde
     izquierdo retrocede. Los controles, enlaces y capas modales se ignoran. */
  var NAV_IGNORE = 'button, a, input, select, textarea, label, summary, ' +
    '[role="button"], [contenteditable="true"], .topbar, ' +
    '.deck-controls, .lightbox, .help-overlay';
  var EDGE_BACK = 56;

  function navBlocked(target) {
    return Boolean(target && target.closest && target.closest(NAV_IGNORE));
  }

  function hasSelection() {
    var selection = window.getSelection ? window.getSelection() : null;
    return Boolean(selection && String(selection).trim());
  }

  document.addEventListener('click', function (event) {
    if (event.defaultPrevented || event.button !== 0) return;
    if (!lightbox.hidden || !helpOverlay.hidden) return;
    if (navBlocked(event.target)) return;
    if (Date.now() - state.swipedAt < 420) return;
    if (hasSelection()) return;
    /* Un clic de teclado (detail 0) avanza; con puntero, el borde izquierdo vuelve. */
    var back = state.scene !== 'startup' && event.detail !== 0 && event.clientX <= EDGE_BACK;
    if (back) prevSlide();
    else nextSlide();
  });

  /* Segundo botón del mouse o del mando: retrocede sin abrir el menú contextual. */
  document.addEventListener('contextmenu', function (event) {
    if (state.scene === 'startup') return;
    if (!lightbox.hidden || !helpOverlay.hidden) return;
    if (navBlocked(event.target)) return;
    event.preventDefault();
    prevSlide();
  });

  $('#lightboxClose').addEventListener('click', closeLightbox);
  $('#lightboxZoomIn').addEventListener('click', function () { zoomLightbox(0.2); });
  $('#lightboxZoomOut').addEventListener('click', function () { zoomLightbox(-0.2); });
  lightboxScroll.addEventListener('click', function (event) {
    if (event.target === lightboxScroll) closeLightbox();
  });

  helpOverlay.addEventListener('click', function (event) {
    if (event.target === helpOverlay) closeHelp();
  });

  document.addEventListener('keydown', function (event) {
    var tag = (event.target && event.target.tagName) || '';
    var typing = /INPUT|TEXTAREA|SELECT/.test(tag) || (event.target && event.target.isContentEditable);

    if (!lightbox.hidden) {
      if (event.key === 'Escape') { event.preventDefault(); closeLightbox(); return; }
      if (event.key === '+' || event.key === '=') { event.preventDefault(); zoomLightbox(0.2); return; }
      if (event.key === '-') { event.preventDefault(); zoomLightbox(-0.2); return; }
    }

    if (!helpOverlay.hidden) {
      if (event.key === 'Escape' || event.key === '?' || event.key === 'h' || event.key === 'H') {
        event.preventDefault();
        closeHelp();
      }
      return;
    }

    if (typing) return;

    switch (event.key) {
      case 'ArrowRight':
      case 'PageDown':
      case ' ':
        event.preventDefault();
        nextSlide();
        break;
      case 'ArrowLeft':
      case 'PageUp':
      case 'Backspace':
      case ',':
        event.preventDefault();
        prevSlide();
        break;
      case '.':
      case 'n':
      case 'N':
        event.preventDefault();
        nextSlide();
        break;
      case 'Home':
        if (state.scene === 'deck') { event.preventDefault(); goToSlide(0, true); }
        break;
      case 'End':
        if (state.scene === 'deck') { event.preventDefault(); goToSlide(TOTAL, true); }
        break;
      case 'p':
      case 'P':
        event.preventDefault();
        togglePlaying();
        break;
      case 'g':
      case 'G':
        event.preventDefault();
        togglePan();
        break;
      case 'f':
      case 'F':
        event.preventDefault();
        toggleFullscreen();
        break;
      case '?':
      case 'h':
      case 'H':
        event.preventDefault();
        toggleHelp();
        break;
      case 'Enter':
        if (state.scene === 'deck' && !(event.target && event.target.closest && event.target.closest('button, a'))) {
          event.preventDefault();
          openLightbox();
        }
        break;
      case 'Escape':
        if (state.scene === 'deck' && state.playing) {
          event.preventDefault();
          setPlaying(false);
        }
        break;
      default:
        if (/^[1-5]$/.test(event.key)) {
          var chapter = chapters[parseInt(event.key, 10) - 1];
          if (chapter) {
            event.preventDefault();
            startDeck(state.playing, true);
            goToSlide(chapter.from - 1, true);
          }
        }
    }
  });

  window.addEventListener('hashchange', function () {
    clearIntroTimer();
    clearAdvanceTimer();
    if (!readHash()) {
      showScene('startup');
    }
  });

  window.addEventListener('resize', function () {
    clearTimeout(state.resizeTimer);
    state.resizeTimer = window.setTimeout(function () {
      if (state.scene === 'deck' && state.current < TOTAL) setupPan();
    }, 180);
  });

  deck.addEventListener('pointerdown', function (event) {
    if (event.pointerType === 'mouse' && event.button !== 0) return;
    state.touchStart = { x: event.clientX, y: event.clientY, time: Date.now() };
  });

  deck.addEventListener('pointerup', function (event) {
    if (!state.touchStart) return;
    var dx = event.clientX - state.touchStart.x;
    var dy = event.clientY - state.touchStart.y;
    var elapsed = Date.now() - state.touchStart.time;
    state.touchStart = null;
    if (elapsed > 900) return;
    if (Math.abs(dx) < 70 || Math.abs(dx) < Math.abs(dy) * 1.6) return;
    if (event.target.closest && event.target.closest('button, a')) return;
    state.swipedAt = Date.now();
    if (dx < 0) nextSlide();
    else prevSlide();
  });

  init();
})();
