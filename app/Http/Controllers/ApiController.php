<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\SpamWords;
use App\Models\blacklisted;

class ApiController extends Controller
{
    /**
     * Send Mail.
     *
     * @param MailRequest $request
     * @return Redirect
     */
    
    
    /**
     * Check if the content includes spam words or not
     * Checking if there are spam words or not
     *
     */
    private function CheckForSpamWords($subject, $name, $body)
    {
        $spam_DB_words = DB::table('spam_words')->select('spamwords')->get();
        $spam_words    = explode(', ', $spam_DB_words);
        
        
        $detectedSpamWords                     = new \stdClass();
        $detectedSpamWords->spamWordsinSubject = array();
        $detectedSpamWords->spamWordsinName    = array();
        $detectedSpamWords->spamWordsinBody    = array();
        
        foreach ($spam_words as $spam_word) {
            
            if (strpos($subject, $spam_word) !== false) {
                $detectedSpamWords->spamWordsinSubject[] = $spam_word;
            }
            if (strpos($spam_word, $name) !== false) {
                $detectedSpamWords->spamWordsinName[] = $spam_word;
            }
            if (strpos($body, $spam_word) !== false) {
                $detectedSpamWords->spamWordsinBody[] = $spam_word;
            }
        }
        return $detectedSpamWords;
    }
    
    
    /**
     * 
     * Check the html is valid or not is checked
     */
    
    
    
    private function CheckHtml($body)
    {
        $errorList = array();
        
        if ($body) {
            $doc = new \DOMDocument();
            $doc->loadHTML($body);
            $xml = simplexml_import_dom($doc);
            /**
             * Checking all hyperlinks and images alt attributtes
             * 3. All hyperlinks and image tags should have Alt properties
             */
            
            $imagestest = $xml->xpath('//img');
            if (!empty($imagestest)) {
                foreach ($imagestest as $imgalt) {

                    $file_headers = @get_headers($imgalt['src']);
                    if (!$file_headers || (isset($file_headers[0]) && $file_headers[0] == 'HTTP/1.1 404 Not Found')) {
                        $errorList[] = "Image Not Found -> " . $imgalt["src"];
                    }
                    
                    if (!isset($imgalt['alt']) || empty($imgalt['alt'])) {
                        $errorList[] = "Image alt is missing -> " . $imgalt['src'];
                    }
            
                    if (!in_array(strtolower(pathinfo($imgalt['src'], PATHINFO_EXTENSION)), array('gif','jpg','jpeg','png','x-png','gif'))) {
                     $errorList[] = 'not image';
                    } 

                }
            }
            
            
            /**
             *
             * Check the links, i.e. if they are working links or not
             */
            
            
            $linktest = $xml->xpath('//a');
            if (!empty($linktest)) {
                foreach ($linktest as $linktesthref) {
                    
                    if (!empty($linktesthref['href'])) {
                        
                        $file_headers = @get_headers($linktesthref['href']);
                        if (!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
                            $errorList[] = "URL not found -> " . $linktesthref['href'];
                        }
                    }
                }
            }
			
			
			
			
			/**
             *
             * Check in body the links, i.e. if they are working links or not
             */
            $reg_exUrl = "#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#";

            if(preg_match_all($reg_exUrl, $body, $urls) > 0) {

                foreach ($urls[0] as $url) {

                   $file_headers = @get_headers($url);

                    if (!$file_headers || (isset($file_headers[0]) && $file_headers[0] == 'HTTP/1.1 404 Not Found')) {

                      $errorList[] = "link Not Found -> " . $url;
                   }
                } 
            }
            
            libxml_clear_errors();
            
            $doc->loadHTML($body);
            $errors = libxml_get_errors();
            if ($errors) {
                foreach ($errors as $errorshtml) {
                    $errorList[] = "HTML error" . $errorshtml->message . "    line " . $errorshtml->line;
                }
            }
        }
        
        return $errorList;
    }
    /**
     * 1. Email Format
     *
     */
    
    
    private function CheckEmail($email)
    {
        /**
         * 
         * Check the format of the mail, i.e. the format corresponds or not
         *
         */
        
        $emailInfo           = new \stdClass();
        $emailInfo->isValid  = true;
        $emailInfo->provider = "";
        
        $email_domain = preg_replace('/^.+?@/', '', $email) . '.';
        
        if (!checkdnsrr($email_domain, 'MX') && !checkdnsrr($email_domain, 'A')) {
            $emailInfo->isValid = false;
        }
        /**
         * Check which type  the mail belongs to
         */
          $email_domains = DB::table('email_domains')->select('email')->get();
           foreach ( $email_domains as  $email_domains_fm) {
        if (preg_match('/'.$email_domains_fm->email.'/i', $email)) {
            $emailInfo->provider = "Public Provider";
             break;
        } else {
            $emailInfo->provider = "Company's Email Server";
        }
           }
        return $emailInfo;
        
    }
    
