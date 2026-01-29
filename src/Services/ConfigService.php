<?php

namespace NetBrothers\SyncAccBundle\Services;

class ConfigService
{
    public function __construct(
        private array $config
    ) {
        $this->validate();
    }

    private function validate(): void
    {
        if (!$this->isEnabled()) {
            // If the bundle is not enabled, no further validation is needed.
            return;
        }

        $requiredKeys = ['acc_server', 'acc_software_token', 'acc_server_token'];
        foreach ($requiredKeys as $key) {
            if (empty($this->config[$key])) {
                throw new \LogicException("SyncAcc is enabled, but the config value for '$key' is missing or empty.");
            }
        }

        if ($this->isBasicAuthEnabled()) {
            $requiredAuthKeys = ['acc_basic_auth_user', 'acc_basic_auth_password'];
            foreach ($requiredAuthKeys as $key) {
                if (empty($this->config[$key])) {
                    throw new \LogicException("Basic Auth is enabled for SyncAcc, but '$key' is missing or empty.");
                }
            }
        }
    }

    public function isEnabled(): bool
    {
        return filter_var($this->config['acc_enable'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    public function getServer(): string
    {
        return (string) ($this->config['acc_server'] ?? '');
    }

    public function getSoftwareToken(): string
    {
        return (string) ($this->config['acc_software_token'] ?? '');
    }

    public function getServerToken(): string
    {
        return (string) ($this->config['acc_server_token'] ?? '');
    }

    public function isBasicAuthEnabled(): bool
    {
        return filter_var($this->config['acc_use_basic_auth'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    public function getBasicAuthUser(): string
    {
        return (string) ($this->config['acc_basic_auth_user'] ?? '');
    }

    public function getBasicAuthPassword(): string
    {
        return (string) ($this->config['acc_basic_auth_password'] ?? '');
    }
}
