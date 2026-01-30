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

namespace NetBrothers\SyncAccBundle\Dto;

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
