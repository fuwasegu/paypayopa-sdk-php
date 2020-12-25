<?php
require_once('TestBoilerplate.php');

use PayPay\OpenPaymentAPI\Models\AccountLinkPayload;
use PayPay\OpenPaymentAPI\Models\BasePaymentPayload;
use PayPay\OpenPaymentAPI\Models\CapturePaymentAuthPayload;
use PayPay\OpenPaymentAPI\Models\CreatePaymentAuthPayload;
use PayPay\OpenPaymentAPI\Models\CreatePaymentPayload;
use PayPay\OpenPaymentAPI\Models\CreatePendingPaymentPayload;
use PayPay\OpenPaymentAPI\Models\CreateQrCodePayload;
use PayPay\OpenPaymentAPI\Models\ModelException;
use PayPay\OpenPaymentAPI\Models\OrderItem;
use PayPay\OpenPaymentAPI\Models\RefundPaymentPayload;
use PayPay\OpenPaymentAPI\Models\RevertAuthPayload;

class PayloadsTest extends TestBoilerplate
{
    /**
     * Account Link payload test
     *
     * @return void
     */
    public function testAccountLinkPayload()
    {
        $test = new AccountLinkPayload();
        $test->setScopes(["direct_debit", "preauth_capture_native", "continuous_payments"]);
        $test->setNonce();
        $test->setRedirectType("WEB_LINK");
        $test->setRedirectUrl("https://merchant.com");
        $test->setReferenceId("CUSTOM_MERCHANT_IDENTIFIER");
        $test->setPhoneNumber("99897877732144");
        $test->setDeviceId(uniqid("devid_"));
        $test->setUserAgent("UC Browser");

        $this->assertIsArray($test->serialize());
        $collector[] = $test->getScopes();
        $collector[] = $test->getNonce();
        $collector[] = $test->getRedirectType();
        $collector[] = $test->getRedirectUrl();
        $collector[] = $test->getReferenceId();
        $collector[] = $test->getPhoneNumber();
        $collector[] = $test->getDeviceId();
        $collector[] = $test->getUserAgent();
    }

    /**
     * Basepayment check
     *
     * @return void
     */
    public function testBasePaymentPayload()
    {
        $test = new BasePaymentPayload();
        $test->setOrderReceiptNumber("SAMPLE_ORN_ID");
        $test->setUserAuthorizationId("SAMPLE_UA_ID");
        $this->assertIsArray($test->serialize());
        $collector[] = $test->getOrderReceiptNumber();
        $collector[] = $test->getUserAuthorizationId();
    }

    /**
     * Create payment auth payload 
     *
     * @return void
     */
    public function testPaymentCapturePayload()
    {
        $test = new CreatePaymentAuthPayload();

        $test->setMerchantPaymentId("TEST_MERCH_ID");
        $test->setUserAuthorizationId("TEST_AUTH_ID");
        $test->setAmount(['amount' => 20, 'currency' => 'JPY']);
        $test->setRequestedAt();
        
        $test->setExpiresAt($this->HourFromNow());

        $this->assertIsArray($test->serialize());
        $collector[] = $test->getMerchantPaymentId();
        $collector[] = $test->getUserAuthorizationId();
        $collector[] = $test->getAmount();
        $collector[] = $test->getRequestedAt();
        $collector[] = $test->getExpiresAt();
    }

    /**
     * Payment capture Payload
     *
     * @return void
     */
    public function testCapturePaymentPayload()
    {
        $test = new CapturePaymentAuthPayload();

        $test->setMerchantPaymentId("TEST_MERCHANT_PAYMENT_ID");
        $test->setAmount(['amount' => 20, 'currency' => 'JPY']);
        $test->setMerchantCaptureId("UNIQUE_MERCHANTPAYMENT_ID");
        $test->setRequestedAt();
        $test->setOrderDescription("WALAWALABINGBONG");

        $this->assertIsArray($test->serialize());

        $test->getMerchantPaymentId();
        $test->getAmount();
        $test->getMerchantCaptureId();
        $test->getRequestedAt();
        $test->getOrderDescription();
    }

    public function testCreatePaymentPayload()
    {

        $test = new CreatePaymentPayload();

        $test->setProductType("VIRTUAL_BONUS_INVESTMENT");
        $test->setMerchantPaymentId("TEST_MERCHANT_PAYMENT_ID");
        $test->setUserAuthorizationId("TEST_AUTH_ID");
        $test->setAmount(['amount' => 20, 'currency' => 'JPY']);
        $test->setRequestedAt();
        $this->assertIsArray($test->serialize());
        $test->getProductType();
    }
    private function HourFromNow()
    {
        $dt = new DateTime();
        $dt->add(new DateInterval('PT1H'));
        return $dt;
    }
    public function testCreatePendingPaymentPayload()
    {

        $test = new CreatePendingPaymentPayload();
        $test->setExpiryDate($this->HourFromNow());
        $test->setMerchantPaymentId("TEST_MERCHANT_PAYMENT_ID");
        $test->setUserAuthorizationId("TEST_AUTH_ID");
        $test->setAmount(['amount' => 20, 'currency' => 'JPY']);
        $test->setRequestedAt();
        $this->assertIsArray($test->serialize());
        $test->getExpiryDate();
    }

