<?php

namespace src\mietshop\view;

use src\mietshop\contracts\IGlobalView;

class GlobalView implements IGlobalView
{
    private function genBootstrapHeader(string $host)
    {
        return "
        <nav class=\"navbar bg-primary-subtle sticky-top border-bottom border-3 border-primary w-100 z-2\">
	        <div class=\"container-fluid\">
		        <img src=\"{$host}/favicon.png\" alt=\"favicon.png\" width=\"70\" height=\"70\">
                    <button class=\"navbar-toggler\" type=\"button\" data-bs-toggle=\"offcanvas\" data-bs-target=\"#offcanvasNavbar\" aria-controls=\"offcanvasNavbar\" aria-label=\"Toggle navigation\">
                        <span class=\"navbar-toggler-icon\"></span>
                    </button>
                    <div class=\"offcanvas offcanvas-end\" tabindex=\"-1\" id=\"offcanvasNavbar\" aria-labelledby=\"offcanvasNavbarLabel\" role=\"dialog\">
                        <div class=\"offcanvas-header bg-primary-subtle border-bottom border-3 border-primary\">
                            <h5 class=\"offcanvas-title\" id=\"offcanvasNavbarLabel\"><?php echo COMPANY_NAME ?></h5>
                            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"offcanvas\" aria-label=\"Close\"></button>
                        </div>
                        <div class=\"offcanvas-body bg-primary-subtle\">
                            <ul class=\"navbar-nav justify-content-end flex-grow-1 pe-3\">
                                <li class=\"nav-item\">
                                    <a class=\"nav-link active\" aria-current=\"page\" href=\"{$host}\">Startseite 🏠</a>
                                </li>
                                <li class=\"nav-item\">
                                    <a class=\"nav-link\" href=\"{$host}/?view=search\">Jetzt Mietobjekte entdecken 🔎</a>
                                </li>
                                <li class=\"nav-item\">
                                    <a class=\"nav-link\" href=\"{$host}/?view=login\">Anmelden</a>
                                </li>
                                <li class=\"nav-item\">
                                    <a class=\"nav-link\" href=\"{$host}/?view=termsofservice\">AGBs</a>
                                </li>
                                <li class=\"nav-item\">
                                    <a class=\"nav-link\" href=\"{$host}/?view=privacypolicy\">Datenschutzerklärung</a>
                                </li>
                                <li class=\"nav-item\">
                                    <a class=\"nav-link\" href=\"{$host}/?view=imprint\">Impressum</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        ";
    }

    private function genBootstrapFooter(string $host)
    {
        return "
        
        ";
    }

    public function show(array $data = []): void
    {
        $header = $this->genBootstrapHeader($data['host']);

    }
}