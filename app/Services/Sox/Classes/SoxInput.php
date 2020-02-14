<?php

namespace App\Services\Sox\Classes;

class SoxInput implements InputInterface
{

    /** @var string $sox Sox command */
    protected $sox;

    /** @var string $file */
    protected $file;

    /** @var int|float|null $volumeFactor */
    protected $volumeFactor = null;

    /** @var array  */
    protected $cutPoints = [];

    /** @var int $channels Number of channels */
    protected $channels;

    /** @var string $sampleRate */
    protected $sampleRate;

    /** @var bool Pipe mode */
    protected $isPipe = false;

    /**
     *  Input constructor.
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
        $this->sox = config('sox.sox');
    }

    /**
     *  Static constructor
     * @param string $file
     * @return \App\Services\Sox\Classes\InputInterface
     */
    public static function make(string $file)
    {
        return new static($file);
    }

    /**
     *  Input string
     *
     * @return string
     */
    public function toString()
    {
        /** @var string $pipe_cmd Pipe command format */
        $pipe_cmd = "\"|$this->sox %s$this->file -p %s%s%s\"";

        /** @var string $volume Volume settings */
        $volume = '';

        /** @var string $trim Cutting settings */
        $trim = '';

        if (!is_null($this->volumeFactor)) {
            $volume = "-v $this->volumeFactor ";
        }

        if (!empty($this->cutPoints)) {
            if (count($this->cutPoints) == 1) {
                $trim = ' trim ' . $this->cutPoints[0];
            } else {
                $trim = ' trim ' . $this->cutPoints[0] . ' ' . $this->cutPoints[1];
            }
        }

        $channels = '';
        if (!is_null($this->channels)) {
            $channels = " channels $this->channels";
        }

        $sampleRate = '';
        if (!is_null($this->sampleRate)) {
            $sampleRate = " rate $this->sampleRate";
        }

        if ($this->isPipe) {
            return sprintf($pipe_cmd, $volume, $channels, $trim, $sampleRate);
        }

        return " $volume $this->file $channels $trim $sampleRate";
    }

    /**
     *  Input string
     *
     * @return string
     */
    public function getInputString()
    {
        return $this->toString();
    }


    /*
     * =====================================================================================
     *                                  Edit methods
     * =====================================================================================
     */


    /**
     *  Set/Get volume factor
     * @param null|float $factor
     * @return \App\Services\Sox\Classes\InputInterface
     */
    public function volume($factor = null)
    {
        if (is_int($factor) || is_float($factor)) {
            $this->isPipe = true;
            $this->volumeFactor = $factor;
            return $this;
        }

        throw new \InvalidArgumentException('Volume factor is invalid');
    }

    /**
     *  Set/Get cut points
     *
     * @param float $start
     * @param float $duration
     * @return \App\Services\Sox\Classes\InputInterface
     * @throws \InvalidArgumentException
     */
    public function cut(float $start, float $duration = 0.0)
    {
        $this->isPipe = true;

        if (!is_null($start) && is_null($duration)) {
            $this->cutPoints = [$start];
            return $this;
        } else {
            $this->cutPoints = [$start, $duration];
            return $this;
        }
    }

    /**
     *  Set number of channels
     * @param int $value
     * @return InputInterface
     */
    public function channels(int $value) : InputInterface
    {
        $this->channels = $value;

        return $this;
    }

    public function rate(string $rate) : InputInterface
    {
        $this->sampleRate = $rate;

        return $this;
    }

    /**
     *  Pipe mode
     * @param bool $enabled
     * @return \App\Services\Sox\Classes\InputInterface
     */
    public function pipe($enabled = true)
    {
        $this->isPipe = $enabled;

        return $this;
    }
}
