<?php

namespace App;

class View {

    protected string $template;

    /**
     * Render the template, returning it's content.
     *
     * @param string $template
     * @param array  $data
     *
     * @return string The rendered template.
     */
    public function render(string $template, array $data): string
    {
        extract($data, EXTR_OVERWRITE);
        ob_start();

        include($template);

        return ob_get_clean();
    }
}
