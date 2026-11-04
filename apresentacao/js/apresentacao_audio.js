/**
 * ============================================================================
 * LENDAS DO VALE - MOTOR DE ÁUDIO PROCEDURAL (Web Audio API)
 * Síntese em tempo real: BGM de aventura 2D, efeitos sonoros (SFX) e
 * assinaturas auditivas dos personagens e lendas do Vale do Iguaçu.
 * ============================================================================
 */

class SoundEngine {
  constructor() {
    this.ctx = null;
    this.masterGain = null;
    this.bgmGain = null;
    this.sfxGain = null;
    this.isMuted = false;
    this.isBgmMuted = false;
    this.isPlayingBgm = false;
    this.bgmTimer = null;
    this.bgmStep = 0;
  }

  init() {
    if (this.ctx) return;
    try {
      const AudioCtx = window.AudioContext || window.webkitAudioContext;
      this.ctx = new AudioCtx();

      this.masterGain = this.ctx.createGain();
      this.masterGain.gain.setValueAtTime(0.75, this.ctx.currentTime);
      this.masterGain.connect(this.ctx.destination);

      this.bgmGain = this.ctx.createGain();
      this.bgmGain.gain.setValueAtTime(0.28, this.ctx.currentTime);
      this.bgmGain.connect(this.masterGain);

      this.sfxGain = this.ctx.createGain();
      this.sfxGain.gain.setValueAtTime(0.65, this.ctx.currentTime);
      this.sfxGain.connect(this.masterGain);
    } catch (e) {
      console.warn("Web Audio API não inicializada:", e);
    }
  }

  ensureContext() {
    if (!this.ctx) this.init();
    if (this.ctx && this.ctx.state === 'suspended') {
      this.ctx.resume();
    }
  }

  setMasterVolume(val) {
    this.ensureContext();
    if (this.masterGain) {
      this.masterGain.gain.setTargetAtTime(val, this.ctx.currentTime, 0.05);
    }
  }

  toggleMute() {
    this.ensureContext();
    this.isMuted = !this.isMuted;
    if (this.masterGain) {
      this.masterGain.gain.setTargetAtTime(this.isMuted ? 0 : 0.75, this.ctx.currentTime, 0.05);
    }
    return this.isMuted;
  }

  toggleBgm() {
    this.ensureContext();
    if (this.isPlayingBgm) {
      this.stopBgm();
      return false;
    } else {
      this.startBgm();
      return true;
    }
  }

  /* -------------------------------------------------------------
     EFEITOS SONOROS DE NAVEGAÇÃO E HUD
     ------------------------------------------------------------- */
  playHover() {
    this.ensureContext();
    if (!this.ctx || this.isMuted) return;

    const osc = this.ctx.createOscillator();
    const gain = this.ctx.createGain();
    const t = this.ctx.currentTime;

    osc.type = 'sine';
    osc.frequency.setValueAtTime(540, t);
    osc.frequency.exponentialRampToValueAtTime(780, t + 0.06);

    gain.gain.setValueAtTime(0.08, t);
    gain.gain.exponentialRampToValueAtTime(0.001, t + 0.06);

    osc.connect(gain);
    gain.connect(this.sfxGain);

    osc.start(t);
    osc.stop(t + 0.06);
  }

  playClick() {
    this.ensureContext();
    if (!this.ctx || this.isMuted) return;

    const t = this.ctx.currentTime;
    const osc = this.ctx.createOscillator();
    const gain = this.ctx.createGain();

    osc.type = 'triangle';
    osc.frequency.setValueAtTime(440, t);
    osc.frequency.exponentialRampToValueAtTime(880, t + 0.09);

    gain.gain.setValueAtTime(0.2, t);
    gain.gain.exponentialRampToValueAtTime(0.001, t + 0.1);

    osc.connect(gain);
    gain.connect(this.sfxGain);

    osc.start(t);
    osc.stop(t + 0.1);
  }

