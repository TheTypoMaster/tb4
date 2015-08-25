<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 3/08/2015
 * Time: 2:43 PM
 */

namespace TopBetta\Services\UserAccount;


use TopBetta\Repositories\Contracts\UserAuditRepositoryInterface;

class UserAuditService
{

    /**
     * @var UserAuditRepositoryInterface
     */
    private $userAuditRepository;

    public function __construct(UserAuditRepositoryInterface $userAuditRepository)
    {
        $this->userAuditRepository = $userAuditRepository;
    }

    public function createAuditRecord($user, $field, $oldValue, $newValue, $admin = -1)
    {
        return $this->userAuditRepository->create(array(
            "user_id"    => $user,
            "admin_id"   => $admin,
            "field_name" => $field,
            "old_value"  => $oldValue,
            "new_value"  => $newValue
        ));
    }
}