<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    /**
     * Render view di app/views/<view>.php ke dalam layout utama.
     *
     * @param array<string, mixed> $data
     */
    protected function render(string $view, array $data = [], string $title = ''): void
    {
        $pageTitle = $title;
        extract($data, EXTR_SKIP);

        ob_start();
        require APP_PATH . '/views/' . $view . '.php';
        $content = (string) ob_get_clean();

        require APP_PATH . '/views/layouts/main.php';
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . url($path));
        exit;
    }

    /**
     * Panggil di awal setiap aksi POST.
     */
    protected function verifyCsrf(): void
    {
        if (!csrf_valid()) {
            http_response_code(419);
            exit('Sesi tidak valid. Muat ulang halaman lalu coba lagi.');
        }
    }
}
