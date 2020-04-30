<?php

class BBPNActionHandler {

  function renderFields() {
    $notificationsLabel = get_field('notifications_label', 'options');

    $noneDescription = get_field('none_description', 'options');
    $directDescription = get_field('direct_description', 'options');
    $allDescription = get_field('all_description', 'options');

    $notifications = get_post_meta(bbp_get_reply_id(), '_bbpn_notifications', true);

    if (!$notifications) {
      $directChecked = 'checked';
    } else {
      $noneChecked = $notifications === 'off' ? 'checked' : '';
      $allChecked = $notifications === 'all' ? 'checked' : '';
      $directChecked = $notifications === 'direct' ? 'checked' : '';
    }

    echo '<fieldset class="bbp-form">';
    echo '<p>';
      echo "<label for='_bbpn_notifications'>{$notificationsLabel}</label>";
      echo '<br>';
      echo "<input type='radio' name='_bbpn_notifications' value='none' {$noneChecked}> <small> {$noneDescription}</small>";
      echo '<br>';
      echo "<input type='radio' name='_bbpn_notifications' value='direct' {$directChecked}> <small> {$directDescription}</small>";
      echo "<br>";
      echo "<input type='radio' name='_bbpn_notifications' value='all' {$allChecked}> <small> {$allDescription}</small>";
    echo "</p>";
    echo '</fieldset>';
  }

  function handleSave($reply_id) {
    if (isset($_POST) && $_POST['_bbpn_notifications'] !== '' ) {
      update_post_meta($reply_id, '_bbpn_notifications', $_POST['_bbpn_notifications']);
    } else {
      update_post_meta($reply_id, '_bbpn_notifications', 'none');
    }
  }

  public static function handleAction($replyId, $topicId, $forumId, $d, $e, $f, $replyTo) {
    if ($replyTo) {
      $response = get_post($replyId);
      $topic = get_post($topicId);
      $parentReply = get_post($replyTo);
      self::searchTree($response, $parentReply, $topic, true);
    }
  }

  public static function searchTree($response, $reply, $topic, $direct = false) {
    $notificationSettings = get_post_meta($reply->ID, '_bbpn_notifications', true);
    $author = get_user_by('id', $reply->post_author);

    if (!$reply) {
      return;
    }

    if ($notificationSettings === 'direct' && $direct) {
      self::sendMail($author, $response, $reply, $topic);
    } else if ($notificationSettings === 'all') {
      self::sendMail($author, $response, $reply, $topic);
    }

    if ($parentReplyTo = get_post_meta($reply->ID, '_bbp_reply_to', 1)) {
      if ($parentReply = get_post($parentReplyTo)) {
        self::searchTree($response, $parentReply, $topic, false);
      }
    }
  }

  public static function sendMail($author, $response, $reply, $topic) {
    $fromName = get_field('bbpn_from_name', 'options');
    $fromAddress = get_field('bbpn_from_address', 'options');
    $subject = self::buildSubject($author);
    $body = self::buildBody($author, $response, $reply, $topic);
    $email = new \SendGrid\Mail\Mail();
    $email->addTo(new \SendGrid\Mail\To($author->user_email, $author->display_name));
    $email->setFrom(new \SendGrid\Mail\From($fromAddress, $fromName));
    $email->addBcc(new \SendGrid\Mail\Bcc('<some-email>', 'Daniel Griffiths'));
    $email->setSubject(new \SendGrid\Mail\Subject($subject));
    $email->addContent(new \SendGrid\Mail\HtmlContent($body));
    $sendgrid = new \SendGrid(get_field('sendgrid_key', 'options'));
    try {
      $response = $sendgrid->send($email);
    } catch (Exception $e) {
      echo 'Caught exception: '. $e->getMessage() ."\n";
      return;
    }
  }

  public static function buildSubject($authorData) {
    $subject = get_field('bbpn_subject', 'options');
    $subject = str_replace('[email]', $authorData->user_email, $subject);
    $subject = str_replace('[display_name]', $authorData->display_name, $subject);
    return $subject;
  }

  public static function buildBody($authorData, $response, $reply, $topic) {
    global $wp;
    $body = get_field('bbpn_body', 'options');
    $body = str_replace('[email]', $authorData->user_email, $body);
    $body = str_replace('[display_name]', $authorData->display_name, $body);
    $body = str_replace('[current_url]', home_url(add_query_arg([], $wp->request)), $body);
    $body = str_replace('[profile_link]', get_site_url() . '/forums/users/' . str_replace(' ', '-', $authorData->user_login), $body);
    $body = str_replace('[topic_title]', $topic->post_title, $body);
    $body = str_replace(
      '[reply_body]',
      strlen($reply->post_content) > 100 ? substr(0, 100, $reply->post_content) . '...' : $reply->post_content,
      $body
    );
    $body = str_replace(
      '[response_body]',
      strlen($response->post_content) > 100 ? substr(0, 100, $response->post_content) . '...' : $response->post_content,
      $body
    );
    return $body;
  }

}
