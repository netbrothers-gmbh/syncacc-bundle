<?php

declare(strict_types=1);

namespace NetBrothers\SyncAccBundle\Dto;

/**
 * @author Thilo Ratnaweera <info@netbrothers.de>
 * @copyright © 2024 NetBrothers GmbH.
 * @license All rights reserved.
 */
final class AclRoleDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $displayName,
        public readonly string $beschreibung,
        public readonly int $hierarchyId,
        public readonly string $defaultRoute,
        public readonly bool $isHidden,
    ) {}
}