  playSlideChange() {
    this.ensureContext();
    if (!this.ctx || this.isMuted) return;

    const t = this.ctx.currentTime;
    // Som de swoosh atmosférico com sino suave
    const osc1 = this.ctx.createOscillator();
    const osc2 = this.ctx.createOscillator();
    const gain = this.ctx.createGain();

    osc1.type = 'sine';
    osc1.frequency.setValueAtTime(320, t);
    osc1.frequency.exponentialRampToValueAtTime(640, t + 0.18);

    osc2.type = 'triangle';
    osc2.frequency.setValueAtTime(640, t);
    osc2.frequency.exponentialRampToValueAtTime(1280, t + 0.22);

    gain.gain.setValueAtTime(0.18, t);
    gain.gain.exponentialRampToValueAtTime(0.001, t + 0.25);

    osc1.connect(gain);
    osc2.connect(gain);
    gain.connect(this.sfxGain);

    osc1.start(t);
    osc2.start(t);
    osc1.stop(t + 0.25);
    osc2.stop(t + 0.25);
  }

  playZoomIn() {
    this.ensureContext();
    if (!this.ctx || this.isMuted) return;

    const t = this.ctx.currentTime;
    const osc = this.ctx.createOscillator();
    const filter = this.ctx.createBiquadFilter();
    const gain = this.ctx.createGain();

    osc.type = 'sawtooth';
    osc.frequency.setValueAtTime(150, t);
    osc.frequency.exponentialRampToValueAtTime(520, t + 0.28);

    filter.type = 'lowpass';
    filter.frequency.setValueAtTime(400, t);
    filter.frequency.exponentialRampToValueAtTime(3000, t + 0.28);

    gain.gain.setValueAtTime(0.2, t);
    gain.gain.exponentialRampToValueAtTime(0.001, t + 0.3);

    osc.connect(filter);
    filter.connect(gain);
    gain.connect(this.sfxGain);

    osc.start(t);
    osc.stop(t + 0.3);
  }

  playZoomOut() {
    this.ensureContext();
    if (!this.ctx || this.isMuted) return;

    const t = this.ctx.currentTime;
    const osc = this.ctx.createOscillator();
    const gain = this.ctx.createGain();

    osc.type = 'sine';
    osc.frequency.setValueAtTime(600, t);
    osc.frequency.exponentialRampToValueAtTime(200, t + 0.22);

    gain.gain.setValueAtTime(0.16, t);
    gain.gain.exponentialRampToValueAtTime(0.001, t + 0.22);

    osc.connect(gain);
    gain.connect(this.sfxGain);

    osc.start(t);
    osc.stop(t + 0.22);
  }

  /* -------------------------------------------------------------
     ASSINATURAS SONORAS TEMÁTICAS DOS PERSONAGENS E BOSSES
     ------------------------------------------------------------- */

