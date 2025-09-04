<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* forgot.html.twig */
class __TwigTemplate_8dd77a3c1c6f5016acaad405aeb104d1 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "forgot.html.twig"));

        // line 1
        yield "<!DOCTYPE html>
<html lang=\"pl\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Reset hasła — SkyBroker</title>
    <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
    <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
    <link href=\"https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;800&family=Mulish:wght@400;600;700;800&display=swap\" rel=\"stylesheet\">
    <style>
        :root{--primary:#2F7DFF;--primary-600:#2568d6;--primary-50:#EEF5FF;--text:#0C0212;--muted:#64748b;--border:#e5e7eb;--bg:#fff}
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Mulish',system-ui,-apple-system,'Segoe UI',Roboto;background:var(--bg);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
        h1,.btn{font-family:'Be Vietnam Pro','Mulish',Arial,sans-serif}
        .container{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:0 12px 40px rgba(15,23,42,.08);width:100%;max-width:560px;padding:28px}
        .brand{display:flex;align-items:center;gap:10px;margin-bottom:12px}
        .dot{width:22px;height:22px;border-radius:6px;background:var(--primary)}
        .form-group{margin-bottom:16px}
        label{display:block;margin-bottom:8px;font-weight:600}
        input{width:100%;padding:12px 14px;border:1.5px solid var(--border);border-radius:10px}
        input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 4px var(--primary-50)}
        .btn{width:100%;padding:14px;border-radius:10px;border:1px solid var(--primary);background:var(--primary);color:#fff;font-weight:700}
        .btn:hover{background:var(--primary-600)}
        .links{display:flex;gap:10px;justify-content:space-between;margin-top:10px}
        .alink{color:var(--muted);text-decoration:none}
        .alink:hover{color:var(--primary)}
        .alert{display:none;margin-bottom:12px;padding:12px;border-radius:10px}
        .alert-danger{background:#FEF2F2;color:#b91c1c;border:1px solid #fecaca}
        .alert-success{background:#E8FFF1;color:#0f9d58;border:1px solid #b7f7cd}
    </style>
</head>
<body>
    <div class=\"container\">
        <div class=\"brand\"><div class=\"dot\"></div><strong>SkyBroker</strong></div>
        <h1 style=\"margin-bottom:8px;font-size:1.4em\">Przypomnienie hasła</h1>
        <div id=\"ok\" class=\"alert alert-success\"></div>
        <div id=\"err\" class=\"alert alert-danger\"></div>
        <form id=\"forgotForm\">
            <div class=\"form-group\">
                <label for=\"email\">Adres email</label>
                <input id=\"email\" type=\"email\" required>
            </div>
            <button class=\"btn\" type=\"submit\">Wyślij link resetu</button>
            <div class=\"links\">
                <a class=\"alink\" href=\"/v2/login\">Logowanie</a>
                <a class=\"alink\" href=\"/v2/register\">Rejestracja</a>
                <a class=\"alink\" href=\"/v2/web\">Powrót</a>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('forgotForm').addEventListener('submit', function(e){
            e.preventDefault();
            const email = document.getElementById('email').value.trim();
            const ok=document.getElementById('ok'); const err=document.getElementById('err');
            ok.style.display='none'; err.style.display='none';
            if(!email){ err.textContent='Podaj adres email'; err.style.display='block'; return; }
            // Placeholder — do podlinkowania z API resetu hasła
            ok.textContent='Jeśli adres istnieje w systemie, wyślemy instrukcje resetu.';
            ok.style.display='block';
        });
    </script>
</body>
</html>

";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "forgot.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  45 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html lang=\"pl\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Reset hasła — SkyBroker</title>
    <link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
    <link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
    <link href=\"https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;800&family=Mulish:wght@400;600;700;800&display=swap\" rel=\"stylesheet\">
    <style>
        :root{--primary:#2F7DFF;--primary-600:#2568d6;--primary-50:#EEF5FF;--text:#0C0212;--muted:#64748b;--border:#e5e7eb;--bg:#fff}
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Mulish',system-ui,-apple-system,'Segoe UI',Roboto;background:var(--bg);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px}
        h1,.btn{font-family:'Be Vietnam Pro','Mulish',Arial,sans-serif}
        .container{background:#fff;border:1px solid var(--border);border-radius:16px;box-shadow:0 12px 40px rgba(15,23,42,.08);width:100%;max-width:560px;padding:28px}
        .brand{display:flex;align-items:center;gap:10px;margin-bottom:12px}
        .dot{width:22px;height:22px;border-radius:6px;background:var(--primary)}
        .form-group{margin-bottom:16px}
        label{display:block;margin-bottom:8px;font-weight:600}
        input{width:100%;padding:12px 14px;border:1.5px solid var(--border);border-radius:10px}
        input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 4px var(--primary-50)}
        .btn{width:100%;padding:14px;border-radius:10px;border:1px solid var(--primary);background:var(--primary);color:#fff;font-weight:700}
        .btn:hover{background:var(--primary-600)}
        .links{display:flex;gap:10px;justify-content:space-between;margin-top:10px}
        .alink{color:var(--muted);text-decoration:none}
        .alink:hover{color:var(--primary)}
        .alert{display:none;margin-bottom:12px;padding:12px;border-radius:10px}
        .alert-danger{background:#FEF2F2;color:#b91c1c;border:1px solid #fecaca}
        .alert-success{background:#E8FFF1;color:#0f9d58;border:1px solid #b7f7cd}
    </style>
</head>
<body>
    <div class=\"container\">
        <div class=\"brand\"><div class=\"dot\"></div><strong>SkyBroker</strong></div>
        <h1 style=\"margin-bottom:8px;font-size:1.4em\">Przypomnienie hasła</h1>
        <div id=\"ok\" class=\"alert alert-success\"></div>
        <div id=\"err\" class=\"alert alert-danger\"></div>
        <form id=\"forgotForm\">
            <div class=\"form-group\">
                <label for=\"email\">Adres email</label>
                <input id=\"email\" type=\"email\" required>
            </div>
            <button class=\"btn\" type=\"submit\">Wyślij link resetu</button>
            <div class=\"links\">
                <a class=\"alink\" href=\"/v2/login\">Logowanie</a>
                <a class=\"alink\" href=\"/v2/register\">Rejestracja</a>
                <a class=\"alink\" href=\"/v2/web\">Powrót</a>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('forgotForm').addEventListener('submit', function(e){
            e.preventDefault();
            const email = document.getElementById('email').value.trim();
            const ok=document.getElementById('ok'); const err=document.getElementById('err');
            ok.style.display='none'; err.style.display='none';
            if(!email){ err.textContent='Podaj adres email'; err.style.display='block'; return; }
            // Placeholder — do podlinkowania z API resetu hasła
            ok.textContent='Jeśli adres istnieje w systemie, wyślemy instrukcje resetu.';
            ok.style.display='block';
        });
    </script>
</body>
</html>

", "forgot.html.twig", "/var/www/skybrokersystem/templates/forgot.html.twig");
    }
}
