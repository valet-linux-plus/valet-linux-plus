<?php

namespace Valet;

use ConsoleComponents\Writer;
use DomainException;
use Exception;
use Valet\Facades\Request as RequestFacade;

class Ngrok
{
    /**
     * @var string
     */
    private const TUNNEL_ENDPOINT = 'http://127.0.0.1:4040/api/tunnels';
    private const BINARY_DOWNLOAD_LINK = 'https://bin.equinox.io/c/bNyj1mQVY4c/ngrok-v3-stable-linux-amd64.tgz';

    public CommandLine $cli;

    public Filesystem $files;

    /**
     * Create a new Ngrok instance.
     */
    public function __construct(CommandLine $cli, Filesystem $filesystem)
    {
        $this->cli = $cli;
        $this->files = $filesystem;
    }

    /**
     * Install Ngrok binary
     * @throws Exception
     */
    public function install(): void
    {
        if ($this->files->exists(\sprintf('%s/bin/ngrok', VALET_ROOT_PATH))) {
            return;
        }

        Writer::twoColumnDetail('Ngrok', 'Installing');
        $this->files->ensureDirExists(\sprintf('%s/bin', VALET_ROOT_PATH), user());

        $response = RequestFacade::get(self::BINARY_DOWNLOAD_LINK)->send();
        if ($response->hasErrors()) {
            Writer::twoColumnDetail('Ngrok', 'Failed');
            return;
        }
        $zipFile = \sprintf('%s/bin/%s', VALET_ROOT_PATH, basename(self::BINARY_DOWNLOAD_LINK));

        $this->files->putAsUser($zipFile, $response->raw_body);

        $phar = new \PharData($zipFile);
        $phar->extractTo(VALET_ROOT_PATH.'/bin/');

        $this->files->remove($zipFile);
    }

    /**
     * Get the current tunnel URL from the Ngrok API.
     *
     * @throws Exception
     */
    public function currentTunnelUrl(): ?string
    {
        try {
            return retry(20, function (): ?string {
                $body = RequestFacade::get(self::TUNNEL_ENDPOINT)->send()->body;

                // If there are active tunnels on the Ngrok instance we will spin through them and
                // find the one responding on HTTP. Each tunnel has an HTTP and a HTTPS address
                // but for local testing purposes we just desire the plain HTTP URL endpoint.
                if (isset($body->tunnels) && count($body->tunnels) > 0) {
                    return $this->findHttpTunnelUrl($body->tunnels);
                }
                throw new DomainException('Tunnel not established.');
            }, 250);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Find the HTTP tunnel URL from the list of tunnels.
     */
    public function setAuthToken(string $authToken): void
    {
        $this->cli->run(
            \sprintf('%s/bin/ngrok config add-authtoken %s', VALET_ROOT_PATH, $authToken)
        );
    }

    /**
     * Find the HTTP tunnel URL from the list of tunnels.
     */
    private function findHttpTunnelUrl(array $tunnels): ?string
    {
        foreach ($tunnels as $tunnel) {
            if ($tunnel->proto === 'http') {
                return $tunnel->public_url;
            }
        }

        return null;
    }
}
