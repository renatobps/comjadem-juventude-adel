(function () {
  var header = document.querySelector(".site-header");
  var yearEl = document.getElementById("year");
  var form = document.getElementById("preForm");
  var successEl = document.getElementById("formSuccess");
  var countdownRoot = document.getElementById("eventCountdown");
  var apiUrl = document.body && document.body.getAttribute("data-inscricao-api");

  if (yearEl) {
    yearEl.textContent = String(new Date().getFullYear());
  }

  function onScroll() {
    if (!header) return;
    header.classList.toggle("is-scrolled", window.scrollY > 8);
  }

  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  if (countdownRoot) {
    var targetIso = countdownRoot.getAttribute("data-target") || "2026-08-15T00:00:00-03:00";
    var targetMs = new Date(targetIso).getTime();
    var dEl = document.getElementById("cd-days");
    var hEl = document.getElementById("cd-hours");
    var mEl = document.getElementById("cd-mins");
    var sEl = document.getElementById("cd-secs");

    function tick() {
      var now = Date.now();
      var diff = Math.max(0, targetMs - now);
      var totalSec = Math.floor(diff / 1000);
      var days = Math.floor(totalSec / 86400);
      var hours = Math.floor((totalSec % 86400) / 3600);
      var mins = Math.floor((totalSec % 3600) / 60);
      var secs = totalSec % 60;
      if (dEl) dEl.textContent = String(days);
      if (hEl) hEl.textContent = String(hours);
      if (mEl) mEl.textContent = String(mins);
      if (sEl) sEl.textContent = String(secs);
    }

    tick();
    setInterval(tick, 1000);
  }

  if (!form || !successEl) return;

  var whatsappInput = form.querySelector('input[name="whatsapp"]');

  function formatPhone(value) {
    var digits = String(value || "").replace(/\D/g, "").slice(0, 11);
    if (digits.length === 0) return "";
    if (digits.length <= 2) return "(" + digits;
    if (digits.length <= 7) return "(" + digits.slice(0, 2) + ") " + digits.slice(2);
    return "(" + digits.slice(0, 2) + ") " + digits.slice(2, 7) + "-" + digits.slice(7);
  }

  if (whatsappInput) {
    whatsappInput.addEventListener("input", function () {
      whatsappInput.value = formatPhone(whatsappInput.value);
    });
  }

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    var data = new FormData(form);
    var igrejaIdRaw = data.get("igreja_id");
    var payload = {
      nome: String(data.get("nome") || "").trim(),
      idade: Number(data.get("idade")),
      whatsapp: String(data.get("whatsapp") || "").trim(),
      igreja_id: igrejaIdRaw ? Number(igrejaIdRaw) : null,
      lider: String(data.get("lider") || ""),
    };

    if (!apiUrl) {
      console.warn("Defina data-inscricao-api no <body> apontando para a API Laravel.");
      form.hidden = true;
      successEl.hidden = false;
      successEl.focus({ preventScroll: true });
      return;
    }

    var submitBtn = form.querySelector('[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    fetch(apiUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(payload),
    })
      .then(function (res) {
        return res.json().then(function (body) {
          return { res: res, body: body };
        });
      })
      .then(function (_ref) {
        var res = _ref.res;
        var body = _ref.body;
        if (!res.ok) {
          var msg = (body && body.message) || "Não foi possível enviar.";
          if (body && body.errors) {
            msg = Object.values(body.errors)
              .flat()
              .join(" ");
          }
          window.alert(msg);
          return;
        }
        form.hidden = true;
        successEl.hidden = false;
        successEl.focus({ preventScroll: true });
      })
      .catch(function () {
        window.alert("Erro de rede. Verifique se a API está no ar e o endereço em data-inscricao-api.");
      })
      .finally(function () {
        if (submitBtn) submitBtn.disabled = false;
      });
  });
})();
