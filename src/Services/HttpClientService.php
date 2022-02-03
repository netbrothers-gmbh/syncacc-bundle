<?php
/**
 * NetBrothers Sync Access Control Center
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 24.03.21
 *
 */

namespace NetBrothers\SyncAccBundle\Services;


use NetBrothers\SyncAccBundle\Entity\SyncAcc;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class HttpClientService
 * @package NetBrothers\SyncAccBundle\Services
 */
class HttpClientService
{
    /** @var string Template route for getting roles */
    const ACC_SERVER_ROUTE_ROLE = "/sync/get-roles/softwareToken/serverToken/timestamp";

    /** @var string Template route for getting acls for one role */
    const ACC_SERVER_ROUTE_ACL = "/sync/get-permissions/softwareToken/serverToken/timestamp/idRole";

    /** @var string Template route for getting acls for one role */
    const ACC_BUILD_NAME_ROUTE = "/sync/get-build/";

    /** @var array Konfiguration für den HttpClient */
    private array $clientConfig = [
        'auth_basic' => [],
        'headers' => ['Content-Type' => 'application/json'],
    ];

    /** @var array */
    private array $config = [];

    public function __construct(array $config, array $clientConfig)
    {
        $this->config = $config;
        $this->clientConfig = $clientConfig;
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
        $url  = $this->config['acc_server'] . self::ACC_BUILD_NAME_ROUTE;
        $url .=  $this->config['acc_software_token'] . '/' . $this->config['acc_server_token'];
        $response = $this->send($url);
        if (is_array($response) && !empty($response['build'])) {
            return $response['build'];
        }
        return null;
    }

    /**
     * @param SyncAcc $syncAcc
     * @return false|mixed
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getRoles(SyncAcc $syncAcc)
    {
        $url = $this->createUrl($syncAcc, null);
        return $this->send($url);
    }

    /**
     * @param SyncAcc $syncAcc
     * @param int $idRole
     * @return bool
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getPermissionForOneRole(SyncAcc $syncAcc, int $idRole)
    {
        $url = $this->createUrl($syncAcc, $idRole);
        return $this->send($url);
    }

    /**
     * @param SyncAcc $syncAcc
     * @param int|null $idRole
     * @return string
     */
    private function createUrl(SyncAcc $syncAcc, int $idRole = null): string
    {
        $timestamp = $syncAcc->getLastCall()->getTimestamp();
        $baseUrl = (null === $idRole)
            ? $this->config['acc_server'] . self::ACC_SERVER_ROUTE_ROLE
            : $this->config['acc_server'] . self::ACC_SERVER_ROUTE_ACL ;

        $softwareToken = preg_replace("/softwareToken/", $this->config['acc_software_token'], $baseUrl);
        $serverToken = preg_replace("/serverToken/", $this->config['acc_server_token'], $softwareToken);
        $url = preg_replace("/timestamp/", $timestamp, $serverToken);
        if (null !== $idRole) {
            $url = preg_replace("/idRole/", $idRole, $url);
        }
        return $url;
    }


    /**
     * @param string $url
     * @return false|mixed
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function send(string $url)
    {
        $client = HttpClient::create($this->clientConfig);
        $response = $client->request('GET', $url);
        $statusCode = $response->getStatusCode();
        if ($statusCode == 200) {
            return json_decode($response->getContent(), true);
        } else {
            $response->getContent(true);
            return false;
        }
    }
}
