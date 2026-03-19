<?php

namespace Valet\PackageManagers;

use ConsoleComponents\Writer;
use DomainException;
use Valet\CommandLine;
use Valet\Contracts\PackageManager;
use Valet\Contracts\ServiceManager;

class AltLinux implements PackageManager
{
    /**
     * @var CommandLine
     */
    public $cli;
    /**
     * @var ServiceManager
     */
    public $serviceManager;

    private const PACKAGES = [
        'redis' => 'redis-server',
        'mysql' => 'mariadb-server', // ALTLinux uses MariaDB by default
        'mariadb' => 'mariadb-server',
    ];

    /**
     * @var array
     */
    public const PHP_FPM_PATTERN_BY_VERSION = [
        '8.1' => 'php8.1-fpm-fcgi',
        '8.2' => 'php8.2-fpm-fcgi',
        '8.3' => 'php8.3-fpm-fcgi',
        '8.4' => 'php8.4-fpm-fcgi',
    ];

    /**
     * Create a new AltLinux instance.
     */
    public function __construct(CommandLine $cli, ServiceManager $serviceManager)
    {
        $this->cli = $cli;
        $this->serviceManager = $serviceManager;
    }

    /**
     * Get array of installed packages.
     */
    public function packages(string $package): array
    {
        $query = "rpm -qa {$package} | sort";

        return explode(PHP_EOL, $this->cli->run($query));
    }

    /**
     * Determine if the given package is installed.
     */
    public function installed(string $package): bool
    {
        $packages = $this->packages($package);

        foreach ($packages as $installedPackage) {
            if (strpos($installedPackage, $package) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ensure that the given package is installed.
     */
    public function ensureInstalled(string $package): void
    {
        if (!$this->installed($package)) {
            $this->installOrFail($package);
        }
    }

    /**
     * Install the given package and throw an exception on failure.
     */
    public function installOrFail(string $package): void
    {
        Writer::twoColumnDetail($package, 'Installing');

        $this->cli->run(trim('apt-get install -y ' . $package), function ($exitCode, $errorOutput) use ($package) {
            Writer::error(\sprintf('%s: %s', $exitCode, $errorOutput));

            throw new DomainException('Apt was unable to install [' . $package . '].');
        });
    }

    /**
     * Configure package manager on valet install.
     */
    public function setup(): void
    {
        // Nothing to do
    }

    /**
     * Determine if package manager is available on the system.
     */
    public function isAvailable(): bool
    {
        try {
            $output = $this->cli->run('which apt-get', function () {
                throw new DomainException('Apt not available');
            });

            // Check if this is ALTLinux by checking /etc/os-release
            $osRelease = $this->cli->run('cat /etc/os-release 2>/dev/null', function () {
                return '';
            });

            if ($output != '' && strpos($osRelease, 'ID=altlinux') !== false) {
                return true;
            }

            return false;
        } catch (DomainException $e) {
            return false;
        }
    }

    /**
     * Determine php fpm package name.
     */
    public function getPhpFpmName(string $version): string
    {
        $pattern = !empty(self::PHP_FPM_PATTERN_BY_VERSION[$version])
            ? self::PHP_FPM_PATTERN_BY_VERSION[$version] : 'php{VERSION}-fpm-fcgi';

        return str_replace('{VERSION}', $version, $pattern);
    }

    /**
     * Get PHP FPM service name for systemd.
     */
    public function getPhpFpmServiceName(string $version): string
    {
        return 'php' . $version . '-fpm';
    }

    /**
     * Get the `ca-certificates` directory
     */
    public function getCaCertificatesPath(): string
    {
        return '/etc/pki/ca-trust/extracted/pem';
    }

    /**
     * Determine php extension pattern.
     */
    public function getPhpExtensionPrefix(string $version): string
    {
        $pattern = 'php{VERSION}';
        return str_replace('{VERSION}', $version, $pattern);
    }

    /**
     * Get PHP extension name for a given extension.
     */
    public function getPhpExtensionName(string $version, string $extension): string
    {
        // Special case for mysql in ALTLinux
        if ($extension === 'mysql') {
            return 'php' . $version . '-mysqlnd';
        }
        return $this->getPhpExtensionPrefix($version) . '-' . $extension;
    }

    /**
     * Restart network manager in ALTLinux.
     */
    public function restartNetworkManager(): void
    {
        $this->serviceManager->restart(['NetworkManager']);
    }

    /**
     * Get package name by service.
     */
    public function packageName(string $name): string
    {
        if (isset(self::PACKAGES[$name])) {
            return self::PACKAGES[$name];
        }
        throw new \InvalidArgumentException(\sprintf('Package not found by %s', $name));
    }
}
