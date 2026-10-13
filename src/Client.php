<?php

declare(strict_types=1);

namespace MichaelDrennen\Balcony\IciFlowScraper;

use DateTimeImmutable;
use DOMDocument;
use DOMElement;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use MichaelDrennen\Balcony\IciFlowScraper\DTO\IciMmfFlowDTO;
use MichaelDrennen\Balcony\IciFlowScraper\Exceptions\BalconyClientException;

final class Client
{
    private readonly ClientInterface $http;

    public function __construct(
        private readonly Config $config,
        ?ClientInterface $http = null,
    ) {
        $this->http = $http ?? new GuzzleClient([
            'timeout' => $config->timeoutSeconds,
            'http_errors' => false,
        ]);
    }

    /**
     * @return IciMmfFlowDTO[]
     */
    public function fetchFlows(): array
    {
        try {
            $response = $this->http->request('GET', $this->config->url);
        } catch (GuzzleException $e) {
            throw BalconyClientException::transport($e->getMessage(), $e);
        }

        $status = $response->getStatusCode();

        if ($status === 429) {
            $retryHeader = $response->getHeaderLine('Retry-After');
            throw BalconyClientException::rateLimited(is_numeric($retryHeader) ? (int) $retryHeader : null);
        }

        if ($status >= 400) {
            throw BalconyClientException::httpError($status, (string) $response->getBody());
        }

        return $this->parse((string) $response->getBody());
    }

    /**
     * Extract weekly MMF rows from ICI HTML tables. A valid row is a <tr> whose first cell
     * is a mm/dd/yyyy date followed by six numeric cells (total, retail, institutional,
     * government, prime, tax-exempt). Other rows, such as headers, are skipped.
     *
     * @return IciMmfFlowDTO[]
     */
    public function parse(string $html): array
    {
        $previous = libxml_use_internal_errors(true);
        $document = new DOMDocument();
        $document->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $rows = [];

        foreach ($document->getElementsByTagName('tr') as $tr) {
            if (!$tr instanceof DOMElement) {
                continue;
            }

            $cells = [];

            foreach ($tr->getElementsByTagName('td') as $td) {
                $cells[] = trim(preg_replace('/\s+/', ' ', $td->textContent) ?? '');
            }

            if (count($cells) < 7) {
                continue;
            }

            $reportDate = self::parseUsDate($cells[0]);
            $numbers = array_map([self::class, 'parseBillions'], array_slice($cells, 1, 5));

            if ($reportDate === null || in_array(null, $numbers, true)) {
                continue;
            }

            $rows[] = new IciMmfFlowDTO(
                reportDate: $reportDate,
                totalAssetsBillions: $numbers[0],
                retailAssetsBillions: $numbers[1],
                institutionalAssetsBillions: $numbers[2],
                governmentAssetsBillions: $numbers[3],
                primeAssetsBillions: $numbers[4],
            );
        }

        return $rows;
    }

    public static function parseBillions(string $raw): ?float
    {
        $normalized = str_replace([',', '$'], '', trim($raw));

        return ($normalized !== '' && is_numeric($normalized)) ? (float) $normalized : null;
    }

    public static function parseUsDate(string $raw): ?DateTimeImmutable
    {
        $trimmed = trim($raw);
        $date = DateTimeImmutable::createFromFormat('!m/d/Y', $trimmed);

        return ($date !== false && $date->format('m/d/Y') === $trimmed) ? $date : null;
    }
}
