(function () {
  'use strict';

  const historias = {
    'diabo-do-baile': {
      titulo: 'Diabo do Baile',
      texto: 'A lenda conta que um forasteiro elegante apareceu em um baile e encantou todos com sua dança. Durante a festa, alguém percebeu que seus pés não eram humanos, mas cascos. Ao ser descoberto, o visitante revelou sua natureza sombria e desapareceu, deixando medo e mistério entre os presentes.'
    },
    'serpente-do-iguacu': {
      titulo: 'Serpente do Rio Iguaçu',
      texto: 'Nas águas profundas do Rio Iguaçu, histórias antigas falam de uma serpente gigantesca que se move sob a correnteza. Sua presença seria anunciada por redemoinhos, águas agitadas e sons vindos do rio. Em Lendas do Vale, a criatura protege os segredos escondidos nas margens do Iguaçu.'
    },
    'monge-joao-maria': {
      titulo: 'Monge João Maria',
      texto: 'João Maria é lembrado no Sul do Brasil como um monge peregrino, conselheiro e curandeiro ligado à Guerra do Contestado. A tradição popular atribui a ele profecias, bênçãos e fontes de águas consideradas sagradas. No jogo, sua figura misteriosa ganha uma interpretação fantástica conectada às forças do Vale.'
    }
  };

  const modal = document.getElementById('chefe-modal');
  const titulo = document.getElementById('chefe-modal-title');
  const historia = document.getElementById('chefe-modal-historia');
  const fechar = modal?.querySelector('.chefe-modal-fechar');

  if (!modal || !titulo || !historia || !fechar) return;

  document.querySelectorAll('[data-chefe]').forEach((card) => {
    card.addEventListener('click', () => {
      const chefe = historias[card.dataset.chefe];
      if (!chefe) return;

      titulo.textContent = chefe.titulo;
      historia.textContent = chefe.texto;
      modal.showModal();
    });
  });

  fechar.addEventListener('click', () => modal.close());

  modal.addEventListener('click', (event) => {
    const area = modal.getBoundingClientRect();
    const foraDoConteudo = event.clientX < area.left || event.clientX > area.right ||
      event.clientY < area.top || event.clientY > area.bottom;
    if (foraDoConteudo) modal.close();
  });
})();
