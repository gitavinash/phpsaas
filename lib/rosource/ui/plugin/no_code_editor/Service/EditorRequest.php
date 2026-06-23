<?php

class EditorRequest
{
    public static function fromHttpRequest()
    {
        $routePayload = self::fromRouteSegments();

        $url = isset($_GET['url']) ? $_GET['url'] : $routePayload['url'];
        $type = isset($_GET['type']) ? $_GET['type'] : $routePayload['type'];
        $id = isset($_GET['id']) ? (int)$_GET['id'] : (int)$routePayload['id'];

        return [
            'url' => self::normalizeUrl($url),
            'type' => self::normalizeType($type),
            'id' => $id,
            'action' => self::resolveAction($routePayload)
        ];
    }

    private static function fromRouteSegments()
    {
        $route = trim((string)ADMIN_BASE_URL, '/');
        $segments = ($route === '') ? [] : explode('/', $route);

        $response = [
            'action' => 'ui',
            'type' => '',
            'id' => 0,
            'url' => '/'
        ];

        if (!isset($segments[0]) || $segments[0] !== 'editor') {
            return $response;
        }

        if (isset($segments[1]) && $segments[1] === 'render') {
            $response['action'] = 'render';

            if (isset($segments[2])) {
                $response['type'] = $segments[2];
            }
            if (isset($segments[3])) {
                $response['id'] = (int)$segments[3];
            }
            if (isset($segments[4])) {
                $response['url'] = '/' . implode('/', array_slice($segments, 4)) . '/';
            }

            return $response;
        }

        if (isset($segments[1])) {
            $response['type'] = $segments[1];
        }
        if (isset($segments[2])) {
            $response['id'] = (int)$segments[2];
        }
        if (isset($segments[3])) {
            $response['url'] = '/' . implode('/', array_slice($segments, 3)) . '/';
        }

        return $response;
    }

    private static function resolveAction(array $routePayload)
    {
        if (isset($_GET['action']) && $_GET['action'] === 'render') {
            return 'render';
        }

        return $routePayload['action'];
    }

    private static function normalizeUrl($url)
    {
        $url = trim((string)$url);

        if ($url === '' || $url === '/') {
            return '/';
        }

        $url = '/' . trim($url, '/') . '/';

        return preg_replace('#/+#', '/', $url);
    }

    private static function normalizeType($type)
    {
        $type = strtolower(trim((string)$type));
        return preg_replace('/[^a-z0-9_-]/', '', $type);
    }
}
