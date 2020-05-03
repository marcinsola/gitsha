<?php
namespace App\Processors;

require('src/Processors/ProcessorInterface.php');

class GithubProcessor implements ProcessorInterface
{
    private array $arguments;
    private string $repo;
    private ?string $branch;

    public function __construct(string $repo, ?string $branch)
    {
        $this->arguments = [
            'baseEndpoint' => 'https://api.github.com/repos/%s/commits',
            'headers' => ['User-Agent: marcinsola'],
        ];
        $this->repo = $repo;
        $this->branch = $branch;
    }

    public function process(): string
    {
        $endpoint = $this->prepareEndpoint();

        return $this->parseResults(
            $this->getApiResponse($endpoint)
        );
    }

    private function prepareEndpoint(): string
    {
        $endpoint = sprintf($this->arguments['baseEndpoint'], $this->repo);
        if ($this->branch) {
            $endpoint .= sprintf('?sha=%s', $this->branch);
        }

        return $endpoint;
    }

    private function getApiResponse(string $endpoint): string
    {
        $ch = curl_init();
        curl_setopt_array(
            $ch,
            [
                CURLOPT_HTTPHEADER => $this->arguments['headers'] ?? [],
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
            ]
        );

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    private function parseResults(string $response): string
    {
        $result = json_decode($response);

        if (is_array($result)) {
            return $result[0]->sha;
        }

        return "Something went wrong. Please make sure that the repo and branch names provided are valid.";
    }
}
