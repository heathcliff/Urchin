<?php

/**
 *  Use of this wrapper requires the Mailchimp API v2 (https://bitbucket.org/mailchimp/mailchimp-api-php)
 */

class MailchimpWrapper {

    public static function subscribe($email = false, $list_id = false) {
        if ($email) {
            $mailchimp          = new MailChimp($GLOBALS['mailchimp']['api_key']);
            $mailchimp_lists    = new Mailchimp_Lists($mailchimp);
            $list_id            = ($list_id) ? $list_id : $GLOBALS['mailchimp']['list_id'];
            $email              = array('email' => $email);
            $merge_vars         = null;
            $email_type         = 'html';
            $double_optin       = false;
            $update_existing    = true;
            $replace_interests  = false;
            $send_welcome       = true;
            try {
                $subscriber     = $mailchimp_lists->subscribe($list_id, $email, $merge_vars, $email_type, $double_optin, $update_existing, $replace_interests, $send_welcome);
            } catch (Exception $e) {
                return false;
            }

            if (!empty($subscriber['email'])) {
                return true;
            }
        }
        return false;
    }

}