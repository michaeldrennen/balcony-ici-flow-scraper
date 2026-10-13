<?php

declare(strict_types=1);

namespace MichaelDrennen\Balcony\IciFlowScraper\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MichaelDrennen\Balcony\IciFlowScraper\Client;
use MichaelDrennen\Balcony\IciFlowScraper\Config;
use MichaelDrennen\Balcony\IciFlowScraper\Exceptions\BalconyClientException;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    private function fixture(): string
    {
        return (string) file_get_contents(__DIR__ . '/Fixtures/sample_response.html');
    }

    public function testParsesDataRowsAndSkipsHeaders(): void
    {
        $dtos = (new Client(new Config()))->parse($this->fixture());

        $this->assertCount(2, $dtos);
        $this->assertSame('2026-10-07', $dtos[0]->reportDate->format('Y-m-d'));
        $this->assertSame(6450.21, $dtos[0]->totalAssetsBillions);
        $this->assertSame(1025.40, $dtos[0]->primeAssetsBillions);
    }

    public function testFetchUsesMockedHttp(): void
    {
        $http = new GuzzleClient([
            'handler' => HandlerStack::create(new MockHandler([new Response(200, [], $this->fixture())])),
        ]);

        $this->assertCount(2, (new Client(new Config(), $http))->fetchFlows());
    }

    public function testRateLimitThrows(): void
    {
        $http = new GuzzleClient([
            'handler' => HandlerStack::create(new MockHandler([new Response(429, ['Retry-After' => '3'], '')])),
            'http_errors' => false,
        ]);

        try {
            (new Client(new Config(), $http))->fetchFlows();
            $this->fail('Expected BalconyClientException.');
        } catch (BalconyClientException $e) {
            $this->assertTrue($e->isRateLimited());
            $this->assertSame(3, $e->retryAfterSeconds);
        }
    }

    public function testBillionsCoercion(): void
    {
        $this->assertSame(1234.5, Client::parseBillions('1,234.5'));
        $this->assertNull(Client::parseBillions('n/a'));
    }
}
