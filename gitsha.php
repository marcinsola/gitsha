<?php

class Gitsha {
    private array $arguments;
    private array $services;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
        $this->services = [
            'github' => [
                'baseEndpoint' => 'https://api.github.com/repos/%s/commits',
                'headers' => ['User-Agent: marcinsola'],
            ],
        ];
    }

    public function run(): string
    {
        if (sizeof($this->arguments) <= 1) {
            return 'Please provide repository name that you want to scan as an argument.';
        }

        $service = isset($this->arguments[3]) ? $this->arguments[3] : 'github';
        
        if (!in_array($service, array_keys($this->services))) {
            return sprintf('Unknown service: %s', $service);
        }

        //@TODO: zaimplementować ogólną metodę prepareEndpoint
        $endpoint = $this->prepareGithubEndpoint();

        return $this->parseApiResponse(
            $this->getApiResponse($endpoint, $service),
            $service
        );
    }

    //Tutaj przy bardziej rozbudowanej liczbie serwisów można by było zastosować fabrykę
    //i dla każdego z serwisów tworzyć osobne klasy (np. GithubProcessor, BitbucketProcessor, itp.).
    //Na potrzeby bierzącego zadania rozwiązałem to jednak w nieco prostszy sposób.
    private function prepareGithubEndpoint(): string
    {
        $endpoint = sprintf($this->services['github']['baseEndpoint'], $this->arguments[1]);
        if (isset($this->arguments[2])) {
            $endpoint .= sprintf('?sha=%s', $this->arguments[2]);
        }

       return $endpoint;
    }

    private function getApiResponse(string $endpoint, string $service): string
    {
        $ch = curl_init();
        curl_setopt_array(
            $ch,
            [
                CURLOPT_HTTPHEADER => $this->services[$service]['headers'] ?? [],
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
            ]
        );
        
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    private function parseApiResponse(string $response, string $service): string
    {
        $result = json_decode($response);

        if (is_array($result)) {
            return $result[0]->sha;
        }

        return  sprintf("Couldn't find repo %s. Please make sure that the repo and branch names provided are valid.", $this->arguments[1]);
    }
}

$command = new Gitsha($argv);
echo $command->run();

