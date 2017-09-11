<?php


namespace Async\Demo\Controllers\BlockingCode\Modeling;


class Animal
{
    private $data;


    public function __construct(AccessInterface $data)
    {
        $this->data = $data;
        $this->data->withLoader(
            function (array $rawData): array {
                return $this->load($rawData);
            }
        );
    }

    public function getName()
    {
        return $this->data['name'];
    }

    public function toArray()
    {
        return $this->data->toArray();
    }

    public function getId()
    {
        return $this->data['id'];
    }

    public function getDelay()
    {
        return $this->data['delay'];
    }

    private function load(array $rawData)
    {
        $requiredKeys = ['name', 'id', 'delay'];
        foreach ($requiredKeys as $requiredKey) {
            if (!array_key_exists($requiredKey, $rawData)) {
                throw new \Exception('missing data :(');
            }
        }

        return $rawData;
    }
}
