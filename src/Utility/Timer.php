<?php


namespace Async\Demo\Utility;


class Timer
{
    private $start;
    private $mark;
    private $raceSaver;
    private $readings = [];

    public function __construct($raceSaver = false)
    {
        $this->start = microtime(true);
        $this->raceSaver = $raceSaver;
    }

    private function loadLast()
    {
        if (is_null($this->mark)) {
            return $this->start;
        }

        return $this->mark;
    }

    public function checkPoint($value, $readingName)
    {
        $this->check($readingName);

        return $value;
    }

    public function check($readingName)
    {
        $this->read($readingName, $this->loadLast());
    }

    private function read($readingName, $reference)
    {
        $current = microtime(true);
        $reading = $current - $reference;
        $this->mark = $current;
        $unit = ' s';
        $formattedReading = number_format($reading, 4, '.', '') . $unit;

        if ($this->raceSaver) {
            $running = $current - $this->start;
            $runningUnit = 's';
            $formattedRunning = number_format($running, 4, '.', '') . $runningUnit;

            $this->readings[] = [
                $readingName => sprintf(
                    '(current: %s) ~~ (running: %s)',
                    $formattedReading,
                    $formattedRunning
                ),
            ];
        } else {
            $this->readings[$readingName] = $formattedReading;
        }
    }

    public function toArray()
    {
        $this->read('totalTimeTaken', $this->start);

        return $this->readings;
    }
}
