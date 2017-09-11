<?php


namespace Async\Demo\Controllers\BlockingCode\Modeling;


class ExtractionLoader
{
    private $nextEntityLoader;
    private $factory;
    private $parser;

    public function __construct(
        ParserInterface $parser,
        FactoryInterface $factory,
        ExtractionLoader $entityLoader = null
    ) {
        $this->nextEntityLoader = $entityLoader;
        $this->factory = $factory;
        $this->parser = $parser;
    }

    /**
     * Meant for collection extraction
     *
     * @param $rawData
     *
     * @return array
     */
    public function extract($rawData): array
    {
        return $this->parser->parse($rawData);
    }

    /**
     * @param array $rawData
     * @param array $handlers
     *
     * @return AccessInterface
     */
    public function loadData($rawData = [], $handlers = []): AccessInterface
    {
        $handlers = $this->dealWithHandlers($handlers);

        return new Data($rawData, $this->nextEntityLoader, $handlers);
    }

    /**
     * @param array $rawData
     * @param array $handlers
     *
     * @return mixed
     */
    public function loadModel($rawData = [], $handlers = [])
    {
        return $this->factory->createModel($this->loadData($rawData, $handlers));
    }

    private function dealWithHandlers($handlers)
    {
        if (is_array($handlers)) {
            return $handlers;
        }

        return [$handlers];
    }
}
