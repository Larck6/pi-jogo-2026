/**
 * ============================================================================
 * LENDAS DO VALE - CONTROLADOR PRINCIPAL DA APRESENTAÇÃO INTERATIVA (PI 2026)
 * Navegação, Câmera Zoom, Lupa Interativa, Inspetor RPG, Partículas e Atalhos
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', () => {
  // Estado da Aplicação
  const state = {
    currentSlide: 1,
    totalSlides: 20,
    zoomLevel: 1,
    isPanning: false,
    panStart: { x: 0, y: 0 },
    panOffset: { x: 0, y: 0 },
    isMagnifierActive: false,
    isAutoPlay: false,
    autoPlayTimer: null,
    autoPlayDuration: 8500, // 8.5s por slide
    currentViewMode: 'canvas', // 'canvas' ou 'interactive'
  };

  // Elementos do DOM
  const cameraRig = document.getElementById('camera-rig');
  const slideFrame = document.getElementById('slide-frame');
  const slides = document.querySelectorAll('.slide');
  const currentSlideDisplay = document.getElementById('current-slide-num');
  const totalSlidesDisplay = document.getElementById('total-slides-num');
  const slideTitleDisplay = document.getElementById('current-slide-title');
  const progressFill = document.getElementById('progress-fill');
  const indicatorsContainer = document.getElementById('nav-indicators');
  
  // Botões HUD
  const btnPrev = document.getElementById('btn-prev');
  const btnNext = document.getElementById('btn-next');
  const btnZoomIn = document.getElementById('btn-zoom-in');
  const btnZoomOut = document.getElementById('btn-zoom-out');
  const btnZoomReset = document.getElementById('btn-zoom-reset');
  const btnFullscreen = document.getElementById('btn-fullscreen');
  const btnMute = document.getElementById('btn-mute');
  const btnBgm = document.getElementById('btn-bgm');
  const btnOverview = document.getElementById('btn-overview');
  const btnMagnifier = document.getElementById('btn-magnifier');
  const btnAutoplay = document.getElementById('btn-autoplay');
  const btnToggleView = document.getElementById('btn-toggle-view');

  // Modais
  const overviewModal = document.getElementById('overview-modal');
  const btnCloseOverview = document.getElementById('btn-close-overview');
  const inspectorModal = document.getElementById('inspector-modal');
  const btnCloseInspector = document.getElementById('btn-close-inspector');
  const magnifierLens = document.getElementById('magnifier-lens');

  // Canvas de Partículas
  const particleCanvas = document.getElementById('bg-particles-canvas');
  let particleCtx = null;
  let particles = [];
  let mousePos = { x: window.innerWidth / 2, y: window.innerHeight / 2 };

  // Metadados dos 20 Slides do Canva
  const slideData = [
    { id: 1, title: "Capa Oficial", subtitle: "PI Terceirão Sesc Senac 2026 - Lendas do Vale" },
    { id: 2, title: "Sobre o Projeto", subtitle: "A união do Terceirão e o resgate da cultura regional" },
    { id: 3, title: "Estrutura dos Grupos", subtitle: "6 frentes de desenvolvimento integradas" },
    { id: 4, title: "Game Design", subtitle: "Concepção, regras, jogabilidade e fases" },
    { id: 5, title: "Design Gráfico", subtitle: "Identidade visual, pixel art e cenários" },
    { id: 6, title: "Personagens e Elementos", subtitle: "Galeria de criações autorais para o jogo" },
    { id: 7, title: "Gaúcho - Player", subtitle: "O protagonista e a tradição do chimarrão" },
    { id: 8, title: "Fantasma - Mentor", subtitle: "Guia espectral do tutorial de comandos" },
    { id: 9, title: "Soldado Histórico", subtitle: "A memória dos conflitos e da Guerra do Contestado" },
    { id: 10, title: "Maquinista e Maria Fumaça", subtitle: "A locomotiva 310 e o progresso das cidades gêmeas" },
    { id: 11, title: "Monge João Maria", subtitle: "Lenda do Vale, fontes sagradas e profecias" },
    { id: 12, title: "Diabo do Clube Floresta", subtitle: "O mistério do salão de baile e pés de bode" },
    { id: 13, title: "Cobra do Rio Iguaçu", subtitle: "A serpente ancestral adormecida nas águas" },
    { id: 14, title: "NPCs e Moradores", subtitle: "Interatividade e vivência comunitária" },
    { id: 15, title: "Cenários Regionais", subtitle: "Pontos turísticos reais transpostos em 2D" },
    { id: 16, title: "Marketing e Redes", subtitle: "Engajamento no Instagram @lendasdovale.pi2026" },
    { id: 17, title: "Equipe de Som", subtitle: "Efeitos sonoros, trilha e atmosfera imersiva" },
    { id: 18, title: "Programação do Site", subtitle: "Portal da comunidade, download e interação" },
    { id: 19, title: "Programação do Jogo", subtitle: "Física, Godot Engine, lógica e colisões" },
    { id: 20, title: "Encerramento e Agradecimentos", subtitle: "Terceirão Sesc Senac União da Vitória 2026" }
  ];

  // Base de Dados de Personagens para Inspeção & Zoom Detalhado
  const characterDatabase = {
    'gaucho': {
      title: "Gaúcho - O Protagonista",
      tag: "PLAYER HERÓI | CULTURA REGIONAL",
      desc: "Personagem principal de Lendas do Vale. Equipado com sua cuia de chimarrão e vestimentas típicas da cultura do sul, ele é convocado pelo destino quando a cruz milagrosa é rompida. Sua missão é percorrer o vale de ponta a ponta para recuperar as relíquias sagradas.",
      lore: "A cultura gaúcha e tropeira é uma das maiores marcas de União da Vitória e Porto União, presente nos hábitos, tradições e valores do nosso povo.",
      sound: () => window.GameAudio.playGauchoTheme(),
      img: "assets/personagens/gaucho_transparent.png",
      stats: [
        { label: "PAPEL", val: "Protagonista Jogável" },
        { label: "HABILIDADE", val: "Pulo Duplo & Chimarrão" },
        { label: "MOTOR", val: "Godot GDScript 2D" },
        { label: "ORIGEM", val: "Vale do Iguaçu (PR/SC)" }
      ]
    },
    'fantasma': {
      title: "Fantasma - Guia Espiritual",
      tag: "NPC MENTOR | TUTORIAL INTERATIVO",
      desc: "Espírito guardião antigo que acolhe o jogador nos primeiros instantes da aventura. Com diálogos sábios e bem-humorados, ele ensina as mecânicas fundamentais de movimentação, corrida, pulo e interação com o cenário.",
      lore: "Inspirado nas incontáveis histórias de almas e aparições que rondam os casarões e margens históricas das cidades gêmeas.",
      sound: () => window.GameAudio.playFantasmaTheme(),
      img: "assets/personagens/fantasma_transparent.png",
      stats: [
        { label: "PAPEL", val: "Guia de Gameplay" },
        { label: "TIPO", val: "Entidade Espectral" },
        { label: "MECÂNICA", val: "Diálogo & Teclas HUD" },
        { label: "STATUS", val: "Imortal / Neutro" }
      ]
    },
    'soldado': {
      title: "Soldado da Fronteira",
      tag: "PERSONAGEM HISTÓRICO | MEMÓRIA MILITAR",
      desc: "Figura marcante que presta homenagem à rica história das forças que atuaram na região do Contestado. Em Lendas do Vale, o soldado atua como sentinela em postos avançados e pontos estratégicos do mapa.",
      lore: "União da Vitória e Porto União foram palcos centrais da Guerra do Contestado (1912-1916) e possuem tradição militar forte até os dias atuais.",
      sound: () => window.GameAudio.playSoldadoTheme(),
      img: "assets/personagens/soldado_transparent.png",
      stats: [
        { label: "PAPEL", val: "Sentinela & Narrativa" },
        { label: "ÉPOCA", val: "Guerra do Contestado" },
        { label: "ARMA", val: "Mosquetão de Época" },
        { label: "DEFESA", val: "Uniforme Camuflado" }
      ]
    },
    'maria_fumaca': {
      title: "Maquinista e a Maria Fumaça 310",
      tag: "PATRIMÔNIO HISTÓRICO | CORAÇÃO FERROVIÁRIO",
      desc: "A famosa Locomotiva a vapor número 310 e seu fiel maquinista. A Maria Fumaça foi o verdadeiro motor do crescimento econômico e da união entre o Paraná e Santa Catarina, transportando passageiros e a rica madeira e erva-mate.",
      lore: "A Estação União é a única estação ferroviária do Brasil dividida exatamente pela divisa entre dois estados (Paraná e Santa Catarina).",
      sound: () => window.GameAudio.playMariaFumacaTheme(),
      img: "assets/personagens/maria_fumaca_transparent.png",
      stats: [
        { label: "MODELO", val: "Locomotiva Baldwin 310" },
        { label: "COMBUSTÍVEL", val: "Lenha & Vapor" },
        { label: "FUNÇÃO", val: "Ponto Turístico & Viagem" },
        { label: "SIMBOLISMO", val: "União dos Povos" }
      ]
    },
    'monge': {
      title: "Monge João Maria",
      tag: "BOSS MÍSTICO | PROFETA DO VALE",
      desc: "Figura lendária de extrema reverência popular no Vale do Iguaçu. O Monge João Maria viajou pelas matas abençoando olhos d'água, plantando cruzes e curando enfermidades com ervas da terra.",
      lore: "Segundo a lenda local, o morro da Cruz e diversas fontes de água pura protegiam a cidade de enchentes catastróficas pelas orações do Monge.",
      sound: () => window.GameAudio.playMongeTheme(),
      img: "assets/personagens/monge.png",
      stats: [
        { label: "TIPO", val: "Lorde Místico" },
        { label: "HABILIDADE", val: "Água Benta & Salmos" },
        { label: "ARENA", val: "Morro da Cruz" },
        { label: "RECOMPENSA", val: "Fragmento Sagrado I" }
      ]
    },
    'diabo': {
      title: "Diabo do Clube Floresta",
      tag: "BOSS LENDÁRIO | O MISTÉRIO DO BAILE",
      desc: "Uma das lendas urbanas mais famosas da região: em uma animada noite de festa no antigo Clube Floresta, um cavalheiro charmoso e impecável de terno vermelho seduziu os olhares do salão, até que seus sapatos caíram, revelando cascos de bode!",
      lore: "Após a revelação, o salão foi tomado por uma névoa espessa e cheiro de enxofre enquanto as luzes se apagavam em pânico.",
      sound: () => window.GameAudio.playDiaboTheme(),
      img: "assets/personagens/diabo.png",
      stats: [
        { label: "TIPO", val: "Boss de Fogo & Ilusão" },
        { label: "HABILIDADE", val: "Valsa Infernal & Garras" },
        { label: "ARENA", val: "Salão do Clube Floresta" },
        { label: "RECOMPENSA", val: "Fragmento Sagrado II" }
      ]
    },
    'cobra': {
      title: "Cobra Gigante do Rio Iguaçu",
      tag: "BOSS TITÂNICO | O GUARDIÃO DAS ÁGUAS",
      desc: "O monstro mitológico supremo das águas profundas do Rio Iguaçu. A lenda popular relata que uma gigantesca serpente dorme adormecida no leito do rio, com a cabeça sob a Ponte do Arco e a cauda nas correntezas distantes.",
      lore: "Diz a tradição que se a grande cobra despertar e se contorcer, as águas do Iguaçu engolirão as cidades em uma cheia jamais vista na história.",
      sound: () => window.GameAudio.playCobraTheme(),
      img: "assets/personagens/cobra.png",
      stats: [
        { label: "TAMANHO", val: "Colossal (+50 metros)" },
        { label: "HABILIDADE", val: "Tsunami de Águas Negras" },
        { label: "ARENA", val: "Leito do Rio Iguaçu" },
        { label: "RECOMPENSA", val: "Restauração do Vale" }
      ]
    }
  };

  /* -------------------------------------------------------------
     INICIALIZAÇÃO DOS INDICADORES E NAVEGADOR
     ------------------------------------------------------------- */
  function buildIndicators() {
    indicatorsContainer.innerHTML = '';
    for (let i = 1; i <= state.totalSlides; i++) {
      const pip = document.createElement('div');
      pip.className = `indicator-pip ${i === state.currentSlide ? 'active' : ''}`;
      pip.title = `Slide ${i}: ${slideData[i - 1].title}`;
      pip.addEventListener('click', () => {
        window.GameAudio.playClick();
        goToSlide(i);
      });
      indicatorsContainer.appendChild(pip);
    }
  }

  function buildOverviewGrid() {
    const grid = document.getElementById('overview-grid');
    if (!grid) return;
    grid.innerHTML = '';

    slideData.forEach(item => {
      const card = document.createElement('div');
      card.className = `overview-card ${item.id === state.currentSlide ? 'active' : ''}`;
      card.innerHTML = `
        <img class="overview-thumb" src="assets/slides/slide_${item.id}.png" alt="${item.title}">
        <div class="overview-card-label">#${item.id} ${item.title}</div>
      `;
      card.addEventListener('mouseenter', () => window.GameAudio.playHover());
      card.addEventListener('click', () => {
        window.GameAudio.playClick();
        goToSlide(item.id);
        closeOverview();
      });
      grid.appendChild(card);
    });
  }

  /* -------------------------------------------------------------
     NAVEGAÇÃO ENTRE SLIDES
     ------------------------------------------------------------- */
  function goToSlide(slideNum) {
    if (slideNum < 1 || slideNum > state.totalSlides) return;
    if (slideNum === state.currentSlide && document.querySelector(`.slide[data-slide="${slideNum}"]`).classList.contains('active')) return;

    resetZoom();

    const oldSlide = document.querySelector(`.slide[data-slide="${state.currentSlide}"]`);
    if (oldSlide) {
      oldSlide.classList.remove('active');
      oldSlide.classList.add('prev-slide');
      setTimeout(() => oldSlide.classList.remove('prev-slide'), 500);
    }

    state.currentSlide = slideNum;
    const newSlide = document.querySelector(`.slide[data-slide="${state.currentSlide}"]`);
    if (newSlide) {
      newSlide.classList.add('active');
    }

    // Atualiza HUD
    currentSlideDisplay.textContent = String(state.currentSlide).padStart(2, '0');
    totalSlidesDisplay.textContent = String(state.totalSlides).padStart(2, '0');
    slideTitleDisplay.textContent = slideData[state.currentSlide - 1].title;

    const progressPct = ((state.currentSlide - 1) / (state.totalSlides - 1)) * 100;
    progressFill.style.width = `${Math.max(5, progressPct)}%`;

    // Atualiza indicadores
    document.querySelectorAll('.indicator-pip').forEach((pip, idx) => {
      pip.classList.toggle('active', idx + 1 === state.currentSlide);
    });

    btnPrev.disabled = state.currentSlide === 1;
    btnNext.disabled = state.currentSlide === state.totalSlides;

    window.GameAudio.playSlideChange();

    // Controle do Vídeo de Marketing (Slide 16)
    const mktVideo = document.getElementById('marketing-video-player');
    if (mktVideo) {
      if (state.currentSlide === 16) {
        mktVideo.currentTime = 0;
        mktVideo.play().catch(() => {});
      } else {
        mktVideo.pause();
      }
    }

    // Reinicia autoplay timer se ativo
    if (state.isAutoPlay) {
      scheduleNextAutoPlay();
    }
  }

  function nextSlide() {
    if (state.currentSlide < state.totalSlides) {
      goToSlide(state.currentSlide + 1);
    } else if (state.isAutoPlay) {
      goToSlide(1);
    }
  }

  function prevSlide() {
    if (state.currentSlide > 1) {
      goToSlide(state.currentSlide - 1);
    }
  }

  /* -------------------------------------------------------------
     SISTEMA DE CÂMERA & ZOOM
     ------------------------------------------------------------- */
  function updateCameraTransform() {
    cameraRig.style.transform = `translate(${state.panOffset.x}px, ${state.panOffset.y}px) scale(${state.zoomLevel})`;
    btnZoomReset.textContent = `${Math.round(state.zoomLevel * 100)}%`;
  }

  function setZoom(newZoom) {
    const clamped = Math.min(3.2, Math.max(1, newZoom));
    if (clamped === state.zoomLevel) return;

    if (clamped > state.zoomLevel) {
      window.GameAudio.playZoomIn();
    } else {
      window.GameAudio.playZoomOut();
    }

    state.zoomLevel = clamped;
    if (state.zoomLevel === 1) {
      state.panOffset = { x: 0, y: 0 };
    }
    updateCameraTransform();
  }

  function resetZoom() {
    state.zoomLevel = 1;
    state.panOffset = { x: 0, y: 0 };
    updateCameraTransform();
  }

  // Zoom via Botões
  btnZoomIn.addEventListener('click', () => setZoom(state.zoomLevel + 0.35));
  btnZoomOut.addEventListener('click', () => setZoom(state.zoomLevel - 0.35));
  btnZoomReset.addEventListener('click', () => {
    if (state.zoomLevel > 1) {
      resetZoom();
      window.GameAudio.playZoomOut();
    } else {
      setZoom(1.75);
    }
  });

  // Pan ao arrastar com mouse quando o zoom estiver acima de 1.0
  slideFrame.addEventListener('mousedown', (e) => {
    if (state.zoomLevel <= 1 || state.isMagnifierActive) return;
    state.isPanning = true;
    state.panStart = { x: e.clientX - state.panOffset.x, y: e.clientY - state.panOffset.y };
    slideFrame.style.cursor = 'grabbing';
  });

  window.addEventListener('mousemove', (e) => {
    mousePos = { x: e.clientX, y: e.clientY };

    if (state.isPanning) {
      state.panOffset = {
        x: e.clientX - state.panStart.x,
        y: e.clientY - state.panStart.y
      };
      updateCameraTransform();
    }

    if (state.isMagnifierActive) {
      updateMagnifier(e);
    }
  });

  window.addEventListener('mouseup', () => {
    state.isPanning = false;
    slideFrame.style.cursor = '';
  });

  // Zoom suave com a rodinha do mouse (Wheel)
  slideFrame.addEventListener('wheel', (e) => {
    e.preventDefault();
    const delta = e.deltaY < 0 ? 0.2 : -0.2;
    setZoom(state.zoomLevel + delta);
  }, { passive: false });

  /* -------------------------------------------------------------
     LUPA INTERATIVA (MAGNIFIER GLASS)
     ------------------------------------------------------------- */
  function toggleMagnifier() {
    state.isMagnifierActive = !state.isMagnifierActive;
    btnMagnifier.classList.toggle('active', state.isMagnifierActive);
    magnifierLens.classList.toggle('active', state.isMagnifierActive);

    if (state.isMagnifierActive) {
      window.GameAudio.playZoomIn();
      resetZoom();
    } else {
      window.GameAudio.playZoomOut();
    }
  }

  function updateMagnifier(e) {
    const rect = slideFrame.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    if (x < 0 || y < 0 || x > rect.width || y > rect.height) {
      magnifierLens.style.display = 'none';
      return;
    }

    magnifierLens.style.display = 'block';
    magnifierLens.style.left = `${e.clientX - 110}px`;
    magnifierLens.style.top = `${e.clientY - 110}px`;

    // Atualiza imagem de fundo da lupa de acordo com o slide atual
    const bgUrl = `assets/slides/slide_${state.currentSlide}.png`;
    magnifierLens.style.backgroundImage = `url("${bgUrl}")`;
    magnifierLens.style.backgroundSize = `${rect.width * 2.5}px ${rect.height * 2.5}px`;

    const bgX = (x / rect.width) * 100;
    const bgY = (y / rect.height) * 100;
    magnifierLens.style.backgroundPosition = `${bgX}% ${bgY}%`;
  }

  btnMagnifier.addEventListener('click', toggleMagnifier);

  /* -------------------------------------------------------------
     INSPETOR DE PERSONAGEM / MODAL RPG
     ------------------------------------------------------------- */
  function openInspector(charKey) {
    const data = characterDatabase[charKey];
    if (!data) return;

    window.GameAudio.playZoomIn();
    if (data.sound) data.sound();

    document.getElementById('inspector-tag').textContent = data.tag;
    document.getElementById('inspector-title').textContent = data.title;
    document.getElementById('inspector-text').textContent = data.desc;
    document.getElementById('inspector-lore-text').textContent = data.lore;
    document.getElementById('inspector-zoom-img').src = data.img;

    const statsGrid = document.getElementById('inspector-stats');
    statsGrid.innerHTML = '';
    data.stats.forEach(st => {
      const box = document.createElement('div');
      box.className = 'stat-box';
      box.innerHTML = `
        <span class="stat-label">${st.label}</span>
        <span class="stat-value">${st.val}</span>
      `;
      statsGrid.appendChild(box);
    });

    inspectorModal.classList.add('open');
  }

  function closeInspector() {
    inspectorModal.classList.remove('open');
    window.GameAudio.playZoomOut();
  }

  btnCloseInspector.addEventListener('click', closeInspector);
  inspectorModal.addEventListener('click', (e) => {
    if (e.target === inspectorModal) closeInspector();
  });

  // Vincula cliques em hotspots de personagens
  document.querySelectorAll('[data-inspect]').forEach(el => {
    el.addEventListener('click', (e) => {
      e.stopPropagation();
      const key = el.getAttribute('data-inspect');
      openInspector(key);
    });
  });

  /* -------------------------------------------------------------
     OVERVIEW MODAL (GRADE DE SLIDES)
     ------------------------------------------------------------- */
  function openOverview() {
    window.GameAudio.playZoomIn();
    buildOverviewGrid();
    overviewModal.classList.add('open');
  }

  function closeOverview() {
    overviewModal.classList.remove('open');
    window.GameAudio.playZoomOut();
  }

  btnOverview.addEventListener('click', openOverview);
  btnCloseOverview.addEventListener('click', closeOverview);
  overviewModal.addEventListener('click', (e) => {
    if (e.target === overviewModal) closeOverview();
  });

  /* -------------------------------------------------------------
     CONTROLES DE SOM & BGM
     ------------------------------------------------------------- */
  btnMute.addEventListener('click', () => {
    const muted = window.GameAudio.toggleMute();
    btnMute.textContent = muted ? '🔇' : '🔊';
    btnMute.classList.toggle('active', muted);
    if (!muted) window.GameAudio.playClick();
  });

  btnBgm.addEventListener('click', () => {
    const playing = window.GameAudio.toggleBgm();
    btnBgm.classList.toggle('active', playing);
    btnBgm.textContent = playing ? '🎵 MÚSICA ON' : '🎵 MÚSICA OFF';
  });

  /* -------------------------------------------------------------
     TELA CHEIA (FULLSCREEN)
     ------------------------------------------------------------- */
  function toggleFullscreen() {
    window.GameAudio.playClick();
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen().catch(() => {});
      btnFullscreen.textContent = '⛶';
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
        btnFullscreen.textContent = '🗖';
      }
    }
  }

  btnFullscreen.addEventListener('click', toggleFullscreen);

  /* -------------------------------------------------------------
     AUTO-PLAY DA APRESENTAÇÃO
     ------------------------------------------------------------- */
  function scheduleNextAutoPlay() {
    if (state.autoPlayTimer) clearTimeout(state.autoPlayTimer);
    state.autoPlayTimer = setTimeout(() => {
      if (state.isAutoPlay) nextSlide();
    }, state.autoPlayDuration);
  }

  function toggleAutoPlay() {
    state.isAutoPlay = !state.isAutoPlay;
    btnAutoplay.classList.toggle('active', state.isAutoPlay);
    btnAutoplay.textContent = state.isAutoPlay ? '⏸ PAUSAR' : '▶ AUTO';
    window.GameAudio.playClick();

    if (state.isAutoPlay) {
      scheduleNextAutoPlay();
    } else if (state.autoPlayTimer) {
      clearTimeout(state.autoPlayTimer);
      state.autoPlayTimer = null;
    }
  }

  btnAutoplay.addEventListener('click', toggleAutoPlay);

  /* -------------------------------------------------------------
     ALTERNADOR DE MODO (TELA DO JOGO 2D vs SLIDE CANVA ORIGINAL)
     ------------------------------------------------------------- */
  function toggleViewMode() {
    window.GameAudio.playClick();
    const isNowCanva = document.body.classList.toggle('mode-canva');
    if (btnToggleView) {
      btnToggleView.textContent = isNowCanva ? '📄 SLIDE CANVA' : '🎮 TELA DO JOGO';
      btnToggleView.classList.toggle('active', !isNowCanva);
    }
  }

  if (btnToggleView) {
    btnToggleView.addEventListener('click', toggleViewMode);
  }

  /* -------------------------------------------------------------
     BOTÃO "COMEÇAR APRESENTAÇÃO" NO SLIDE 1
     ------------------------------------------------------------- */
  const btnStartCover = document.getElementById('btn-start-presentation');
  if (btnStartCover) {
    btnStartCover.addEventListener('click', () => {
      window.GameAudio.playClick();
      if (!window.GameAudio.isPlayingBgm) {
        btnBgm.click();
      }
      nextSlide();
    });
  }

  /* -------------------------------------------------------------
     BOTÕES DE NAVEGAÇÃO ANTERIOR / PRÓXIMO
     ------------------------------------------------------------- */
  btnPrev.addEventListener('click', prevSlide);
  btnNext.addEventListener('click', nextSlide);

  // Sons de Hover em todos os botões e cards
  document.querySelectorAll('button, .team-card, .zoom-hotspot, .indicator-pip').forEach(el => {
    el.addEventListener('mouseenter', () => window.GameAudio.playHover());
  });

  /* -------------------------------------------------------------
     ATALHOS DE TECLADO INTUITIVOS
     ------------------------------------------------------------- */
  window.addEventListener('keydown', (e) => {
    // Se o inspetor ou modal estiver aberto, ESC fecha
    if (e.key === 'Escape') {
      if (inspectorModal.classList.contains('open')) {
        closeInspector();
        return;
      }
      if (overviewModal.classList.contains('open')) {
        closeOverview();
        return;
      }
      if (state.zoomLevel > 1) {
        resetZoom();
        return;
      }
    }

    // Se estiver digitando em input, ignora
    if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;

    switch (e.key) {
      case 'ArrowRight':
      case ' ':
      case 'PageDown':
      case 'Enter':
        e.preventDefault();
        nextSlide();
        break;

      case 'ArrowLeft':
      case 'Backspace':
      case 'PageUp':
        e.preventDefault();
        prevSlide();
        break;

      case 'Home':
        e.preventDefault();
        goToSlide(1);
        break;

      case 'End':
        e.preventDefault();
        goToSlide(state.totalSlides);
        break;

      case 'f':
      case 'F':
        toggleFullscreen();
        break;

      case 'm':
      case 'M':
        btnMute.click();
        break;

      case 'b':
      case 'B':
        btnBgm.click();
        break;

      case 'o':
      case 'O':
        if (overviewModal.classList.contains('open')) {
          closeOverview();
        } else {
          openOverview();
        }
        break;

      case 'z':
      case 'Z':
        if (state.zoomLevel > 1) {
          resetZoom();
        } else {
          setZoom(1.8);
        }
        break;

      case 'l':
      case 'L':
        toggleMagnifier();
        break;

      case 'a':
      case 'A':
        toggleAutoPlay();
        break;

      case 'v':
      case 'V':
        toggleViewMode();
        break;

      case '+':
      case '=':
        setZoom(state.zoomLevel + 0.35);
        break;

      case '-':
      case '_':
        setZoom(state.zoomLevel - 0.35);
        break;

      case '0':
        resetZoom();
        break;
    }
  });

  /* -------------------------------------------------------------
     SISTEMA DE PARTÍCULAS / VAGALUMES & NÉVOA DO IGUAÇU
     ------------------------------------------------------------- */
  function initParticles() {
    if (!particleCanvas) return;
    particleCtx = particleCanvas.getContext('2d');

    const resize = () => {
      particleCanvas.width = window.innerWidth;
      particleCanvas.height = window.innerHeight;
    };
    resize();
    window.addEventListener('resize', resize);

    particles = [];
    const count = Math.min(65, Math.floor(window.innerWidth / 28));

    for (let i = 0; i < count; i++) {
      particles.push({
        x: Math.random() * particleCanvas.width,
        y: Math.random() * particleCanvas.height,
        size: Math.random() * 2.5 + 1.2,
        speedX: (Math.random() - 0.5) * 0.45,
        speedY: (Math.random() - 0.5) * 0.35 - 0.1,
        alpha: Math.random() * 0.6 + 0.2,
        pulseSpeed: Math.random() * 0.03 + 0.01,
        color: Math.random() > 0.4 ? 'rgba(226, 183, 69,' : 'rgba(77, 238, 234,'
      });
    }

    function animateParticles() {
      particleCtx.clearRect(0, 0, particleCanvas.width, particleCanvas.height);

      particles.forEach(p => {
        p.x += p.speedX;
        p.y += p.speedY;

        // Efeito suave de repulsão do mouse
        const dx = mousePos.x - p.x;
        const dy = mousePos.y - p.y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < 120) {
          p.x -= (dx / dist) * 0.8;
          p.y -= (dy / dist) * 0.8;
        }

        // Wrap nas bordas
        if (p.x < 0) p.x = particleCanvas.width;
        if (p.x > particleCanvas.width) p.x = 0;
        if (p.y < 0) p.y = particleCanvas.height;
        if (p.y > particleCanvas.height) p.y = 0;

        p.alpha += Math.sin(Date.now() * p.pulseSpeed * 0.05) * 0.015;
        p.alpha = Math.max(0.1, Math.min(0.85, p.alpha));

        particleCtx.beginPath();
        particleCtx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
        particleCtx.fillStyle = `${p.color}${p.alpha})`;
        particleCtx.shadowBlur = 10;
        particleCtx.shadowColor = p.color === 'rgba(226, 183, 69,' ? '#e2b745' : '#4deeea';
        particleCtx.fill();
        particleCtx.shadowBlur = 0;
      });

      requestAnimationFrame(animateParticles);
    }

    animateParticles();
  }

  // Inicializa tudo
  buildIndicators();
  initParticles();
  goToSlide(1);
});

