<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="CONJADEM 2026 — Dia 15 de agosto de 2026. Congresso de jovens. Pré-inscrição Juventude Luziânia."
    />
    <title>Pré-inscrição | CONJADEM 2026</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="/styles.css" />
  </head>
  <body data-inscricao-api="{{ url('/api/inscricoes') }}">
    <header class="site-header">
      <div class="container header-inner">
        <a href="#topo" class="header-logo" aria-label="Início — CONJADEM">
          <img class="header-logo__img" src="/comjadem.png" alt="COMJADEM" />
        </a>
        <nav class="header-nav" aria-label="Menu principal">
          <a href="#sobre">Sobre</a>
          <a href="#local">Local</a>
        </nav>
        <div class="header-actions">
          <a href="{{ route('admin.login') }}" class="btn btn--header-outline">Login Admin</a>
          <a href="#inscricao" class="btn btn--header-outline">Inscrições</a>
        </div>
      </div>
    </header>

    <main>
      <section class="hero" id="topo">
        <div class="hero__bg" aria-hidden="true">
          <img
            class="hero__img"
            src="https://images.unsplash.com/photo-1470229722913-7c0e33263637?auto=format&fit=crop&w=1920&q=80"
            alt=""
            width="1920"
            height="1080"
            fetchpriority="high"
          />
          <div class="hero__overlay"></div>
        </div>
        <div class="hero__main">
          <div class="container hero__content">
            <h1 class="hero__title-main"><span class="hero__brand">COMJADEM - ADEL</span></h1>
            <span class="hero__rule" aria-hidden="true"></span>
            <p class="hero__date">Dia <strong>15</strong> de Agosto de 2026</p>
            <p class="hero__cta-line">
              <a href="#inscricao" class="btn btn--hero-white">Faça sua pré-inscrição</a>
            </p>
          </div>
        </div>
        <div
          class="hero__countdown"
          id="eventCountdown"
          data-target="2026-08-15T00:00:00-03:00"
          aria-label="Contagem regressiva para o evento"
        >
          <div class="container hero__countdown-inner">
            <div class="cd-unit">
              <span class="cd-num" id="cd-days">0</span>
              <span class="cd-label">Dias</span>
            </div>
            <div class="cd-unit">
              <span class="cd-num" id="cd-hours">0</span>
              <span class="cd-label">Horas</span>
            </div>
            <div class="cd-unit">
              <span class="cd-num" id="cd-mins">0</span>
              <span class="cd-label">Min</span>
            </div>
            <div class="cd-unit">
              <span class="cd-num" id="cd-secs">0</span>
              <span class="cd-label">Seg</span>
            </div>
          </div>
        </div>
      </section>

      <section class="section section--about" id="sobre" aria-labelledby="sobre-heading">
        <div class="container about-solo">
          <h2 class="section-title">Sobre</h2>
          <p class="about-solo__text">
            O COMJADEM — Congresso Mundial de Jovens e Adolescentes da Assembleia de Deus Ministério
            de Madureira — alcança sua 10ª edição deixando mais uma marca inesquecível. Conhecido
            como o maior encontro pentecostal do planeta, o evento conecta uma geração apaixonada
            por Deus, cheia da presença do Espírito Santo e comprometida em viver os propósitos do
            Seu Reino.
          </p>
          <p class="about-solo__tagline">🔥 COMJADEM 10ª Edição. Uma geração, um propósito, um só fogo.</p>
        </div>
      </section>

      <section class="section section--anchor section--anchor-alt" id="local">
        <div class="container anchor-section">
          <h2 class="section-title">Local</h2>
          <p class="anchor-section__text anchor-section__address">
            <strong>Arena Hall</strong><br />
            Brasília — Colônia Agrícola Vicente Pires
          </p>
          <p class="anchor-section__links">
            <a
              href="https://www.google.com/maps/search/?api=1&query=Arena+Hall+Bras%C3%ADlia+Col%C3%B4nia+Agr%C3%ADcola+Vicente+Pires"
              class="anchor-section__link"
              target="_blank"
              rel="noopener noreferrer"
              >Ver no mapa</a
            >
          </p>
        </div>
      </section>

      <section class="section section--benefits" id="beneficios">
        <div class="container">
          <div class="section-head">
            <span class="section-tag">O que esperar</span>
            <h2 class="section-title">Experiências que ficam marcadas</h2>
          </div>
          <div class="benefits-grid">
            <article class="benefit-card"><h3>Ambiente de unidade</h3><p>Um lugar onde diferenças se encontram na mesma fé e no mesmo amor.</p></article>
            <article class="benefit-card"><h3>Palavra transformadora</h3><p>Mensagens diretas ao ponto, com profundidade bíblica e linguagem atual.</p></article>
            <article class="benefit-card"><h3>Momentos de adoração</h3><p>Espaço pra elevar o coração, silenciar o ruído e encontrar presença real.</p></article>
            <article class="benefit-card"><h3>Conexões reais</h3><p>Amizades que vão além do evento — comunidade que caminha junto.</p></article>
          </div>
        </div>
      </section>

      <section class="section section--form" id="inscricao">
        <div class="container">
          <div class="form-layout">
            <div class="form-intro">
              <span class="section-tag">Pré-inscrição</span>
              <h2 class="section-title">Garanta sua vaga</h2>
            </div>
            <div class="form-panel">
              <div class="form-panel__glow" aria-hidden="true"></div>
              <form class="pre-form" id="preForm" novalidate>
                <div class="form-row"><label class="field"><span class="field__label">Nome</span><input type="text" name="nome" required /></label></div>
                <div class="form-row form-row--2">
                  <label class="field"><span class="field__label">Idade</span><input type="number" name="idade" required min="10" max="120" /></label>
                  <label class="field"><span class="field__label">WhatsApp</span><input type="tel" name="whatsapp" required maxlength="15" inputmode="numeric" placeholder="(99) 99999-9999" pattern="\(\d{2}\)\s\d{5}-\d{4}" /></label>
                </div>
                <div class="form-row">
                  <label class="field">
                    <span class="field__label">Tamanho da camiseta</span>
                    <select name="tamanho_camiseta" class="field__select" required>
                      <option value="" disabled selected>Selecione o tamanho…</option>
                      @foreach (\App\Models\PreInscricao::tamanhoCamisetaOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                      @endforeach
                    </select>
                  </label>
                </div>
                <div class="form-row">
                  <label class="field">
                    <span class="field__label">Igreja</span>
                    @if ($igrejas->isEmpty())
                      <select name="igreja_id" id="igreja_id" class="field__select" disabled>
                        <option value="">Nenhuma igreja cadastrada ainda</option>
                      </select>
                      <span class="field__hint">As igrejas disponíveis são cadastradas pela equipe no painel administrativo.</span>
                    @else
                      <select name="igreja_id" id="igreja_id" class="field__select" required>
                        <option value="" disabled selected>Selecione sua igreja…</option>
                        @foreach ($igrejas as $igreja)
                          <option value="{{ $igreja->id }}">{{ $igreja->nomeNoFormulario() }}</option>
                        @endforeach
                      </select>
                    @endif
                  </label>
                </div>
                <fieldset class="field field--radio">
                  <legend class="field__label">Líder de jovens?</legend>
                  <div class="radio-group">
                    <label class="radio"><input type="radio" name="lider" value="sim" required /><span>Sim</span></label>
                    <label class="radio"><input type="radio" name="lider" value="nao" /><span>Não</span></label>
                  </div>
                </fieldset>
                <button type="submit" class="btn btn--lg btn--primary btn--block" @if($igrejas->isEmpty()) disabled @endif>Garantir minha vaga</button>
              </form>
              <div class="form-success" id="formSuccess" hidden role="status" tabindex="-1">
                <h3>Inscrição recebida!</h3>
                <p>Logo entraremos em contato. Fica ligado no WhatsApp.</p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <div class="success-modal" id="successModal" hidden aria-hidden="true">
      <div class="success-modal__backdrop" data-close-modal="true"></div>
      <section class="success-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="successModalTitle">
        <h3 id="successModalTitle">Inscrição concluída!</h3>
        <p>Você vai receber no WhatsApp mais detalhes sobre os próximos passos.</p>
        <button type="button" class="btn btn--primary btn--sm" id="successModalClose">Entendi</button>
      </section>
    </div>

    <footer class="site-footer">
      <div class="container footer-inner">
        <p class="footer-social">
          <a href="https://www.instagram.com/comjademoficial/" class="footer-social__link" target="_blank" rel="noopener noreferrer">Instagram @comjademoficial</a>
        </p>
        <p class="footer-copy">© <span id="year"></span> — JUVENTUDE+ LUZIÂNIA. Todos os direitos reservados.</p>
      </div>
    </footer>

    <script src="/script.js"></script>
  </body>
</html>
