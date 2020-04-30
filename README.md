# BBPress Notifications

Creates a better notifications system for bbpress. bbpress only provides notifications for all replies in a topic. This can be overwhelming and annoying.

### Features

This plugin instead provides 3 options per-reply. The options are appended to the bbpress reply form as radio buttons.

1. No notifications for this reply.
2. Only notifications of direct replies.
3. Notifications for all descendant replies.

- The notifications in this version are sent with SendGrid (this is what I used, since I have an account and it's more reliable than the `wp_mail` function).
- The config is supplied by ACF fields. This could be done with a plugin options page, but again, in my use-case ACF was already available.
- To send notifications for all ascendent parents of a reply I am using a recursive function based on the `_bbp_reply_to` `meta_value`.

The notification email template can be customized. For instance, in the body you can use the following:

1. `[current_url]` The page which the reply was posted from. Useful for directing the recipient back to the conversation.
2. `[profile_link]` Recipients bbpress profile
3. `[topic_title]` Title of topic
4. `[email]` Recipients WP email address
5. `[display_name]` Recipients WP display name
6. `[reply_body]` Body of the recipients initial reply
7. `[response_body]` Body of the reply to the recipient

You may also use the `[display_name]` placeholder in the subject line of the template.

### Dependencies

- SendGrid (optional)
- Advanced Custom Fields (optional)

### Usage (not configured for ease-of-use, but could be used if you want)

- This isn't meant to be directly portable.
- Download and unzip.
- Install ACF or configure a WP options page.
- Create a SendGrid API key or change the mailing configuration to match your preferred alternative to SendGrid.
- Locate the `get_field()` functions and create ACF fields or WP options accordingly. They are all simple `type="text"` aside from a WYSIWYG Editor for the message body field. This is critical.
- Remove the `require_once(plugin_dir_path(__FILE__) . 'sendgrid-php/sendgrid-php.php');` line from main.php if you are not using SendGrid.
- Activate the plugin.
