<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cadastro de Membros | CONJADEM</title>
    <link rel="stylesheet" href="/styles.css" />
    <style>
      .member-page {
        min-height: 100vh;
        padding: 5.5rem 0 3rem;
      }

      .member-panel {
        max-width: 760px;
        margin: 0 auto;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: clamp(1.2rem, 3vw, 2rem);
      }

      .member-panel h1 {
        margin: 0 0 0.5rem;
        font-size: clamp(1.6rem, 4vw, 2.2rem);
      }

      .member-panel p {
        color: var(--text-muted);
        margin-top: 0;
      }

      .member-alert {
        border-radius: 12px;
        padding: 0.7rem 0.85rem;
        margin-bottom: 1rem;
        font-size: 0.95rem;
      }

      .member-alert--success {
        background: rgba(46, 230, 214, 0.12);
        border: 1px solid rgba(46, 230, 214, 0.35);
        color: #b8fff8;
      }

      .member-alert--error {
        background: rgba(255, 76, 76, 0.12);
        border: 1px solid rgba(255, 76, 76, 0.35);
        color: #ffdada;
      }

      .member-form {
        display: grid;
        gap: 1rem;
      }

      .member-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: 1fr 1fr;
      }

      .member-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
      }

      .password-toggle-wrap {
        position: relative;
      }

      .password-toggle-wrap input {
        padding-right: 2.8rem;
      }

      .password-toggle-btn {
        position: absolute;
        top: 50%;
        right: 0.4rem;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: var(--text-muted);
        width: 2rem;
        height: 2rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        cursor: pointer;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
      }

      .password-toggle-btn:hover {
        color: var(--text);
        background: rgba(255, 255, 255, 0.08);
      }

      .member-back {
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #fff;
        padding: 0.75rem 1rem;
        border-radius: 999px;
      }

      @media (max-width: 700px) {
        .member-grid {
          grid-template-columns: 1fr;
        }
      }
    </style>
  </head>
  <body>
    <main class="member-page">
      <div class="container">
        <section class="member-panel" aria-labelledby="cadastro-membros-heading">
          <h1 id="cadastro-membros-heading">Cadastro de Membros</h1>
          <p>Preencha os dados abaixo para enviar seu cadastro.</p>

          @if (session('success'))
            <div class="member-alert member-alert--success">{{ session('success') }}</div>
          @endif

          @if ($errors->any())
            <div class="member-alert member-alert--error">
              Verifique os dados informados.
            </div>
          @endif

          <form class="member-form" method="post" action="{{ route('membros.publico.store') }}">
            @csrf
            <label class="field">
              <span class="field__label">Nome</span>
              <input type="text" name="nome" value="{{ old('nome') }}" required maxlength="255" />
              @error('nome')
                <span class="field__hint">{{ $message }}</span>
              @enderror
            </label>

            <div class="member-grid">
              <label class="field">
                <span class="field__label">Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required maxlength="255" />
                @error('email')
                  <span class="field__hint">{{ $message }}</span>
                @enderror
              </label>

              <label class="field">
                <span class="field__label">Telefone</span>
                <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}" required maxlength="15" inputmode="numeric" placeholder="(99) 99999-9999" />
                @error('telefone')
                  <span class="field__hint">{{ $message }}</span>
                @enderror
              </label>
            </div>

            <div class="member-grid">
              <label class="field">
                <span class="field__label">Senha</span>
                <input type="password" name="senha" required minlength="6" maxlength="255" />
                @error('senha')
                  <span class="field__hint">{{ $message }}</span>
                @enderror
              </label>

              <label class="field">
                <span class="field__label">Cargo</span>
                @if ($cargos->isEmpty())
                  <select name="cargo_id" class="field__select" disabled>
                    <option value="">Nenhum cargo disponível</option>
                  </select>
                  <span class="field__hint">Não há cargos cadastrados no momento.</span>
                @else
                  <select name="cargo_id" class="field__select" required>
                    <option value="" disabled @selected(old('cargo_id') === null)>Selecione um cargo</option>
                    @foreach ($cargos as $cargo)
                      <option value="{{ $cargo->id }}" @selected((string) old('cargo_id') === (string) $cargo->id)>
                        {{ $cargo->nome }}
                      </option>
                    @endforeach
                  </select>
                @endif
                @error('cargo_id')
                  <span class="field__hint">{{ $message }}</span>
                @enderror
              </label>
            </div>

            <div class="member-actions">
              <button type="submit" class="btn btn--primary btn--lg" @if ($cargos->isEmpty()) disabled @endif>Enviar cadastro</button>
              <a href="{{ url('/') }}" class="member-back">Voltar para início</a>
            </div>
          </form>
        </section>
      </div>
    </main>

    <script>
      (function () {
        var input = document.getElementById('telefone');
        if (!input) return;

        function formatPhone(value) {
          var digits = (value || '').replace(/\D/g, '').slice(0, 11);
          if (!digits) return '';
          if (digits.length <= 2) return '(' + digits;
          if (digits.length <= 7) return '(' + digits.slice(0, 2) + ') ' + digits.slice(2);
          return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 7) + '-' + digits.slice(7);
        }

        input.addEventListener('input', function (event) {
          event.target.value = formatPhone(event.target.value);
        });

        input.value = formatPhone(input.value);
      })();

      (function () {
        var inputs = document.querySelectorAll('input[type="password"]');
        if (!inputs.length) return;

        inputs.forEach(function (input) {
          var wrapper = document.createElement('div');
          wrapper.className = 'password-toggle-wrap';
          input.parentNode.insertBefore(wrapper, input);
          wrapper.appendChild(input);

          var button = document.createElement('button');
          button.type = 'button';
          button.className = 'password-toggle-btn';
          button.setAttribute('aria-label', 'Mostrar senha');
          button.textContent = 'ver';

          button.addEventListener('click', function () {
            var isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            button.setAttribute('aria-label', isPassword ? 'Ocultar senha' : 'Mostrar senha');
            button.textContent = isPassword ? 'ocultar' : 'ver';
          });

          wrapper.appendChild(button);
        });
      })();
    </script>
  </body>
</html>
