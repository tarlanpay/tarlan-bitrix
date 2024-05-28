<?php

namespace Sale\Handlers\PaySystem;

use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Request;
use Bitrix\Main\Text\Encoding;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Sale\BusinessValue;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PriceMaths;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Type;

Loc::loadMessages(__FILE__);

/**
 * Class TarlanPaymentsHandler
 * @package Sale\Handlers\PaySystem
 */
class TarlanPaymentsHandler
    extends PaySystem\ServiceHandler
{
    const PAYMENT_STATUS_SUCCESS = "success";
    const INVOICE_ID_DELIMITER = "#";

    /**
     * @param Payment $payment
     * @param Request|null $request
     * @return PaySystem\ServiceResult
     */
    // initiatePay - является ключевым методом, связующим заказ в магазине с
    // выбранной платежной системой (ПС). На данном этапе раскрывается шаблон ПС
    public function initiatePay(Payment $payment, Request $request = null)
    {
        $invoiceData = $this->createInvoice($payment);
        
        $domain = $this->getBusinessValue($payment, "DOMAIN_GATEWAY");
        
        $isTestMode = $this->isTestMode();
        
        if ($isTestMode){
            $domain = $this->getBusinessValue($payment, "TEST_DOMAIN_GATEWAY");
        };

        $createPrimalUrl = $domain . $this->getBusinessValue($payment, "PRIMAL_IN_URL");

        $invoiceData["CURRENCY"] = $payment->getField("CURRENCY");
        $invoiceData["URL"] = $createPrimalUrl;

        $this->setExtraParams($invoiceData);

        return $this->showTemplate($payment, "template");
    }

    /**
     * @param Payment $payment
     * @return mixed
     */
    protected function isTestMode(Payment $payment = null)
    {
        return ($this->getBusinessValue($payment, 'PS_IS_TEST') === 'Y');
    }
    
    /**
     * @param Payment $payment
     * @return mixed|string
     */
    private function getSuccessUrl(Payment $payment)
    {
        return $this->getBusinessValue($payment, 'SUCCESS_REDIRECT_URL');
    }
   

    /**
     * @param Payment $payment
     * @return mixed|string
     */
    private function getFailureUrl(Payment $payment)
    {
        return $this->getBusinessValue($payment, 'FAILURE_REDIRECT_URL');
    }

    /**
     * @param Payment $payment
     * @return mixed|string
     */
    private function getCallbackUrl(Payment $payment)
    {
        return $this->getBusinessValue($payment, 'CALLBACK_URL');
    }

    /**
     * @param Payment $payment
     * @return mixed|string
     */
    private function getMerchantID(Payment $payment)
    {
        return $this->getBusinessValue($payment, 'MERCHANT_ID');
    }

    /**
     * @param Payment $payment
     * @return mixed|string
     */
    private function getProjectID(Payment $payment)
    {
        return $this->getBusinessValue($payment, 'PROJECT_ID');
    }

    /**
     * @param Payment $payment
     * @return mixed|string
     */
    private function getSecretKey(Payment $payment)
    {
        return $this->getBusinessValue($payment, 'SECRET_KEY');
    }

    // Из данных платежа собирает подпись для авторизации
    private function prepareSign(string $secretKey, array $array_data): string
    {
        $payload = $array_data["project_reference_id"] . $array_data["status_code"] . $array_data["amount"];

        // Concatenate the base64-encoded data with the secret
        $dataToSign = $payload . $secretKey;

        $hashValue = hash("sha256", $dataToSign);

        // Hash the result to SHA-256
        return hash("sha256", $dataToSign);
    }

    // createInvoice - подготавливает всю информацию о платеже, дополнительно включая параметры
    // из настроек платежной системы для проекта.
    private function createInvoice(Payment $payment): array
    {
        $merchantID = $this->getMerchantID($payment);
        $projectID = $this->getProjectID($payment);

        $amount = number_format((float)$payment->getSum(), 2, '.', '');

        $hash = $this->prepareHash($payment, $amount);

        return [
            "merchant_project_id" => $merchantID . ":" . $projectID,
            "project_hash" => $hash,
            "project_reference_id" => $payment->getField("ID"),
            "callback_url" => $this->getCallbackUrl($payment),
            "description" => $this->service->getField("ID") . "-bitrix",
            "amount" => $amount,
            "success_redirect_url" => $this->getSuccessUrl($payment),
            "failure_redirect_url" => $this->getFailureUrl($payment),
            "project_order_id" => $this->getFailureUrl($payment),
        ];
    }

    private function prepareHash(Payment $payment, string $amount): string
    {
        $projectHash = $payment->getField("ID") . $amount;

        $secretKey = $this->getSecretKey($payment);

        $dataToSign = $projectHash . $secretKey;

        return hash("sha256", $dataToSign);
    }

    public function processRequest(Payment $payment, Request $request)
    {
        $result = new PaySystem\ServiceResult();

        $inputStream = self::readFromStream();

        // $callbackData - ответ от TarlanPayments в виде json объекта со всеми параметрами,
        // относящимися к платежу
        $callbackData = json_decode($inputStream, true);
        // $callbackData["project_hash"] - авторизационный хэш.
        // Проверка на валидность запроса
        if ($this->checkSign($payment, $callbackData)) {
            $paymentId = $callbackData["project_reference_id"];

            // При успешной авторизации запроса с колбека идет проверка на статус транзакции в системе TarlanPayments
            if ($callbackData["status_code"] === self::PAYMENT_STATUS_SUCCESS && $paymentId) {
                $description = Loc::getMessage("SALE_HPS_TARLANPAYMENTS_TRANSACTION", [
                    "#ID#" => $callbackData["transaction_id"],
                    "#PAYMENT_NUMBER#" => $callbackData["project_reference_id"]
                ]);

                $invoiceId = $callbackData["transaction_id"] ?? $callbackData["project_reference_id"];
                $fields = array(
                    "PS_INVOICE_ID" => $invoiceId . self::INVOICE_ID_DELIMITER . $paymentId,
                    "PS_STATUS_CODE" => $callbackData["status_code"],
                    "PS_STATUS_DESCRIPTION" => $description,
                    "PS_SUM" => $callbackData["amount"],
                    "PS_STATUS" => "Y",
                    "PS_RESPONSE_DATE" => new DateTime()
                );

                $ysChng = $this->getBusinessValue($payment, "PS_CHANGE_STATUS_PAY");

                if ($this->getBusinessValue($payment, "PS_CHANGE_STATUS_PAY") === "Y") {
                    $result->setOperationType(PaySystem\ServiceResult::MONEY_COMING);
                }

                $result->setPsData($fields);
            }
        } else {
            $error = Loc::getMessage("SALE_HPS_TARLANPAYMENTS_ERROR_SIGN");

            $result->addError(new Error($error));
        }


        if (!$result->isSuccess()) {
            $error = __CLASS__ . ": processRequest: " . join("\n", $result->getErrorMessages());
            PaySystem\Logger::addError($error);
        }
        
        return $result;
    }

    /**
     * @return bool|string
     */
    private static function readFromStream()
    {
        return file_get_contents("php://input");
    }

    /**
     * @param $sign
     * @param Payment $payment
     * @param array $callbackData
     * @return bool
     */
    private function checkSign(Payment $payment, array $callbackData)
    {
        $secretKey = (string)$this->getBusinessValue($payment, "SECRET_KEY");

        $sign = $callbackData["project_hash"];

        unset($callbackData["project_hash"]);

        $generatedSign = $this->prepareSign($secretKey, $callbackData);

        return $sign === $generatedSign;
    }

    // Следующие методы являются реализацией абстрактых функций

    public function getPaymentIdFromRequest(Request $request)
    {
        $inputStream = self::readFromStream();

        $callBackData = json_decode($inputStream, true);

        return $callBackData["project_reference_id"];

    }

    /**
     * @return array
     */
    public function getCurrencyList()
    {
        return ["KZT", "RUB"];
    }

    /**
     * @return array
     */
    static public function getIndicativeFields()
    {
        return array('BX_HANDLER' => 'TARLANPAYMENTS');
    }

    // isMyResponse - Выполняется неявно для авторизации получаемого запроса от callback
    // Выставляем дефолтное значение true, т.к снаружи выполняется другая проверка checkSign
    public static function isMyResponse(Request $request, $paySystemId)
    {
        return true;
    }

    /**
     * @param Request $request
     * @param $paySystemId
     * @return bool
     */
    static protected function isMyResponseExtended(Request $request, $paySystemId)
    {
        $id = $request->get('BX_PAYSYSTEM_CODE');

        return (int)$id === (int)$paySystemId;
    }
}