<?php
final class SirportlyAPI {

    private $_token, $_secret;
    private $_api_basepath = 'http://api.sirportly.com';

    public function __construct($token, $secret){
        $this->_token = $token;
        $this->_secret = $secret;
    }

    final private function _sendRequest($path, $params = array()){
        $return = false;

        try{
            $path = $this->_api_basepath.$path;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$path);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "X-Auth-Token:{$this->_token}",
                "X-Auth-Secret:{$this->_secret}",
            ));

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, API_TIMEOUT);

            if(!empty($params)){
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            }

            if(!$return = curl_exec($ch)){
                throw new LoggedException('Sirportly API unresponsive');
            } else {
                $return = json_decode($return);

                if(isset($return->error)){
                    throw new LoggedException("Sirportly API returned an error: {$return->error}");
                }
            }

            curl_close($ch);

        } catch (LoggedException $e) {
            return false;
        }

        return $return;
    }

    /**
     * TICKET METHODS
     */

    final public function listTickets(){
        $path = '/api/v2/tickets/all';

        return $this->_sendRequest($path);
    }

    final public function findTicket($query){
        $path = '/api/v2/tickets/search';
        $params = array(
            'query' => $query,
        );

        return $this->_sendRequest($path, $params);
    }

    final public function postToTicket($author_name, $author_email, $ticketRef, $message, $subject = null){

        $path = '/api/v2/tickets/post_update';
        $params = array(
            'ticket' => $ticketRef,
            'message' => $message,
            'author_name' => $author_name,
            'author_email' => $author_email,
        );

        // Check if the user exists, if so add the userID to the parameters
        if($user = $this->userLookupByEmail($author_email)){
            $params['user'] = $user->id;
        }

        if(!is_null($subject)){
            $params['subject'] = $subject;
        }

        return $this->_sendRequest($path, $params);
    }

    /**
     * USERS METHODS
     */

    final private function userLookup($data = array()){
        $path = '/api/v2/users/info';

        if(!empty($data)){
            $params = $data;
        }

        return $this->_sendRequest($path, $params);
    }

    final public function userLookupByEmail($email){
        return $this->userLookup(
            array(
                'user' => $email
            )
        );
    }

    final public function userLookupByUsername($username){
        return $this->userLookup(
            array(
                'user' => $username
            )
        );
    }

    final public function userLookupByID($id){
        return $this->userLookup(
            array(
                'user' => $id
            )
        );
    }
}