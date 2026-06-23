<?php

class CanvasDataRepository
{
    /** @var mysqli */
    private $connection;

    /** @var string */
    private $tablePrefix;

    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->tablePrefix = TABLE_PREFIX;
    }

    /**
     * Resolve link by URL first, then fallback to type + id.
     */
    public function resolveLinkByRequest(array $request)
    {
        $url = $this->escape($request['url']);
        $type = $this->escape($request['type']);
        $id = (int)$request['id'];

        if ($url !== '') {
            $query = "SELECT link_id, link_url, link_relation_id, link_type, link_status, link_site_id
                FROM {$this->tablePrefix}link
                WHERE link_url='{$url}' AND link_site_id=" . SITE_ID . "
                ORDER BY link_modified DESC
                LIMIT 1";

            $result = mysqli_query($this->connection, $query);
            if ($result && mysqli_num_rows($result) > 0) {
                return mysqli_fetch_assoc($result);
            }
        }

        if ($type !== '' && $id > 0) {
            $query = "SELECT link_id, link_url, link_relation_id, link_type, link_status, link_site_id
                FROM {$this->tablePrefix}link
                WHERE link_type='{$type}'
                AND link_relation_id='{$id}'
                AND link_site_id=" . SITE_ID . "
                ORDER BY link_modified DESC
                LIMIT 1";

            $result = mysqli_query($this->connection, $query);
            if ($result && mysqli_num_rows($result) > 0) {
                return mysqli_fetch_assoc($result);
            }
        }

        return null;
    }

    /**
     * Load canvas/content data by resolved URL and content type.
     */
    public function loadCanvasData(array $resolvedLink)
    {
        $linkType = $this->escape($resolvedLink['link_type']);
        $relationId = (int)$resolvedLink['link_relation_id'];

        switch ($linkType) {
            case 'page':
                return $this->loadPageData($relationId);

            case 'post':
                return $this->loadPostData($relationId);

            default:
                return [
                    'type' => $linkType,
                    'id' => $relationId,
                    'raw' => null,
                    'sections' => []
                ];
        }
    }

    private function loadPageData($pageId)
    {
        $query = "SELECT page_id, page_title, page_description, page_highlight, page_section, page_type
            FROM {$this->tablePrefix}page
            WHERE page_id='{$pageId}'
            LIMIT 1";

        $result = mysqli_query($this->connection, $query);
        if (!$result || mysqli_num_rows($result) === 0) {
            return [
                'type' => 'page',
                'id' => $pageId,
                'raw' => null,
                'sections' => []
            ];
        }

        $row = mysqli_fetch_assoc($result);
        $sections = [];

        if (!empty($row['page_section'])) {
            $decoded = json_decode(base64_decode($row['page_section']), true);
            if (is_array($decoded)) {
                $sections = $decoded;
            }
        }

        return [
            'type' => 'page',
            'id' => (int)$row['page_id'],
            'raw' => $row,
            'sections' => $sections
        ];
    }

    private function loadPostData($postId)
    {
        $query = "SELECT post_id, post_title, post_description, post_type, post_image
            FROM {$this->tablePrefix}post
            WHERE post_id='{$postId}'
            LIMIT 1";

        $result = mysqli_query($this->connection, $query);
        if (!$result || mysqli_num_rows($result) === 0) {
            return [
                'type' => 'post',
                'id' => $postId,
                'raw' => null,
                'sections' => []
            ];
        }

        return [
            'type' => 'post',
            'id' => $postId,
            'raw' => mysqli_fetch_assoc($result),
            'sections' => []
        ];
    }

    private function escape($value)
    {
        return mysqli_real_escape_string($this->connection, (string)$value);
    }
}
