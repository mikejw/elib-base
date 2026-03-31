<?php

declare(strict_types=1);

namespace Empathy\ELib;

use Composer\InstalledVersions;
use Empathy\ELib\Util\Libs;
use Empathy\MVC\Bootstrap;
use Empathy\MVC\Config as EmpConfig;
use Empathy\MVC\DI;
use Empathy\MVC\Session;

class AdminController extends EController
{
    public function __construct(Bootstrap $boot, bool $assertAdmin = true)
    {
        parent::__construct($boot);
        if ($assertAdmin) {
            DI::getContainer()->get('CurrentUser')->assertAdmin($this);
        }

        $this->detectHelp();

        $cache = null;
        $cacheEnabled = false;
        try {
            $cache = DI::getContainer()->get('Cache');
            $cacheEnabled = DI::getContainer()->get('cacheEnabled');
        } catch (\Exception $e) {
            //
        }

        if ($cache && $cacheEnabled) {
            $this->assign('installed', $cache->cachedCallback('installed_lib_info', [$this, 'getInstalledLibInfo']));
        } else {
            $this->assign('installed', $this->getInstalledLibInfo());
        }
    }

    /**
     * @return array<string, array{name: string, version: string}>
     */
    public function getInstalledLibInfo(): array
    {
        Libs::findAll();
        $libs = Libs::getInstalled();
        $installed = [];
        foreach ($libs as $lib) {
            if (InstalledVersions::isInstalled($lib)) {
                $pretty = InstalledVersions::getPrettyVersion($lib);
                $installed[$lib] = [
                    'version' => (string) ($pretty ?? ''),
                    'name' => 'No description',
                ];
                $path = InstalledVersions::getInstallPath($lib);
                $jsonRaw = @file_get_contents($path.'/composer.json');
                if ($jsonRaw === false) {
                    continue;
                }
                $composerJson = json_decode($jsonRaw, true);
                if (is_array($composerJson)) {
                    $installed[$lib]['name'] = (string) ($composerJson['description'] ?? 'No description');
                }
            }
        }
        return $installed;
    }

    private function tplInLib(string $help_file): bool
    {
        $dirs = $this->elib_tpl_dirs;
        if ($dirs === null) {
            return false;
        }
        foreach ($dirs as $dir) {
            $file = $dir.'/'.$help_file;
            if (file_exists($file)) {
                return true;
            }
        }
        return false;
    }


    /**
     * @return string|false
     */
    protected function findHelp(): string|false
    {
        $help_file = 'admin_help/'.$this->class.'_'.$this->event.'.tpl';
        if (
            file_exists(
                EmpConfig::get('DOC_ROOT').'/presentation/'.$help_file
            )
            || $this->tplInLib($help_file)
        ) {
            return $help_file;
        }
        $help_file = 'admin_help/'.$this->class.'.tpl';
        if (
            file_exists(
                EmpConfig::get('DOC_ROOT').'/presentation/'.$help_file
            )
            || $this->tplInLib($help_file)
        ) {
            return $help_file;
        }
        return false;
    }


    protected function detectHelp(): void
    {
        $help_file = $this->findHelp();
        if ($help_file) {
            $this->assign('help_file', 'elib:/'.$help_file);
        }
    }

    public function default_event(): void
    {
        $this->setTemplate('elib:/admin/admin.tpl');
    }

    public function store(): void
    {
        $this->setTemplate('elib:/admin/store.tpl');
    }

    public function password(): void
    {
        $currentUser = DI::getContainer()->get('CurrentUser');
        $this->setTemplate('elib:/admin/password.tpl');
        if (isset($_POST['submit'])) {
            $errors = $currentUser->doChangePassword(
                $_POST['old_password'],
                $_POST['password1'],
                $_POST['password2']
            );

            if (sizeof($errors) < 1) {
                $this->redirect('admin');
            } else {
                $this->assign('errors', $errors);
            }
        } elseif (isset($_POST['cancel'])) {
            $this->redirect('admin');
        }
    }

    public function toggle_help(): void
    {
        if ($this->isXMLHttpRequest()) {
            $help_shown = Session::get('help_shown');
            if ($help_shown) {
                Session::set('help_shown', false);
            } else {
                Session::set('help_shown', true);
            }
            header('Content-type: application/json');
            echo json_encode(1);
            exit();
        }
    }

}
