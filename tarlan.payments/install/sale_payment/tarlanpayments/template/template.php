<?
use Bitrix\Main\Localization\Loc;
\Bitrix\Main\Page\Asset::getInstance()->addCss("/bitrix/themes/.default/sale.css");
Loc::loadMessages(__FILE__);

$amount = round($params['PAYMENT_SHOULD_PAY'], 2);
?>
<div class="mb-4" >
    <p><?=Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_TARLANPAYMENTS_DESCRIPTION')." <strong>".SaleFormatCurrency($params['PAYMENT_SHOULD_PAY'], $payment->getField('CURRENCY'))."</strong>";?></p>
    <form id="paysystem-tarlanpayments-form" name="PayForm" action="<?=$params['URL'];?>" method="post">
        <input type="hidden" name="merchant_project_id" value="<?=$params['merchant_project_id'];?>">
        <input type="hidden" name="callback_url" value="<?=$params['callback_url'];?>">
        <input type="hidden" name="success_redirect_url" value="<?=$params['success_redirect_url'];?>">
        <input type="hidden" name="failure_redirect_url" value="<?=$params['failure_redirect_url'];?>">
        <input type="hidden" name="project_order_id" value="<?=$params['project_order_id'];?>">
        <input type="hidden" name="project_reference_id" value="<?=$params['project_reference_id'];?>">
        <input type="hidden" name="amount" value="<?=$amount;?>">
        <input type="hidden" name="description" value="<?=$params['description'];?>">
        <input type="hidden" name="project_hash" value="<?=$params['project_hash'];?>">

        <input type="image" class="mw-100" name="submit">

        <div class="d-flex align-items-center justify-content-start mb-4">
            <p class="m-0 p-3"><?=Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_TARLANPAYMENTS_REDIRECT_MESS');?></p>
        </div>

        <div class="alert alert-info"><?=Loc::getMessage('SALE_HANDLERS_PAY_SYSTEM_TARLANPAYMENTS_WARNING_RETURN');?></div>
    </form>
</div>

