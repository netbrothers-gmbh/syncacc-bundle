<?php

declare(strict_types=1);

/*
 * This file is part of the NetBrothers SyncAccBundle.
 *
 * (c) 2024 NetBrothers GmbH | Stefan Wessel (https://netbrothers.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NetBrothers\SyncAccBundle\Services;


use NetBrothers\SyncAccBundle\Entity\SyncAcc;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HttpClientService
{
    const string ACC_SERVER_ROUTE_ROLE = "/sync/get-roles/softwareToken/serverToken/timestamp";
    const string ACC_SERVER_ROUTE_ACL = "/sync/get-permissions/softwareToken/serverToken/timestamp/idRole";
    const string ACC_BUILD_NAME_ROUTE = "/sync/get-build/";

    private HttpClientInterface $client;

    public function __construct(
        private readonly ConfigService $configService,
        HttpClientInterface $client
    ) {
        $clientOptions = [
            'headers' => ['Content-Type' => 'application/json'],
        ];

        if ($this->configService->isBasicAuthEnabled()) {
            $clientOptions['auth_basic'] = [
                $this->configService->getBasicAuthUser(),
                $this->configService->getBasicAuthPassword()
            ];
        }

        $this->client = $client->withOptions($clientOptions);
    }

    /**
     * @return string|null
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getBuildName(): ?string
    {
        $url  = $this->configService->getServer() . self::ACC_BUILD_NAME_ROUTE;
        $url .=  $this->configService->getSoftwareToken() . '/' . $this->configService->getServerToken();
        $response = $this->send($url);
        if (is_array($response) && !empty($response['build'])) {
            return $response['build'];
        }
        return null;
    }

    /**
     * @param SyncAcc $syncAcc
     * @return array|null
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getRoles(SyncAcc $syncAcc): ?array
    {
        $url = $this->createUrl($syncAcc, null);
        return $this->send($url);
    }

    /**
     * @param SyncAcc $syncAcc
     * @param int $idRole
     * @return array|null
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getPermissionForOneRole(SyncAcc $syncAcc, int $idRole): ?array
    {
        $url = $this->createUrl($syncAcc, $idRole);
        return $this->send($url);
    }

    /**
     * @param SyncAcc $syncAcc
     * @param int|null $idRole
     * @return string
     */
    private function createUrl(SyncAcc $syncAcc, ?int $idRole = null): string
    {
        $timestamp = $syncAcc->getLastCall()->getTimestamp();
        $baseUrl = (null === $idRole)
            ? $this->configService->getServer() . self::ACC_SERVER_ROUTE_ROLE
            : $this->configService->getServer() . self::ACC_SERVER_ROUTE_ACL ;

        $softwareToken = preg_replace("/softwareToken/", $this->configService->getSoftwareToken(), $baseUrl);
        $serverToken = preg_replace("/serverToken/", $this->configService->getServerToken(), $softwareToken);
        $url = preg_replace("/timestamp/", (string) $timestamp, $serverToken);
        if (null !== $idRole) {
            $url = preg_replace("/idRole/", (string) $idRole, $url);
        }
        return $url;
    }


    /**
     * @param string $url
     * @return array|null
     * @throws TransportExceptionInterface
     */
    private function send(string $url): ?array
    {
        $response = $this->client->request('GET', $url);

        if (200 !== $response->getStatusCode()) {
            $response->getHeaders(true);
            return null;
        }
        return $response->toArray(true);
    }
}
