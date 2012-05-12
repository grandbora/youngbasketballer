<?php
/**
 * Extension Class for facebook
 */

require_once("facebook.php");

/**
 * Extends the BaseFacebook class with the intent of adding extra functionalities
 */
class FacebookApi_Extension extends Facebook
{
    /**
     * Make an API call to $userId/apprequests
     *
     * @param int $userId
     * @param int $message
     * @return array(requestid, to)
     * @throws FacebookApiException
     */
    public function makeAppRequest($userId, $message) {

        $applicationAccessToken = $this->requestApplicationAccessToken();
        if (false === $applicationAccessToken)
        throw new FacebookApiException(array('error' => "Failed to retrieve ApplicationAccessToken."));

        $paramList = array("message" => $message);
        $paramList[] = $applicationAccessToken;

        return $this->_graph($userId ."/apprequests",'POST', $paramList);
    }

    /**
     * Delete an existing apprequest whose Id given
     *
     * @param int $requestId
     * @param int $userId
     * @return
     * @throws FacebookApiException
     */
    public function deleteAppRequest($requestId, $userId) {
        return $this->_graph($requestId."_".$userId,'DELETE', array("access_token" => $this->getAccessToken()));
    }

    /**
     * Makes an oauth request to get an application access token
     *
     * @return mixed An access token or false if an access token could not be generated.
     */
    protected function requestApplicationAccessToken() {

        $url = $this->getUrl('graph', '/oauth/access_token');
        $paramList = array(
        		"client_id" => $this->getAppId(),
        		"client_secret" => $this->getApiSecret(),
        		"grant_type" => "client_credentials"
        );
        $access_token_response = $this->makeRequest($url, $paramList);

        $response_params = array();
        parse_str($access_token_response, $response_params);
        if (!isset($response_params['access_token'])) {
            return false;
        }

        return $response_params['access_token'];
    }
}