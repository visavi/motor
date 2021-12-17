<?php

namespace App;

class View
{
    protected string $template;

    /**
     * Render template
     *
     * @param string $template
     * @param array  $data
     *
     * @return string The rendered template.
     */
    public function render(string $template, array $data): string
    {
        $file = __DIR__ . '/views/' . $template . '.php';

/*        if (! is_readable($file)) {
            throw new \Exception("$file not found");
        }*/

        extract($data, EXTR_SKIP);
        ob_start();

        require $file;

        return ob_get_clean();
    }
}
