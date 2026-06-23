<?php

class NoCodeEditorController
{
    /** @var ThemeLoader */
    private $themeLoader;

    public function __construct(ThemeLoader $themeLoader)
    {
        $this->themeLoader = $themeLoader;
    }

    /**
     * Controller entrypoint for both editor wrapper and raw preview render.
     */
    public function handle(array $request)
    {
        if ($request['action'] === 'render') {
            $result = $this->themeLoader->load($request);
            $this->renderThemeHtml($result);
            return;
        }

        $result = $this->themeLoader->inspect($request);

        $this->renderEditorFrame($request, $result);
    }

    private function renderThemeHtml(array $result)
    {
        header('Content-Type: text/html; charset=utf-8');

        if (!$result['success']) {
            http_response_code(404);
            echo '<h1>Preview unavailable</h1>';
            echo '<p>' . htmlspecialchars($result['message'], ENT_QUOTES, 'UTF-8') . '</p>';
            return;
        }

        // Mark response for editor tooling while preserving exact public markup output.
        echo "<!-- no-code-editor:rendered-via-public-router -->\n";
        echo $result['renderedHtml'];
    }

    private function renderEditorFrame(array $request, array $result)
    {
        $previewUrl = '/dashboard/editor/render?url=' . rawurlencode($request['url'])
            . '&type=' . rawurlencode($request['type'])
            . '&id=' . (int)$request['id'];

        $viewFile = __DIR__ . '/../View/editor_layout.php';
        require $viewFile;
    }
}
