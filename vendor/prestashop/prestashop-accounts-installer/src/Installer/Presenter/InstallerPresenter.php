<?php
/**
 *  Copyright since 2007 PrestaShop SA and Contributors
 *  PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *  *
 *  NOTICE OF LICENSE
 *  *
 *  This source file is subject to the Academic Free License version 3.0
 *  that is bundled with this package in the file LICENSE.md.
 *  It is also available through the world-wide-web at this URL:
 *  https://opensource.org/licenses/AFL-3.0
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *  *
 *  @author    PrestaShop SA and Contributors <contact@prestashop.com>
 *  @copyright Since 2007 PrestaShop SA and Contributors
 *  @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\PsAccountsInstaller\Installer\Presenter;

use PrestaShop\PsAccountsInstaller\Installer\Installer;

class InstallerPresenter
{
    /**
     * @var Installer
     */
    private $installer;

    /**
     * @var \Context
     */
    private $context;

    /**
     * InstallerPresenter constructor.
     *
     * @param Installer $installer
     * @param \Context|null $context
     */
    public function __construct(Installer $installer, \Context $context = null)
    {
        $this->installer = $installer;

        if (null === $context) {
            $context = \Context::getContext();
        }
        $this->context = $context;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function present()
    {
        // Fallback minimal Presenter
        return [
            'psIs17' => $this->installer->isShopVersion17(),

            'psAccountsInstallLink' => $this->installer->getInstallLink(),
            'psAccountsEnableLink' => $this->installer->getEnableLink(),
            'psAccountsUpdateLink' => $this->installer->getUpgradeLink(),

            'psAccountsIsInstalled' => $this->installer->isModuleInstalled(),
            'psAccountsIsEnabled' => $this->installer->isModuleEnabled(),
            'psAccountsIsUptodate' => $this->installer->checkModuleVersion(),

            'onboardingLink' => null,
            'user' => [
                'email' => null,
                'emailIsValidated' => false,
                'isSuperAdmin' => $this->isEmployeeSuperAdmin(),
            ],
            'currentShop' => null,
            'shops' => [],
            'superAdminEmail' => null,
            'ssoResendVerificationEmail' => null,
            'manageAccountLink' => null,
        ];
    }

    /**
     * @return bool
     */
    public function isEmployeeSuperAdmin()
    {
        return $this->context->employee->isSuperAdmin();
    }
}
