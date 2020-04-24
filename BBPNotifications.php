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
    add_action('wp_enqueue_scripts', 'BBPNotifications::enqueueAssets');
    add_action('bbp_insert_reply', 'BBPNActionHandler::handleAction');
  }

  public function enqueueAssets() {
    wp_enqueue_style('bbpnotifications-style-bundle', plugin_dir_url(__FILE__) . 'dist/main.css');
    wp_enqueue_script('bbpnotifications-vendor-bundle', plugin_dir_url(__FILE__) . 'dist/1.index.js');
    wp_enqueue_script('bbpnotifications-script-bundle', plugin_dir_url(__FILE__) . 'dist/index.js');
    wp_localize_script(
      'bbpnotifications-script-bundle',
      '$BBPN',
      [
        'baseUrl' => get_site_url(),
        'authId' => get_current_user_id(),
        'nonce' => wp_create_nonce('wp_rest'),
        'dom' => [
          'forumWrapperEl' => self::BBPRESS_WRAPPER,
          'topicIdEl' => self::BBPRESS_TOPIC_ID_GETTER,
          'replyHeaderEl' => self::BBPRESS_HEADER,
          'replyHeaderMetaEl' => self::BBPRESS_META,
          'appendedReplyLikeEl' => self::BBPRESS_LIKE,
        ]
      ]
    );
  }
}
