<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Notifications Language Lines
    |--------------------------------------------------------------------------
    |
    */

    'name'                          => 'Notifications',
    'empty'                         => 'There are no notifications yet',
    'post_notification_like'        => ':Name has liked your post',
    'post_notification_star'        => ':Name has starred your post',
    'following_notification'        => ':Name has started following you',
    'post_notification_comment'     => ':Name has commented your post',
    'comment_reply_notification'    => ':Name has replied to your comment to the post',
    'follower_post_create'          => ':Name has created new post',
    'follower_post_subject'         => 'New post has been created',
    'view_post'                     => 'View Post',
    'view_user'                     => 'View User',
    'clear'                         => 'Clear',
    'circle_invite_notification'    => ':Name has invited you to the ":Circle_title" circle',
    'mark_all_read'                 => 'Mark all as read',
    'start_rendering_subject'       => 'Project rendering is started',
    'start_rendering_message'       => 'The ":name" project rendering is started',
    'rendering_finished_subject'    => 'Your project is complete',
    'rendering_success_text'        => ':name project is published',
    'rendering_fail_text'           => ':name project failed to publish',
    'upload_files_success_text'     => 'Project files uploaded successfully',

    // Stars
    'new_star_notification' => 'Your project ":project" has been starred by :user.',

    // Followers
    'new_follower_notification' => ':user started follow you.',

    // Messages
    'new_message_notification' => ':user sent you a message.',

    // Comments
    'new_comment_notification' => ':user commented ":project"',
    'new_reply_notification'   => ':replier replied to you comment on ":project".',

    // Donations
    'new_donation_notification' => ':payer sent a new donation, expires :expiresIn.',
    'new_donation_auto_accepted' => ':payer sent a donation, auto accepted.',
    'new_donation_auto_declined' => ':payer sent a donation, auto declined.',
    'donation_accepted'         => ':payee accepted your donation.',
    'donation_declined'         => ':payee declined your donation.',
    'donation_expired'          => 'Donation from :payer expired.',

    // Projects
    'project_clipped' => ':user has clipped :type from your ":project" project.',

    /*
    |--------------------------------------------------------------------------
    | Notification errors
    |-------------------------------------------------------------------------- |
    |     |
    */
    'wrong_access_rights' => 'You don’t have access to notifications.',
];
