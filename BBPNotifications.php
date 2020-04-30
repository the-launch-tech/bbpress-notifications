<?php

class BBPNotifications {

  public static function instance() {
    static $inst = null;
    if ($inst === null) {
      $inst = new BBPNotifications();
    }
    return $inst;
  }


  public function __construct() {
    add_action('bbp_new_reply', 'BBPNActionHandler::handleAction', 1, 7);
    add_action ('bbp_theme_before_reply_form_content', 'BBPNActionHandler::renderFields');
    add_action ('bbp_new_reply', 'BBPNActionHandler::handleSave', 10, 1);
    add_action ('bbp_edit_reply', 'BBPNActionHandler::handleSave', 10, 1);
  }

}
