<?php

require_once __DIR__ . '/Model/CanvasDataRepository.php';
require_once __DIR__ . '/Service/EditorRequest.php';
require_once __DIR__ . '/Service/ThemeRouteResolver.php';
require_once __DIR__ . '/Service/ThemeRenderer.php';
require_once __DIR__ . '/Service/ThemeLoader.php';
require_once __DIR__ . '/Controller/NoCodeEditorController.php';

$request = EditorRequest::fromHttpRequest();

global $connection;

$repository = new CanvasDataRepository($connection);
$resolver = new ThemeRouteResolver();
$renderer = new ThemeRenderer();
$themeLoader = new ThemeLoader($repository, $resolver, $renderer);
$controller = new NoCodeEditorController($themeLoader);

$controller->handle($request);
