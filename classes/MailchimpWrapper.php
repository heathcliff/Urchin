<?php

/**
 *  Use of this wrapper requires the Mailchimp API v2 (https://bitbucket.org/mailchimp/mailchimp-api-php)
 */

class MailchimpWrapper {

    public static function subscribe($email = false, $list_id = false, $send_welcome = false, $merge_vars = null) {
        if ($email && class_exists('MailChimp')) {
            $mailchimp          = new MailChimp($GLOBALS['mailchimp']['api_key']);
            $mailchimp_lists    = new Mailchimp_Lists($mailchimp);
            $list_id            = ($list_id) ? $list_id : $GLOBALS['mailchimp']['list_id'];
            $email              = array('email' => $email);
            $email_type         = 'html';
            $double_optin       = false;
            $update_existing    = true;
            $replace_interests  = false;
            $send_welcome       = $send_welcome;
            try {
                $subscriber     = $mailchimp_lists->subscribe($list_id, $email, $merge_vars, $email_type, $double_optin, $update_existing, $replace_interests, $send_welcome);
            } catch (Exception $e) {
                return false;
            }
            if (!empty($subscriber['email'])) {
                return $subscriber;
            }
        }
        return false;
    }

    public static function unsubscribe($email, $list_id, $delete_member = false, $send_goodbye = false, $send_notify = false) {
        if ($email && class_exists('MailChimp')) {
            $mailchimp          = new MailChimp($GLOBALS['mailchimp']['api_key']);
            $mailchimp_lists    = new Mailchimp_Lists($mailchimp);
            $list_id            = ($list_id) ? $list_id : $GLOBALS['mailchimp']['list_id'];
            $email              = array('email' => $email);
            try {
                $response           = $mailchimp_lists->unsubscribe($list_id, $email, $delete_member, $send_goodbye, $send_notify);
            } catch (Exception $e) {
                return false;
            }
            if (!empty($response['complete'])) {
                return true;
            }
        }
        return false;
    }

    public static function getStaticSegments($list_id = false) {
        if (class_exists('MailChimp')) {
            $mailchimp          = new MailChimp($GLOBALS['mailchimp']['api_key']);
            $mailchimp_lists    = new Mailchimp_Lists($mailchimp);
            $list_id            = ($list_id) ? $list_id : $GLOBALS['mailchimp']['list_id'];
            return $mailchimp_lists->staticSegments($list_id);
        }
        return false;
    }

    public static function addToSegment($email = false, $seg_id, $list_id = false) {
        return self::modifySegment($email, $seg_id, $list_id, 'add');
    }

    public static function removeFromSegment($email = false, $seg_id, $list_id = false) {
        return self::modifySegment($email, $seg_id, $list_id, 'remove');
    }

    private static function modifySegment($email = false, $seg_id, $list_id = false, $action = 'add') {
        if (class_exists('MailChimp')) {
            $mailchimp          = new MailChimp($GLOBALS['mailchimp']['api_key']);
            $mailchimp_lists    = new Mailchimp_Lists($mailchimp);
            $list_id            = ($list_id) ? $list_id : $GLOBALS['mailchimp']['list_id'];
            $batch              = array(array('email' => $email));
            try {
                if ($action == 'add') {
                    $response = $mailchimp_lists->staticSegmentMembersAdd($list_id, $seg_id, $batch);
                } else {
                    $response = $mailchimp_lists->staticSegmentMembersDel($list_id, $seg_id, $batch);
                }
            } catch (Exception $e) {
                return false;
            }
            if (!empty($response['success_count']) && $response['success_count']) {
                return $response;
            }
        }
        return false;
    }

}