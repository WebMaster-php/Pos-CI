<?php
defined('BASEPATH') or exit('No direct script access allowed');

add_action('settings_groups', '_maybe_sms_gateways_settings_group');

function _maybe_sms_gateways_settings_group($groups)
{
    $CI = &get_instance();
    $gateways = $CI->sms->get_gateways();
    if (count($gateways) > 0) {
        $groups[] = array(
          'name'=>'sms',
          'lang'=>'SMS',
          'order'=>12,
          );
    }

    return $groups;
}

add_action('app_init', '_init_core_sms_gateways');

function _init_core_sms_gateways()
{
    $CI = &get_instance();

    $CI->sms->add_gateway('twilio', array(
            'name'=>'Twilio',
            'info'=>'<p>Phone numbers must be in format <a href="https://www.twilio.com/docs/glossary/what-e164" target="_blank">E.164</a>. Click <a href="https://support.twilio.com/hc/en-us/articles/223183008-Formatting-International-Phone-Numbers" target="_blank">here</a> to read more how phone numbers should be formatted.</p><hr class="hr-10" />',
            'options'=> array(
                array(
                    'name'=>'account_sid',
                    'label'=>'Account SID',
                ),
                array(
                    'name'=>'auth_token',
                    'label'=>'Auth Token',
                ),
                array(
                    'name'=>"phone_number",
                    'label'=>'Twilio Phone Number',
                ),
            ),
        ));
}

function is_sms_trigger_active($trigger = '')
{
    $CI = &get_instance();
    $active = $CI->sms->get_activate_gateway();

    if (!$active) {
        return false;
    }

    return $CI->sms->is_trigger_active($trigger);
}

function can_send_sms_based_on_creation_date($data_date_created)
{
    $now = time();
    $your_date = strtotime($data_date_created);
    $datediff = $now - $your_date;

    $days_diff = floor($datediff / (60 * 60 * 24));

    return $days_diff < DO_NOT_SEND_SMS_ON_DATA_OLDER_THEN || $days_diff == DO_NOT_SEND_SMS_ON_DATA_OLDER_THEN;
}

function twilio_trigger_send_sms($number, $message)
{
    $CI = &get_instance();

    // Using composer
    // require_once(APPPATH . '/third_party/twilio/Twilio/autoload.php');

    // Account SID from twilio.com/console
    static $sid;
    // Auth Token from twilio.com/console
    static $token;
    // Twilio Phone Number
    static $phone;

    if (!$sid) {
        $sid = $CI->sms->get_option('twilio', 'account_sid');
    }

    if (!$token) {
        $token = $CI->sms->get_option('twilio', 'auth_token');
    }

    if (!$phone) {
        $phone = $CI->sms->get_option('twilio', 'phone_number');
    }

    $client = new Twilio\Rest\Client($sid, $token);

    try {
        $client->messages->create(
                // The number to send the SMS
                $number,
                array(
                     // A Twilio phone number you purchased at twilio.com/console
                    'from' => $phone,
                    'body' => $message,
                )
            );
        logActivity('SMS to send via Twilio to '.$number.', Message: '.$message);
    } catch (Exception $e) {
        logActivity('Failed to send SMS via Twilio: '.$e->getMessage());

        return false;
    }

    return true;
}
