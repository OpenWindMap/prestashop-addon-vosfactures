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

namespace PrestaShop\PsAccountsInstaller\Installer\Facade;

use PrestaShop\PsAccountsInstaller\Installer\Exception\ModuleNotInstalledException;
use PrestaShop\PsAccountsInstaller\Installer\Exception\ModuleVersionException;
use PrestaShop\PsAccountsInstaller\Installer\Installer;
use PrestaShop\PsAccountsInstaller\Installer\Presenter\InstallerPresenter;

class PsAccounts
{
    /**
     * Available services class names
     */
    const PS_ACCOUNTS_PRESENTER = 'PrestaShop\Module\PsAccounts\Presenter\PsAccountsPresenter';
    const PS_ACCOUNTS_SERVICE = 'PrestaShop\Module\PsAccounts\Service\PsAccountsService';
    const PS_BILLING_SERVICE = 'PrestaShop\Module\PsAccounts\Service\PsBillingService';

    /**
     * @var Installer
     */
    private $installer;

    /**
     * PsAccounts constructor.
     *
     * @param Installer $installer
     */
    public function __construct(Installer $installer)
    {
        $this->installer = $installer;
    }

    /**
     * @param string $serviceName
     *
     * @return mixed
     *
     * @throws ModuleNotInstalledException
     * @throws ModuleVersionException
     */
    public function getService($serviceName)
    {
        if ($this->installer->isModuleInstalled()) {
            if ($this->installer->checkModuleVersion()) {
                return \Module::getInstanceByName($this->installer->getModuleName())
                    ->getService($serviceName);
            }
            throw new ModuleVersionException('Module version expected : ' . $this->installer->getModuleVersion());
        }
        throw new ModuleNotInstalledException('Module not installed : ' . $this->installer->getModuleName());
    }

    /**
     * @return mixed
     *
     * @throws ModuleNotInstalledException
     * @throws ModuleVersionException
     */
    public function getPsAccountsService()
    {
        return $this->getService(self::PS_ACCOUNTS_SERVICE);
    }

    /**
     * @return mixed
     *
     * @throws ModuleNotInstalledException
     * @throws ModuleVersionException
     */
    public function getPsBillingService()
    {
        return $this->getService(self::PS_BILLING_SERVICE);
    }

    /**
     * @return mixed
     *
     * @throws ModuleNotInstalledException
     * @throws ModuleVersionException
     */
    public function getPsAccountsPresenter()
    {
        if ($this->installer->isModuleInstalled() &&
            $this->installer->checkModuleVersion() &&
            $this->installer->isModuleEnabled()
        ) {
            return $this->getService(self::PS_ACCOUNTS_PRESENTER);
        } else {
            return new InstallerPresenter($this->installer);
        }
    }
}
