<?php
/**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
* Title   : Conekta Card Payment Gateway for Prestashop
* Author  : Conekta.io
* URL     : https://www.conekta.io/es/docs/plugins/prestashop.
*
*  @author Conekta <support@conekta.io>
*  @copyright 2012-2017 Conekta
*  @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  @version v1.0.0
*/

/**
 * class Conekta_PrestashopValidationModuleFrontController
 */
class Conekta_PrestashopValidationModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        $cart = $this->context->cart;
        $authorized = false;
        $customer = new Customer($cart->id_customer);
        $conekta = new Conekta_Prestashop();

        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'conekta_prestashop') {
                $authorized = true;
                break;
            }
        }
        if (!$authorized) {
            die($this->module->getTranslator()
                ->trans('This payment method is not available.', array(), 'Modules.Conekta_Prestashop.Shop'));
        }

        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        $type = pSQL(Tools::getValue('type'));
        $msi = pSQL(Tools::getValue('monthly_installments'));
        $conektaToken = pSQL(Tools::getValue('conektaToken'));

        $conekta->processPayment($type, $conektaToken, $msi);

        $this->setTemplate('module:conekta_prestashop/views/templates/front/payment_return.tpl');
    }
}
