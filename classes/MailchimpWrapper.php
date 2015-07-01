<?php

/**
 *  Use of this wrapper requires the Mailchimp API v2 (https://bitbucket.org/mailchimp/mailchimp-api-php)
 */

class MailchimpWrapper {

    public static function subscribe($email = false, $list_id = false, $send_welcome = false, $merge_vars = null, $replace_interests = false) {
        if ($email && class_exists('MailChimp')) {
            $mailchimp          = new MailChimp($GLOBALS['mailchimp']['api_key']);
            $mailchimp_lists    = new Mailchimp_Lists($mailchimp);
            $list_id            = ($list_id) ? $list_id : $GLOBALS['mailchimp']['list_id'];
            $email              = array('email' => $email);
            $email_type         = 'html';
            $double_optin       = false;
            $update_existing    = true;
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

    public static function getGroups($list_id = false) {
        if (class_exists('MailChimp')) {
            $mailchimp          = new MailChimp($GLOBALS['mailchimp']['api_key']);
            $mailchimp_lists    = new Mailchimp_Lists($mailchimp);
            $list_id            = ($list_id) ? $list_id : $GLOBALS['mailchimp']['list_id'];
            return $mailchimp_lists->interestGroupings($list_id);
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

    public static function addToGroup($email = false, $parent_name = false, $group = false) {
        $merge_vars = array(
            'GROUPINGS' => array(
                array(
                     'name' => $parent_name,
                     'groups' => array($group)
                 )
            )
        );
        self::subscribe($email, false, false, $merge_vars);
        return false;
    }

    public static function removeFromGroup($email = false, $parent_name = false, $group = false) {
        // TODO: look for better alternatives to accomplish this. As of Mailchimp API v2.0 this seems to be the best solution...blech...

        $mailchimp          = new MailChimp($GLOBALS['mailchimp']['api_key']);
        $mailchimp_lists    = new Mailchimp_Lists($mailchimp);
        $list_id            = $GLOBALS['mailchimp']['list_id'];
        $emails             = array(array('email' => $email));

        // call the api to get the user's groups
        $user               = $mailchimp_lists->memberInfo($list_id, $emails);
        $groupings          = (!empty($user['data'][0]['merges']['GROUPINGS'])) ? $user['data'][0]['merges']['GROUPINGS'] : array();

        // remove the grouping from their interests and generate merge vars
        if ($groupings) {
            $modified_groupings = array();
            foreach ($groupings as $grouping) {
                if ($grouping['groups'][0]['interested'] && $grouping['name'] != $parent_name) {
                    $modified_groupings[] = array(
                        'name'      => $grouping['name'],
                        'groups'    => array($grouping['groups'][0]['name'])
                    );
                }
            }
            $merge_vars = array(
                'GROUPINGS' => $modified_groupings
            );
            self::subscribe($email, false, false, $merge_vars, true);
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