<?php

class Restorer_Content_User extends Restorer_Content_Base_Entity {
  public function save($entity) {
    /**
     * If the account is existing: Do not change user password, email
     */
    if ($account = user_load($entity->uid)) {
      unset($entity->pass);
    }

    return parent::save($entity);
  }
}
