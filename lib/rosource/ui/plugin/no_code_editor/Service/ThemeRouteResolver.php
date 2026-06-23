<?php

class ThemeRouteResolver
{
    /**
     * Build runtime route context using resolved link details.
     */
    public function buildRouteContext(array $resolvedLink)
    {
        $resolvedUrl = isset($resolvedLink['link_url']) ? $resolvedLink['link_url'] : '/';
        $resolvedUrl = $this->normalizeUrl($resolvedUrl);

        $urlStructure = explode('/', trim($resolvedUrl, '/'));
        if ($resolvedUrl === '/') {
            $urlStructure = [''];
        }

        return [
            'url' => $resolvedUrl,
            'urlStructure' => $urlStructure,
            'themeRouterFile' => USER_PATH . 'router.php'
        ];
    }

    private function normalizeUrl($url)
    {
        $url = trim((string)$url);

        if ($url === '' || $url === '/') {
            return '/';
        }

        return '/' . trim($url, '/') . '/';
    }
}
