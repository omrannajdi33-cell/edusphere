<?php

namespace App\Services;

class WorksheetAnnotationService
{
    public function emptyDocument(): array
    {
        return [
            'version' => 1,
            'pageCount' => 0,
            'pages' => [],
        ];
    }

    public function normalize(?array $data): array
    {
        if (! is_array($data) || ! isset($data['pages']) || ! is_array($data['pages'])) {
            return $this->emptyDocument();
        }

        return [
            'version' => 1,
            'pageCount' => (int) ($data['pageCount'] ?? count($data['pages'])),
            'pages' => $data['pages'],
        ];
    }

    public function mergePageStrokes(array $document, int $page, string $layer, array $strokes): array
    {
        $document = $this->normalize($document);
        $key = (string) $page;

        if (! isset($document['pages'][$key])) {
            $document['pages'][$key] = ['student' => [], 'teacher' => []];
        }

        $document['pages'][$key][$layer] = array_values($strokes);
        $document['pageCount'] = max($document['pageCount'], $page + 1);

        return $document;
    }

    public function mergeIncoming(array $existing, array $incoming, string $layer): array
    {
        $existing = $this->normalize($existing);
        $incoming = $this->normalize($incoming);

        foreach ($incoming['pages'] as $pageKey => $pageData) {
            if (! is_array($pageData)) {
                continue;
            }

            if (! isset($existing['pages'][$pageKey])) {
                $existing['pages'][$pageKey] = ['student' => [], 'teacher' => []];
            }

            if (isset($pageData[$layer]) && is_array($pageData[$layer])) {
                $existing['pages'][$pageKey][$layer] = array_values($pageData[$layer]);
            }
        }

        $existing['pageCount'] = max(
            (int) $existing['pageCount'],
            (int) $incoming['pageCount'],
            count($existing['pages'])
        );

        return $existing;
    }
}
