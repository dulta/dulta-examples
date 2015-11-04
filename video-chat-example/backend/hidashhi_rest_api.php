<?php
/*
    Dulta - Video chat example.

    For documentation and more examples see: http://github.com/dulta

    (c) 2015 Dulta
    THIS WORK ‘AS-IS’ WE PROVIDE.
    NO WARRANTY EXPRESS OR IMPLIED.
    WE’VE DONE OUR BEST, TO DEBUG AND TEST.
    LIABILITY FOR DAMAGES DENIED.

    PERMISSION IS GRANTED HEREBY, TO COPY, SHARE, AND MODIFY.
    USE AS IS FIT, FREE OR FOR PROFIT.
    THESE RIGHTS, ON THIS NOTICE, RELY.
*/

/**
 * Dulta REST API example helper.
 */
class DultaRestApi {

  /** @type string Your application key */
  protected $appKey;

  /** @type string Dulta Rest API url */
  protected $restUrl;


  /**
   * Constructor
   *
   * @param string $apiKey    Your application key
   * @param string $restUrl   Rest API url
   */
  public function __construct($apiKey, $restUrl) {
    $this->apiKey = $apiKey;
    $this->restUrl = $restUrl;
  }

  /**
   * Retrieve accesstoken by identifier (<user-identifier>@<application-domain>)
   * E.g.: userABCDEF1234@user.somedomain.com
   *
   * @param  string $identifier Email-formatted identifier (E.g. userABCDEF1234@user.somedomain.com)
   *
   * @throws Exception If apiKey or result is invalid.
   *
   * @return array              JSON decoded result
   */
  public function retrieveAccessToken($identifier) {
    $path = '/guestToken';
    $data = array('email' => $identifier);
    $result = $this->execute($path, 'POST', $data);
    if (array_key_exists('result', $result) && $result['result'] === 'unauthorized') {
      throw new Exception('Dulta Application key is invalid.');
    }
    if (array_key_exists('result', $result) && $result['result'] === 'error') {
      throw new Exception("Dulta error occured: ".$result['message']." || ".$result['code']);
    }
    return $result;
  }

  /**
   * Execute a Dulta REST API call.
   *
   * @param  string $path   REST Api path (e.g. "/guestToken")
   * @param  string $method HTTP Method ("GET"/"POST")
   * @param  array  $data   Data fields (key/value array)
   *
   * @return array          JSON decoded result.
   */
  public function execute($path, $method = "GET", $data = array()) {
    $curl = curl_init();

    $url = sprintf("%s%s", $this->restUrl, $path);
    if (!array_key_exists("apiKey", $data)) {
      $data["apiKey"] = $this->apiKey;
    }

    switch ($method){
      case "POST":
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data) {
          curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        break;
      case "PUT":
        curl_setopt($curl, CURLOPT_PUT, 1);
        if ($data) {
          $url = sprintf("%s?%s", $url, http_build_query($data));
        }
        break;
      default:
        if ($data) {
          $url = sprintf("%s?%s", $url, http_build_query($data));
        }
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

    $result = curl_exec($curl);

    return json_decode($result, true);
  }
}

?>
