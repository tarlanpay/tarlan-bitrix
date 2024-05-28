<?php
use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Sale\PaySystem;


Loc::loadMessages(__FILE__);

$description = array(
    'RETURN' => Loc::getMessage('SALE_HPS_TARLANPAYMENTS_RETURN'),
    'RESTRICTION' => Loc::getMessage('SALE_HPS_TARLANPAYMENTS_RESTRICTION'),
    'COMMISSION' => Loc::getMessage('SALE_HPS_TARLANPAYMENTS_COMMISSION'),
);

$isAvailable = PaySystem\Manager::HANDLER_AVAILABLE_TRUE;

$portalZone = Loader::includeModule('intranet') ? CIntranetUtils::getPortalZone() : "";
$licensePrefix = Loader::includeModule('bitrix24') ? \CBitrix24::getLicensePrefix() : "";

if (Loader::includeModule('bitrix24'))
{
    if ($licensePrefix !== 'ru')
    {
        $isAvailable = PaySystem\Manager::HANDLER_AVAILABLE_FALSE;
    }
}
elseif (Loader::includeModule('intranet') && $portalZone !== 'ru')
{
    $isAvailable = PaySystem\Manager::HANDLER_AVAILABLE_FALSE;
}

$data = array(
    'NAME' => Loc::getMessage('SALE_HPS_TARLANPAYMENTS'),
    'SORT' => 500,
    'IS_AVAILABLE' => $isAvailable,
    'CODES' => array(
        "MERCHANT_ID" => array(
            "NAME" => GetMessage("SALE_TARLANPAYMENTS_MERCHANT_ID"),
            "DESCR" => GetMessage("SALE_TARLANPAYMENTS_MERCHANT_ID_DESC"),
            "VALUE" => "",
            'GROUP' => GetMessage("CONNECT_SETTINGS_TARLANPAYMENTS"),
            'SORT' => 310,
            "TYPE" => ""
        ),
        "PROJECT_ID" => array(
            "NAME" => GetMessage("SALE_TARLANPAYMENTS_PROJECT_ID"),
            "DESCR" => GetMessage("SALE_TARLANPAYMENTS_PROJECT_ID_DESC"),
            "VALUE" => "",
            'GROUP' => GetMessage("CONNECT_SETTINGS_TARLANPAYMENTS"),
            'SORT' => 310,
            "TYPE" => ""
        ),
        "SECRET_KEY" => array(
            "NAME" => GetMessage("SALE_TARLANPAYMENTS_SECRET_KEY"),
            "DESCR" => GetMessage("SALE_TARLANPAYMENTS_SECRET_KEY_DESC"),
            "VALUE" => "",
            'GROUP' => GetMessage("CONNECT_SETTINGS_TARLANPAYMENTS"),
            'SORT' => 320,
            "TYPE" => ""
        ),
        "DOMAIN_GATEWAY" => array(
            "NAME" => GetMessage("SALE_TARLANPAYMENTS_DOMAIN_GATEWAY"),
            "DESCR" => GetMessage("SALE_TARLANPAYMENTS_DOMAIN_GATEWAY_DESC"),
            "VALUE" => "prapi.tarlanpayments.kz",
            'GROUP' => GetMessage("CONNECT_SETTINGS_TARLANPAYMENTS"),
            'SORT' => 330,
            "TYPE" => ""
        ),
        "TEST_DOMAIN_GATEWAY" => array(
            "NAME" => GetMessage("SALE_TARLANPAYMENTS_SANDBOX_DOMAIN_GATEWAY"),
            "DESCR" => GetMessage("SALE_TARLANPAYMENTS_SANDBOX_DOMAIN_GATEWAY_DESC"),
            "VALUE" => "sandboxapi.tarlanpayments.kz",
            'GROUP' => GetMessage("CONNECT_SETTINGS_TARLANPAYMENTS"),
            'SORT' => 330,
            "TYPE" => ""
        ),
        "PRIMAL_IN_URL" => array(
            "NAME" => GetMessage("SALE_TARLANPAYMENTS_PRIMAL_IN_URL"),
            "DESCR" => GetMessage("SALE_TARLANPAYMENTS_PRIMAL_IN_URL_DESC"),
            "VALUE" => "/transaction/api/v1/transaction/primal/pay-in",
            'GROUP' => GetMessage("CONNECT_SETTINGS_TARLANPAYMENTS"),
            'SORT' => 330,
            "TYPE" => ""
        ),
        "SUCCESS_REDIRECT_URL" => array(
            "NAME" => GetMessage("SALE_TARLANPAYMENTS_SUCCESS_REDIRECT_URL"),
            "DESCR" => GetMessage("SALE_TARLANPAYMENTS_SUCCESS_REDIRECT_URL_DESC"),
            "VALUE" => "https://github.com/skip2/go-qrcode",
            'GROUP' => GetMessage("CONNECT_SETTINGS_TARLANPAYMENTS"),
            'SORT' => 360,
            "TYPE" => ""
        ),
        "FAILURE_REDIRECT_URL" => array(
            "NAME" => GetMessage("SALE_TARLANPAYMENTS_FAILURE_REDIRECT_URL"),
            "DESCR" => GetMessage("SALE_TARLANPAYMENTS_FAILURE_REDIRECT_URL_DESC"),
            "VALUE" => "https://www.youtube.com/",
            'GROUP' => GetMessage("CONNECT_SETTINGS_TARLANPAYMENTS"),
            'SORT' => 380,
            "TYPE" => ""
        ),
        "CALLBACK_URL" => array(
            "NAME" => GetMessage("SALE_TARLANPAYMENTS_CALLBACK_URL"),
            "DESCR" => GetMessage("SALE_TARLANPAYMENTS_CALLBACK_URL_DESC"),
            "VALUE" => "https://webhook.site/f9abe8c2-f47c-4df2-a642-531572c894fe",
            'GROUP' => GetMessage("CONNECT_SETTINGS_TARLANPAYMENTS"),
            'SORT' => 400,
            "TYPE" => ""
        ),
        "PAYMENT_SHOULD_PAY" => array(
            "NAME" => Loc::getMessage("SALE_TARLANPAYMENTS_SHOULD_PAY"),
            'SORT' => 600,
            'GROUP' => 'PAYMENT',
            'DEFAULT' => array(
                'PROVIDER_KEY' => 'PAYMENT',
                'PROVIDER_VALUE' => 'SUM'
            )
        ),
        "PS_CHANGE_STATUS_PAY" => array(
            "NAME" => Loc::getMessage("SALE_TARLANPAYMENTS_STATUS_PAY"),
            'SORT' => 700,
            'GROUP' => 'GENERAL_SETTINGS',
            "INPUT" => array(
                'TYPE' => 'Y/N'
            ),
            'DEFAULT' => array(
                "PROVIDER_KEY" => "INPUT",
                "PROVIDER_VALUE" => "Y",
            )
        ),
        "PS_IS_TEST" => array(
            "NAME" => Loc::getMessage("SALE_TARLANPAYMENTS_IS_TEST"),
            'SORT' => 900,
            'GROUP' => 'GENERAL_SETTINGS',
            "INPUT" => array(
                'TYPE' => 'Y/N'
            )
        ),
    )
);