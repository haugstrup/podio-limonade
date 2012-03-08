<?php
require_once 'vendor/limonade.php';
require_once 'vendor/podio-php/PodioAPI.php';

function configure() {
  option('CLIENT_ID', 'YOUR_CLIENT_ID'); // Replace with your client ID
  option('CLIENT_SECRET', 'YOUR_CLIENT_SECRET'); // Replace with your client secret

  layout('layout.php');
  error_layout('layout.php');
  option('REDIRECT_URI', 'http://'.$_SERVER['HTTP_HOST'].url_for('authorize/callback'));
}

dispatch('/', 'root');
function root() {
  return render('<h1>Podio Authentication sample</h1><p><a href="https://podio.com/oauth/authorize?response_type=code&client_id='.option('CLIENT_ID').'&redirect_uri='.rawurlencode(option('REDIRECT_URI')).'">Try to authorize</a>.</p>');
}

dispatch('/authorize/callback', 'authorize');
function authorize() {
  try {
    $api = Podio::instance(option('CLIENT_ID'), option('CLIENT_SECRET'));
    $api->authenticate('authorization_code', array('code' => $_GET['code'], 'redirect_uri' => option('REDIRECT_URI')));
    return render("<p>Your access token is {$api->oauth->access_token}</p>");
  }
  catch(PodioError $e) {
    return render("<p>There was an error. The API responded with the error type <b>{$e->body['error']}</b> and the message <b>{$e->body['error_description']}. <a href='".url_for('/')."'>Retry</a></p>");
  }
}

run();
