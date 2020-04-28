<?php

class FreelanceAPI
{
    private $token='';

    function __construct($token)
    {
        $this->token=$token;
    }

    function getAPI($url){

            $token = $this->token;
            //setup the request, you can also use CURLOPT_URL
            $ch = curl_init($url);
            // Returns the data/output as a string instead of raw data
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //Set your auth headers
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ));
            // get stringified data/output. See CURLOPT_RETURNTRANSFER
            $data = curl_exec($ch);

            // get info about the request
            //$info = curl_getinfo($ch);
            // close curl resource to free up system resources
            curl_close($ch);
            return json_decode($data,1);
    }

    public function getProjects($page=0){
        $url=($page>0)?'https://api.freelancehunt.com/v2/projects?page[number]='.$page  :
            'https://api.freelancehunt.com/v2/projects';
        return $this->getAPI($url);
     }

     public function getProfile(){
        $url='https://api.freelancehunt.com/v2/my/profile';
        return $this->getAPI($url);
     }
     public function getSkills(){
        $url='https://api.freelancehunt.com/v2/skills';
        return $this->getAPI($url);
     }

}
