<?php

namespace App\Tests\Controller;

use App\Factory\ProcessedMessageFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    use \Zenstruck\Foundry\Test\Factories;

    public function testStatusWithoutTokenReturnsUnauthorized(): void
    {
        $client = static::createClient();
        ProcessedMessageFactory::createMany(5);
        
        $client->request('GET', '/_respatch/api/status');
        
        $this->assertResponseStatusCodeSame(401);
    }

    public function testStatusWithInvalidTokenReturnsUnauthorized(): void
    {
        $client = static::createClient();
        ProcessedMessageFactory::createMany(5);
        
        $client->request('GET', '/_respatch/api/status', [], [], [
            'HTTP_X_RESPATCH_TOKEN' => 'invalid_token',
        ]);
        
        $this->assertResponseStatusCodeSame(401);
    }

    public function testStatusWithValidTokenReturnsOk(): void
    {
        $client = static::createClient();
        ProcessedMessageFactory::createMany(5);
        
        $token = $_ENV['RESPATCH_TOKEN'] ?? 'some_secure_hash_token_123_test';
        
        $client->request('GET', '/_respatch/api/status', [], [], [
            'HTTP_X_RESPATCH_TOKEN' => $token,
        ]);
        
        $this->assertResponseIsSuccessful();
        
        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        $data = json_decode($responseContent, true);
        $this->assertArrayHasKey('status', $data);
        $this->assertSame('OK', $data['status']);
    }

    public function testDashboardReturnsCorrectStructure(): void
    {
        $client = static::createClient();
        ProcessedMessageFactory::createMany(5);
        
        $token = $_ENV['RESPATCH_TOKEN'] ?? 'some_secure_hash_token_123_test';
        
        $client->request('GET', '/_respatch/api/dashboard', [], [], [
            'HTTP_X_RESPATCH_TOKEN' => $token,
        ]);
        
        $this->assertResponseIsSuccessful();
        
        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        
        $data = json_decode($responseContent, true);
        
        $this->assertIsArray($data);
        $this->assertArrayHasKey('snapshot', $data);
        $this->assertArrayHasKey('messages', $data);
    }

    public function testStatisticsReturnsCorrectStructure(): void
    {
        $client = static::createClient();
        ProcessedMessageFactory::createMany(5);
        
        $token = $_ENV['RESPATCH_TOKEN'] ?? 'some_secure_hash_token_123_test';
        
        $client->request('GET', '/_respatch/api/statistics', ['period' => '1-day'], [], [
            'HTTP_X_RESPATCH_TOKEN' => $token,
        ]);
        
        $this->assertResponseIsSuccessful();
        
        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        
        $data = json_decode($responseContent, true);
        
        $this->assertIsArray($data);
        $this->assertArrayHasKey('periods', $data);
        $this->assertArrayHasKey('period', $data);
        $this->assertArrayHasKey('metrics', $data);
    }

    public function testHistoryReturnsCorrectStructure(): void
    {
        $client = static::createClient();
        ProcessedMessageFactory::createMany(5);
        
        $token = $_ENV['RESPATCH_TOKEN'] ?? 'some_secure_hash_token_123_test';
        
        $client->request('GET', '/_respatch/api/history', ['period' => '1-day'], [], [
            'HTTP_X_RESPATCH_TOKEN' => $token,
        ]);
        
        $this->assertResponseIsSuccessful();
        
        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        
        $data = json_decode($responseContent, true);
        
        $this->assertIsArray($data);
        $this->assertArrayHasKey('periods', $data);
        $this->assertArrayHasKey('period', $data);
        $this->assertArrayHasKey('snapshot', $data);
        $this->assertArrayHasKey('filters', $data);
    }

    public function testTransportReturnsCorrectStructure(): void
    {
        $client = static::createClient();
        ProcessedMessageFactory::createMany(5);
        
        $token = $_ENV['RESPATCH_TOKEN'] ?? 'some_secure_hash_token_123_test';
        
        $client->request('GET', '/_respatch/api/transport', [], [], [
            'HTTP_X_RESPATCH_TOKEN' => $token,
        ]);
        
        $this->assertResponseIsSuccessful();
        
        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        
        $data = json_decode($responseContent, true);
        
        $this->assertIsArray($data);
        $this->assertArrayHasKey('transports', $data);
        $this->assertArrayHasKey('transport', $data);
        $this->assertArrayHasKey('limit', $data);
    }

    public function testWorkersReturnsCorrectStructure(): void
    {
        $client = static::createClient();
        ProcessedMessageFactory::createMany(5);
        
        $token = $_ENV['RESPATCH_TOKEN'] ?? 'some_secure_hash_token_123_test';
        
        $client->request('GET', '/_respatch/api/workers', [], [], [
            'HTTP_X_RESPATCH_TOKEN' => $token,
        ]);
        
        $this->assertResponseIsSuccessful();
        
        $responseContent = $client->getResponse()->getContent();
        $this->assertJson($responseContent);
        
        $data = json_decode($responseContent, true);
        
        $this->assertIsArray($data);
        $this->assertArrayHasKey('workers', $data);
    }
}