    /**
     *
     *6. Check if Sender’s Email Domain is blacklistedor not.
     */
    
    private function CheckBlacklistedor($ip)
    {
        
        $errorList = array();
        
        $dnsbl_lookup = DB::table('blacklisted')->select('blacklisted_host')->get();
        $listed       = array();
        if ($ip) {
            $reverse_ip = implode(".", array_reverse(explode(".", $ip)));
            
            foreach ($dnsbl_lookup as $host) {
                if (checkdnsrr($reverse_ip . "." . $host->blacklisted_host . ".", "A")) {
                    $listed[] = $host->blacklisted_host;
                }
            }
        }
         if(count($listed) > 0) {
        $errorList[] =$host->blacklisted_host.'  Listed';
        }
        
        if (isset($ip) && $ip != null) {
            $ip = $ip;
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                //echo $ip;
            } else {
                $errorList[] = "Please enter a valid IP";
            }
        }
        return $errorList;
        
    }
    public function ApiMail(Request $request)
    {
        $rezult  = 0;
        $name    = $request->input('name');
        $email   = $request->input('email');
        $subject = $request->input('subject');
        $body    = $request->input('body');
		
		
		
			$ip ='';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ip = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ip = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ip = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ip = $_SERVER['REMOTE_ADDR'];
		else
       
		
		
        libxml_use_internal_errors(true);
        
        $responseObj             = new \stdClass();
        $responseObj->status     = "OK";
        $responseObj->error_list = array();
        $responseObj->incoming_date = array();
        /**
         * INPUT DATA
         *
         *1. Email Subject
         *2. Sender Name
         *3. Sender Email
         *4. Email Content (as HTML)
         *
         * First we check whether they exist or not
         */
        
        
        if (!$name) {
            $responseObj->status       = "FAILED";
            $responseObj->error_list[] = 'Sender name cannot be empty';
        }else{
           
           $responseObj->incoming_date[]= 'name :: '.$name;

        }
        
        if (!$email) {
            $responseObj->status       = "FAILED";
            $responseObj->error_list[] = 'Sender email cannot be empty';
            
        }else{
          
           $responseObj->incoming_date[]= 'email :: '.$email;

        }
        
        
        if (!$subject) {
            $responseObj->status       = "FAILED";
            $responseObj->error_list[] = 'Subject cannot be empty';
        }else{
          
           $responseObj->incoming_date[]= 'subject :: '.$subject;

        }
        if($ip){
           $responseObj->incoming_date[]= 'ip :: '.$ip ;
        }
        
        if (!$body) {
            
            $responseObj->status       = "FAILED";
            $responseObj->error_list[] = 'Email content cannot be empty';
            
        } else {
             $responseObj->incoming_date[]= 'body :: '.html_entity_decode($body);
            $responseObj->content_plain_tet_size = strlen(strip_tags($body));
            $responseObj->content_html_size      = strlen($body);
        }


        
        /**
         * 
         *   call the  CheckForSpamWords  function
         *  with this $subject, $name, $body arguments   
         * 
         **/
        $detectedSpamWords = $this->CheckForSpamWords($subject, $name, $body);
        
        
        if (count($detectedSpamWords->spamWordsinSubject) > 0) {
            $responseObj->status       = "FAILED";
            $responseObj->error_list[] = 'Email Subject contains spam words';
        }
        
        if (count($detectedSpamWords->spamWordsinName) > 0) {
            $responseObj->status       = "FAILED";
            $responseObj->error_list[] = 'Sender name contains spam words';
        }
        
        if (count($detectedSpamWords->spamWordsinBody) > 0) {
            $responseObj->status       = "FAILED";
            $responseObj->error_list[] = 'Email body contains spam words';
        }
        
        $responseObj->spam_words = $detectedSpamWords;
        
        /**
         * 
         *  call the  CheckHtml  function
         *  with the $body argument   
         **/
        
        
        $htmlErrors = $this->CheckHtml($body);
        if (count($htmlErrors) > 0) {
            $responseObj->status     = "FAILED";
            $responseObj->error_list = array_merge($responseObj->error_list, $htmlErrors);
        }
        
        /**
         *  call the  CheckEmail  function
         *  with the $email argument   
         * 
         **/
        
        
        $emailInfo = $this->CheckEmail($email);
        if (!$emailInfo->isValid) {
            $responseObj->status = "FAILED";
        }
        
        $responseObj->emailInfo = $emailInfo;
        
        
        
        /**
         *  call the  CheckBlacklistedor  function
         *  with the $ip argument  
         **/
        
        $blacklistErrors = $this->CheckBlacklistedor($ip);
        if (count($blacklistErrors) > 0) {
            $responseObj->status     = "FAILED";
            $responseObj->error_list = array_merge($responseObj->error_list, $blacklistErrors);
        }
        
        
        $responseJSON = json_encode($responseObj);
        echo $responseJSON;
        
    }
    
    
}
