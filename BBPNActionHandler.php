<?php

class BBPNActionHandler {

  public static function handleAction($replyId, $topicId, $forumId) {
    $replyTo = get_post_meta($replyId, 'reply_to', 1);
    if ($replyTo) {
      $parentReply = get_post($replyTo);
      if (get_field('desires_notifications', $parentReply->post_author)) {
        $authorData = get_userdata($parentReply->post_author);
        wp_mail(
          self::buildTo($authorData),
          self::buildSubject($authorData),
          self::buildBody($authorData),
          self::buildHeaders($authorData)
        );
      }
    }
  }

  public static function buildTo($authorData) {
    return $authorData->user_email;
  }

  public static function buildSubject($authorData) {
    $subject = get_field('bbpn_subject', 'options');
    $subject = str_replace('[email]', $authorData->user_email, $subject);
    $subject = str_replace('[username]', $authorData->username, $subject);
    $subject = str_replace('[first_name]', $authorData->first_name, $subject);
    $subject = str_replace('[last_name]', $authorData->last_name, $subject);
    $subject = str_replace('[display_name]', $authorData->display_name, $subject);
    return $subject;
  }

  public static function buildBody($authorData) {
    $body = get_field('bbpn_body', 'options');
    $body = str_replace('[email]', $authorData->user_email, $body);
    $body = str_replace('[username]', $authorData->username, $body);
    $body = str_replace('[first_name]', $authorData->first_name, $body);
    $body = str_replace('[last_name]', $authorData->last_name, $body);
    $body = str_replace('[display_name]', $authorData->display_name, $body);
    return $body;
  }

  public static function buildHeaders($authorData) {
    $fromData = get_field('bbpn_from', 'options');
    $ccData = get_field('bbpn_cc', 'options');
    $bccData = get_field('bbpn_bcc', 'options');
    $replyToData = get_field('bbpn_replyto', 'options');

    $headers = [
      "Content-Type: text/html; charset=UTF-8",
      "From: {$fromData['name']} <{$fromData['address']}>",
    ];

    foreach ($ccData as $cc) {
      $headers[] = "Cc: {$cc['name']} <{$cc['address']}>";
    }

    foreach ($bccData as $bcc) {
      $headers[] = "Bcc: {$bcc['name']} <{$bcc['address']}>";
    }

    if ($replyToData) {
      $headers[] = "Reply-To: {$replyToData['name']} <{$replyToData['address']}>";
    }

    return $headers;
  }

}