    /**
     * Checking payloads method chaining
     *
     * @return void
     */
    public function testCreateQrCode()
    {
        $test = new CreateQrCodePayload();
        $OrderItems = [];
        $OrderItems[] = (new OrderItem())->setName('Cake')->setQuantity(1)->setUnitPrice(['amount' => 20, 'currency' => 'JPY']);
        $test
            ->setMerchantPaymentId('Test123')
            ->setStoreInfo('Sample store information')
            ->setMetadata([
                "foo" => 'bar',
                "hello" => "world"
            ])
            ->setAmount(['amount' => 20, 'currency' => 'JPY'])
            ->setCodeType()
            ->setOrderItems($OrderItems)
            ->setOrderDescription('WillaWillaBingBong')
            ->setStoreId(uniqid("SampleStoreIdentifier_"))
            ->setTerminalId("SampleTerminalIdentifier_")
            ->setRequestedAt()
            ->setRedirectType("WEB_LINK")
            ->setRedirectUrl("https://merchant.domain/test/callback")
            ->setUserAgent("UC Browser")
            ->setIsAuthorization(false);
        
        $test->setAuthorizationExpiry($this->HourFromNow());

        $this->assertIsArray($test->serialize());

        $collector[] = $test->getMerchantPaymentId();
        $collector[] = $test->getStoreInfo();
        $collector[] = $test->getMetadata();
        $collector[] = $test->getAmount();
        $collector[] = $test->getCodeType();
        $collector[] = $test->getOrderItems();
        $collector[] = $test->getOrderDescription();
        $collector[] = $test->getStoreId();
        $collector[] = $test->getTerminalId();
        $collector[] = $test->getRequestedAt();
        $collector[] = $test->getRedirectType();
        $collector[] = $test->getRedirectUrl();
        $collector[] = $test->getUserAgent();
        $collector[] = $test->getIsAuthorization(false);
        $collector[] = $test->getAuthorizationExpiry();
    }

    /**
     * Order Item test
     *
     * @return void
     */
    public function testOrderItem()
    {
        $test = new OrderItem();
        $test->setName("TEST_PRODUCT");
        $test->setCategory("SAMPLE_CATEGORY_NAME");
        $test->setQuantity(12);
        $test->setProductId("UNIQUE_PRODUCT_ID");
        $test->setUnitPrice(['amount' => 20, 'currency' => 'JPY']);

        $this->assertIsArray($test->serialize());

        $test->getName();
        $test->getCategory();
        $test->getQuantity();
        $test->getProductId();
        $test->getUnitPrice();
    }
    /**
     * Refund Payload test
     *
     * @return void
     */
    public function testRefundPaymentPayload()
    {
        $test = new RefundPaymentPayload();
        $test->setMerchantRefundId("UNIQ_REFUND_ID");
        $test->setPaymentId("PAYPAY_PAYMENT_ID");
        $test->setAmount(['amount' => 20, 'currency' => 'JPY']);
        $test->setRequestedAt();
        $test->setReason("REASON_DESCRIPTION");

        $this->assertIsArray($test->serialize());

        $test->getMerchantRefundId();
        $test->getPaymentId();
        $test->getAmount();
        $test->getRequestedAt();
        $test->getReason();
    }
    /**
     * Revert Payload
     *
     * @return void
     */
    public function testRevertAuthPayload()
    {
        $test = new RevertAuthPayload();
        $test->setMerchantRevertId("UNIQ_REVERT_ID");
        $test->setPaymentId("PAYPAY_PAYMENT_ID");
        $test->setRequestedAt();
        $test->setReason("REASON_DESCRIPTION");

        $this->assertIsArray($test->serialize());

        $test->getMerchantRevertId();
        $test->getPaymentId();
        $test->getRequestedAt();
        $test->getReason();
    }

    public function testModelFailures()
    {
        $test = new CapturePaymentAuthPayload();
        //Empty property string
        $test->setMerchantPaymentId("");
        $test->setAmount(['amount' => 20, 'currency' => 'JPY']);
        $test->setMerchantCaptureId("UNIQUE_MERCHANTPAYMENT_ID");
        $test->setRequestedAt();
        $test->setOrderDescription("WALAWALABINGBONG");
        try {
            $test->validate();
        } catch (ModelException $e) {
            $this->assertStringContainsString("cannot be empty", $e->getMessage());
        } finally {
            try {
                // Exceeed max string length
                $test->setMerchantPaymentId(GetRand(69));
            } catch (ModelException $e) {
                $this->assertStringContainsString("exceeds maximum size of  characters", $e->getMessage());
            }
        }
    }
    public function testMultiFieldFailure()
    {
        $test = new CapturePaymentAuthPayload();
        //Empty property string
        $test->setMerchantPaymentId(5);
        $test->setMerchantCaptureId("UNIQUE_MERCHANTPAYMENT_ID");
        $test->setRequestedAt();
        $test->setOrderDescription(2);
        try {
            $test->validate();
        } catch (ModelException $e) {
            $this->assertNotEmpty($e->fields);
        }
    }
}
