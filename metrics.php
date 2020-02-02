<?php

header("Content-type: text/plain; version=0.4.0");
require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/functions.metrics.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/metrics.config.php';
error_reporting(0);

echo "# mailcow prometheus metrics endpoint \n";

if ((isset($_GET['token'])) and ($token == $_GET['token'])) {

$curl_array = multi_curl(array("https://${mailcow_hostname}/api/v1/get/rspamd/actions",
                               "https://${mailcow_hostname}/api/v1/get/status/solr",
                               "https://${mailcow_hostname}/api/v1/get/mailbox/all",
                               "https://${mailcow_hostname}/api/v1/get/domain/all",
                               "https://${mailcow_hostname}/api/v1/get/mailq/all",
                               "https://${mailcow_hostname}/api/v1/get/syncjobs/all/no_log",
                               "https://${mailcow_hostname}/api/v1/get/quarantine/all"
), $mailcow_api_key);


// rspamd
$rspamd_stats_array = json_decode($curl_array[0], true);
$rspamd_total = $rspamd_stats_array[1][1] + $rspamd_stats_array[0][1] + $rspamd_stats_array[2][1] + $rspamd_stats_array[3][1] + $rspamd_stats_array[4][1];
echo "# rspamd metrics \n";
echo 'mailcow_rspamd_soft_reject_total ', $rspamd_stats_array[1][1], " \n";
echo 'mailcow_rspamd_reject_total ', $rspamd_stats_array[0][1], " \n";
echo 'mailcow_rspamd_rewrite_subject_total ', $rspamd_stats_array[2][1], " \n";
echo 'mailcow_rspamd_add_header_total ', $rspamd_stats_array[3][1], " \n";
echo 'mailcow_rspamd_no_action_total ', $rspamd_stats_array[4][1], " \n";
echo 'mailcow_rspamd_total ', $rspamd_total, " \n";

// solr
$solr_array = json_decode($curl_array[1], true);
if ($solr_array["solr_enabled"]) {
  echo "# solr metrics \n";
  echo 'mailcow_solr_documents_total ', $solr_array["solr_documents"], " \n";
}

// mailboxes
$mailbox_array = json_decode($curl_array[2], true);
echo "# mailbox metrics \n";
echo 'mailcow_mailboxes_total ', count($mailbox_array), " \n";
foreach ($mailbox_array as $value){
  $mailbox_array_messages += $value['messages'];
  $mailbox_array_quota_used += $value['quota_used'];
  $mailbox_array_quota += $value['quota'];
}
if (!empty($mailbox_array_messages)) {
  echo 'mailcow_mailboxes_messages_total ', $mailbox_array_messages, " \n";
}
if (!empty($mailbox_array_quota)) {
  echo 'mailcow_mailboxes_quota_total ', $mailbox_array_quota, " \n";
}
if (!empty($mailbox_array_quota_used)) {
  echo 'mailcow_mailboxes_quota_used_total ', $mailbox_array_quota_used, " \n";
}

// domains
$domain_array = json_decode($curl_array[3], true);
echo "# domain metrics \n";
echo 'mailcow_domains_total ', count($domain_array), " \n";
foreach ($domain_array as $value){
  $domain_aliases += $value['aliases_in_domain'];
}
echo 'mailcow_domains_aliases_total ', $domain_aliases, " \n";

// mailq
$mailq_array = json_decode($curl_array[4], true);
echo "# mailq metrics \n";
echo 'mailcow_mailq_total ', count($mailq_array), " \n";

// syncjobs
$synjob_array = json_decode($curl_array[5], true);
echo "# syncjobs \n";
echo 'mailcow_syncjobs_total ', count($synjob_array), " \n";

// quarantine
$quarantine_array = json_decode($curl_array[6], true);
echo "# quarantine \n";
echo 'mailcow_quarantine_total ', count($quarantine_array), " \n";

} else {
  http_response_code(401);
  echo "# unauthorized";
}

?>
