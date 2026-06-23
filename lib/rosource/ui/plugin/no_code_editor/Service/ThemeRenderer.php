<?php

class ThemeRenderer
{
    /**
     * Execute existing frontend router files so output stays identical to public pages.
     */
    public function render(array $routeContext, array $editorContext)
    {
        $url = $routeContext['url'];
        $urlStructure = $routeContext['urlStructure'];
        $themeRouterFile = $routeContext['themeRouterFile'];

        // Expose editor context to templates/widgets for future drag-drop tooling.
        $GLOBALS['NO_CODE_EDITOR_CONTEXT'] = $editorContext;

        ob_start();

        if ($url === '/') {
            require ROUTER . 'homepage.php';
        } else {
            require ROUTER . 'routerManager.php';
        }

        return ob_get_clean();
    }
}
