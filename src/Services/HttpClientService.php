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
    private $clientConfig = [
        'auth_basic' => [],
        'headers' => ['Content-Type' => 'application/json'],
    ];

    /** @var array */
    private $config = [];

    public function __construct(array $config, array $clientConfig)
    {
        $this->config = $config;
        $this->clientConfig = $clientConfig;
    }

    /**
     * @return mixed|null
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getBuildName() {
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
        $timestamp = $syncAcc->getLastCall()->getTimestamp();
        $baseUrl = $this->config['acc_server'] . self::ACC_SERVER_ROUTE_ROLE ;
        $tUrl = preg_replace("/softwareToken/", $this->config['acc_software_token'], $baseUrl);
        $t2Url = preg_replace("/serverToken/", $this->config['acc_server_token'], $tUrl);
        $url = preg_replace("/timestamp/", $timestamp, $t2Url);
        return $this->send($url);

    }

    /**
     * @param int|null $idRole
     * @return bool
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getPermissionForOneRole(SyncAcc $syncAcc, int $idRole)
    {
        $timestamp = $syncAcc->getLastCall()->getTimestamp();
        $baseUrl = $this->config['acc_server'] . self::ACC_SERVER_ROUTE_ACL ;
        $tUrl = preg_replace("/softwareToken/", $this->config['acc_software_token'], $baseUrl);
        $t2Url = preg_replace("/serverToken/", $this->config['acc_server_token'], $tUrl);
        $url = preg_replace("/timestamp/", $timestamp, $t2Url);
        $url = preg_replace("/idRole/", $idRole, $url);
        return $this->send($url);
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
            $response = json_decode($response->getContent(), true);
            return $response;
        } else {
            $response->getContent(true);
            return false;
        }
    }
}