  // 1. GAÚCHO (Gaita com galope alegre 8-bit)
  playGauchoTheme() {
    this.ensureContext();
    if (!this.ctx || this.isMuted) return;

    const notes = [293.66, 369.99, 440.00, 587.33]; // D, F#, A, D
    const t = this.ctx.currentTime;

    notes.forEach((freq, idx) => {
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const startTime = t + (idx * 0.09);

      osc.type = 'sawtooth';
      osc.frequency.setValueAtTime(freq, startTime);

      gain.gain.setValueAtTime(0.18, startTime);
      gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.35);

      osc.connect(gain);
      gain.connect(this.sfxGain);

      osc.start(startTime);
      osc.stop(startTime + 0.35);
    });
  }

  // 2. FANTASMA (Ressonância etérea misteriosa)
  playFantasmaTheme() {
    this.ensureContext();
    if (!this.ctx || this.isMuted) return;

    const t = this.ctx.currentTime;
    const osc = this.ctx.createOscillator();
    const lfo = this.ctx.createOscillator();
    const lfoGain = this.ctx.createGain();
    const gain = this.ctx.createGain();

    lfo.frequency.value = 5.5; // Vibrato
    lfoGain.gain.value = 35;
    lfo.connect(osc.frequency);

    osc.type = 'sine';
    osc.frequency.setValueAtTime(650, t);
    osc.frequency.exponentialRampToValueAtTime(420, t + 1.2);

    gain.gain.setValueAtTime(0.01, t);
    gain.gain.linearRampToValueAtTime(0.24, t + 0.2);
    gain.gain.exponentialRampToValueAtTime(0.001, t + 1.4);

    lfo.start(t);
    osc.start(t);
    lfo.stop(t + 1.4);
    osc.stop(t + 1.4);

    osc.connect(gain);
    gain.connect(this.sfxGain);
  }

  // 3. SOLDADO (Toque de corneta marcial)
  playSoldadoTheme() {
    this.ensureContext();
    if (!this.ctx || this.isMuted) return;

    const melody = [
      { f: 392, d: 0.12 }, // G
      { f: 523.25, d: 0.12 }, // C
      { f: 659.25, d: 0.14 }, // E
      { f: 783.99, d: 0.35 }  // G alto
    ];
    let offset = 0;
    const t = this.ctx.currentTime;

    melody.forEach(item => {
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      const st = t + offset;

      osc.type = 'triangle';
      osc.frequency.setValueAtTime(item.f, st);

      gain.gain.setValueAtTime(0.22, st);
      gain.gain.exponentialRampToValueAtTime(0.001, st + item.d + 0.1);

      osc.connect(gain);
      gain.connect(this.sfxGain);

      osc.start(st);
      osc.stop(st + item.d + 0.1);
      offset += item.d + 0.04;
    });
  }

  // 4. MAQUINISTA E MARIA FUMAÇA (Apito a vapor duplo e caldeira)
  playMariaFumacaTheme() {
    this.ensureContext();
    if (!this.ctx || this.isMuted) return;

    const t = this.ctx.currentTime;
    [0, 0.25].forEach(stOffset => {
      const st = t + stOffset;
      const osc1 = this.ctx.createOscillator();
      const osc2 = this.ctx.createOscillator();
      const gain = this.ctx.createGain();

      osc1.type = 'sawtooth';
      osc2.type = 'triangle';
      osc1.frequency.setValueAtTime(415, st); // Som característico do apito a vapor
      osc2.frequency.setValueAtTime(520, st);

      gain.gain.setValueAtTime(0.25, st);
      gain.gain.exponentialRampToValueAtTime(0.001, st + 0.22);

      osc1.connect(gain);
      osc2.connect(gain);
      gain.connect(this.sfxGain);

      osc1.start(st);
      osc2.start(st);
      osc1.stop(st + 0.22);
      osc2.stop(st + 0.22);
    });
  }

  // 5. MONGE JOÃO MARIA (Sino budista sagrado e oração)
  playMongeTheme() {
    this.ensureContext();
    if (!this.ctx || this.isMuted) return;

    const t = this.ctx.currentTime;
    const fundamentals = [220, 440, 660, 1100]; // Harmônicos de sino de bronze

    fundamentals.forEach((freq, idx) => {
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();

      osc.type = 'sine';
      osc.frequency.setValueAtTime(freq, t);

      const amp = 0.2 / (idx + 1);
      gain.gain.setValueAtTime(amp, t);
      gain.gain.exponentialRampToValueAtTime(0.0005, t + 2.2);

      osc.connect(gain);
      gain.connect(this.sfxGain);

      osc.start(t);
      osc.stop(t + 2.2);
    });
  }

  // 6. DIABO DO CLUBE FLORESTA (Riso demoníaco grave e FM ríspido)
  playDiaboTheme() {
    this.ensureContext();
    if (!this.ctx || this.isMuted) return;

    const t = this.ctx.currentTime;
    const notes = [140, 130, 115, 95]; // Risada descendo

    notes.forEach((freq, idx) => {
      const osc = this.ctx.createOscillator();
      const mod = this.ctx.createOscillator();
      const modGain = this.ctx.createGain();
      const gain = this.ctx.createGain();
      const st = t + (idx * 0.16);

      mod.frequency.value = 45;
      modGain.gain.value = 60;
      mod.connect(osc.frequency);

      osc.type = 'sawtooth';
      osc.frequency.setValueAtTime(freq, st);

      gain.gain.setValueAtTime(0.25, st);
      gain.gain.exponentialRampToValueAtTime(0.001, st + 0.18);

      osc.connect(gain);
      gain.connect(this.sfxGain);

      mod.start(st);
      osc.start(st);
      mod.stop(st + 0.18);
      osc.stop(st + 0.18);
    });
  }

  // 7. COBRA DO RIO IGUAÇU (Estrondo de águas profundas e sibilo da serpente)
  playCobraTheme() {
    this.ensureContext();
    if (!this.ctx || this.isMuted) return;

    const t = this.ctx.currentTime;

    // Sub-bass rumble
    const subOsc = this.ctx.createOscillator();
    const subGain = this.ctx.createGain();
    subOsc.type = 'triangle';
    subOsc.frequency.setValueAtTime(55, t);
    subOsc.frequency.linearRampToValueAtTime(38, t + 1.2);

    subGain.gain.setValueAtTime(0.35, t);
    subGain.gain.exponentialRampToValueAtTime(0.001, t + 1.2);

    subOsc.connect(subGain);
    subGain.connect(this.sfxGain);

    subOsc.start(t);
    subOsc.stop(t + 1.2);

    // Hiss de ruído da serpente
    const bufferSize = this.ctx.sampleRate * 0.8;
    const noiseBuffer = this.ctx.createBuffer(1, bufferSize, this.ctx.sampleRate);
    const output = noiseBuffer.getChannelData(0);
    for (let i = 0; i < bufferSize; i++) {
      output[i] = Math.random() * 2 - 1;
    }

    const whiteNoise = this.ctx.createBufferSource();
    whiteNoise.buffer = noiseBuffer;

    const filter = this.ctx.createBiquadFilter();
    filter.type = 'bandpass';
    filter.frequency.setValueAtTime(2800, t);
    filter.Q.value = 4.0;

    const noiseGain = this.ctx.createGain();
    noiseGain.gain.setValueAtTime(0.12, t);
    noiseGain.gain.exponentialRampToValueAtTime(0.001, t + 0.8);

    whiteNoise.connect(filter);
    filter.connect(noiseGain);
    noiseGain.connect(this.sfxGain);

    whiteNoise.start(t);
    whiteNoise.stop(t + 0.8);
  }

  /* -------------------------------------------------------------
     TRILHA SONORA PROCEDURAL (BGM - "A LENDA DO VALE")
     Arpejos atmosféricos em estilo Chiptune/16-bit RPG relaxante
     ------------------------------------------------------------- */
  startBgm() {
    if (this.isPlayingBgm) return;
    this.ensureContext();
    this.isPlayingBgm = true;

    // Escala menor mágica: D, F, G, A, C, D
    const melodyPattern = [
      293.66, 349.23, 392.00, 440.00,
      523.25, 440.00, 392.00, 349.23,
      261.63, 329.63, 392.00, 440.00,
      392.00, 329.63, 293.66, 220.00
    ];

    const bassPattern = [
      146.83, 146.83, 174.61, 174.61,
      130.81, 130.81, 110.00, 110.00
    ];

    const stepDuration = 0.26; // Segundos por nota

    const tick = () => {
      if (!this.isPlayingBgm || !this.ctx) return;

      const t = this.ctx.currentTime;
      const noteFreq = melodyPattern[this.bgmStep % melodyPattern.length];
      const bassFreq = bassPattern[Math.floor(this.bgmStep / 2) % bassPattern.length];

      // Nota Melódica
      const osc = this.ctx.createOscillator();
      const gain = this.ctx.createGain();
      osc.type = 'square';
      osc.frequency.setValueAtTime(noteFreq, t);

      gain.gain.setValueAtTime(0.035, t);
      gain.gain.exponentialRampToValueAtTime(0.001, t + stepDuration * 0.9);

      // Filtro Low-Pass para deixar o som macio e nostálgico
      const filter = this.ctx.createBiquadFilter();
      filter.type = 'lowpass';
      filter.frequency.setValueAtTime(1400, t);

      osc.connect(filter);
      filter.connect(gain);
      gain.connect(this.bgmGain);

      osc.start(t);
      osc.stop(t + stepDuration * 0.9);

      // Baixo (a cada 2 steps)
      if (this.bgmStep % 2 === 0) {
        const bassOsc = this.ctx.createOscillator();
        const bassGain = this.ctx.createGain();
        bassOsc.type = 'triangle';
        bassOsc.frequency.setValueAtTime(bassFreq, t);

        bassGain.gain.setValueAtTime(0.08, t);
        bassGain.gain.exponentialRampToValueAtTime(0.001, t + stepDuration * 1.8);

        bassOsc.connect(bassGain);
        bassGain.connect(this.bgmGain);

        bassOsc.start(t);
        bassOsc.stop(t + stepDuration * 1.8);
      }

      this.bgmStep++;
      this.bgmTimer = setTimeout(tick, stepDuration * 1000);
    };

    tick();
  }

  stopBgm() {
    this.isPlayingBgm = false;
    if (this.bgmTimer) {
      clearTimeout(this.bgmTimer);
      this.bgmTimer = null;
    }
  }
}

// Instância global do motor de som
window.GameAudio = new SoundEngine();
