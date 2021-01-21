<?php
require_once('TestBoilerplate.php');

use PayPay\OpenPaymentAPI\Models\AccountLinkPayload;

class DecodeAccountLinkResponseTest extends BoilerplateTest
{
    public function testDecode()
    {
        $this->InitCheck();
        $uaresponse  = $this->config['uaresponse'];
        try {
            $data = $this->client->user->decodeUserAuth($uaresponse);
            var_dump($data);
        } catch (\Throwable $th) {
            $message = $th->getMessage();
            $this->assertNotEmpty($message, "Blank error");
        }
    }
}