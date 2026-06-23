<?php

class ThemeLoader
{
    /** @var CanvasDataRepository */
    private $repository;

    /** @var ThemeRouteResolver */
    private $resolver;

    /** @var ThemeRenderer */
    private $renderer;

    public function __construct(CanvasDataRepository $repository, ThemeRouteResolver $resolver, ThemeRenderer $renderer)
    {
        $this->repository = $repository;
        $this->resolver = $resolver;
        $this->renderer = $renderer;
    }

    /**
     * Main orchestration flow:
     * 1. Resolve page by url/type/id
     * 2. Fetch canvas/content by resolved URL
     * 3. Resolve existing public router context
     * 4. Render through existing frontend theme files
     */
    public function load(array $request)
    {
        $result = $this->inspect($request);

        if (!$result['success']) {
            return $result;
        }

        $editorContext = [
            'request' => $request,
            'resolvedLink' => $result['resolvedLink'],
            'canvas' => $result['canvas'],
            'routeContext' => $result['routeContext']
        ];

        $renderedHtml = $this->renderer->render($result['routeContext'], $editorContext);
        $result['renderedHtml'] = $renderedHtml;

        return $result;
    }

    /**
     * Resolve data and route context without rendering theme output.
     */
    public function inspect(array $request)
    {
        $resolvedLink = $this->repository->resolveLinkByRequest($request);

        if ($resolvedLink === null) {
            return [
                'success' => false,
                'message' => 'Unable to resolve the requested URL in link table.',
                'request' => $request,
                'renderedHtml' => ''
            ];
        }

        $canvasData = $this->repository->loadCanvasData($resolvedLink);
        $routeContext = $this->resolver->buildRouteContext($resolvedLink);

        return [
            'success' => true,
            'message' => 'Theme request resolved using existing frontend routing context.',
            'request' => $request,
            'resolvedLink' => $resolvedLink,
            'canvas' => $canvasData,
            'routeContext' => $routeContext,
            'renderedHtml' => ''
        ];
    }
}
