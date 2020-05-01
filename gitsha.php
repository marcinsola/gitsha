<?php

class Gitsha {
    private array $arguments;
    private array $baseUrls;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
        $this->services = [
            'github' => [
                'baseEndpoint' => 'https://api.github.com/repos/%s/commits',
                'headers' => ['User-Agent: marcinsola'],
            ],
        ]
    }

    public function run(): string
    {
        if (size($this->arguments) <= 1) {
            return 'Please provide repository name that you want to scan as an argument.';
        }

        $service = isset($this->arguments[3] ? $this->arguments[3] : 'github';
        
        if (!in_array($service, array_keys($this->services))) {
            return sprintf('Unknown service: %s', $service);
        }

        $endpoint = call_user_func(sprintf('prepare%sEndpoint', ucfirst($service));
        //@TODO: Zaimplementować parseApiResponse
        $result = $this->parseApiReponse(
            $this->getApiResponse($endpoint, $service),
            $service
        );
    }
    
    //Tutaj przy bardziej rozbudowanej liczbie serwisów można by było zastosować fabrykę.
    //Na potrzeby bierzącego zadania rozwiązałem to jednak w nieco prostszy sposób.
    private function prepareGithubEndpoint(): string
    {
        $endpoint = sprintf($this->services['github']['baseEndpoint'], $this->arguments[1]);
        if (isset($this->arguments[2])) {
            $endpoint .= sprintf('?sha=%s', $this->arguments[2]);
        }

       return $endpoint;
    }

    private function getApiResult(string $endpoint, string $service): string
    {
        curl_setopt_array(
            $ch,
            [
                CURLOPT_HTTPHEADER => [
                    $this->services[$service]['headers'],
                ],
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
            ]
        );

        return curl_exec($ch);
    }
}

if ($argc <= 1) {
    echo 'Please provide repository name that you want to scan as an argument.';
    die;
}
$ch = curl_init();
$endpoint = sprintf('https://api.github.com/repos/%s/commits', $argv[1]);

if (isset($argv[2])) {
    $endpoint .= sprintf('?sha=%s', $argv[2]);
}

curl_setopt_array(
    $ch,
    [
        CURLOPT_HTTPHEADER => [
            'User-Agent: marcinsola',
        ],
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
    ]
);

$result = json_decode(curl_exec($ch));
curl_close($ch);

if (is_array($result)) {
    echo $result[0]->sha;
    die;
}

echo sprintf("Couldn't find repo %s. Please make sure that the repo and branch names provided are valid.", $argv[1]);
