<?php

class Dumper_Data_FakeEntityUserRole {
  public $user;
  public $role;

  public function __construct($uid, $rid) {
    if (!$this->user = user_load($uid)) {
      throw new FakeEntityUserRoleException("Invalid user: " . filter_xss_admin($uid));
    }

    if (!$this->role = user_role_load($rid)) {
      throw new FakeEntityUserRoleException("Invalid user: " . filter_xss_admin($rid));
    }
  }

  public function entityType() {
    return 'user_role';
  }

  public function identifier() {
    return "{$this->role->rid}_{$this->user->uid}";
  }
}
