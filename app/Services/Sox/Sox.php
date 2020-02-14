<?php

namespace App\Services\Sox;

use App\Services\Sox\Classes\SoxInput;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use App\Services\Sox\Classes\InputInterface;

class Sox implements SoxInterface
{

    /** @var string $sox Sox command */
    protected $sox;

    /** @var  string $mode Input File Combining  */
    protected $mode = '';

    /** @var \Illuminate\Support\Collection $inputs Inputs */
    protected $inputs;

    /**  @var array $output Output settings */
    protected $output = [
        'path' => '',
        'options' => []
    ];

    /**
     * Sox constructor.
     * @param string $mode Available `mix`, `concat`
     * @throws \InvalidArgumentException
     */
    public function __construct(string $mode = null)
    {
        if (is_string($mode)) {
            $this->setMode($mode);
        }

        $this->sox = config('sox.sox');
        $this->inputs = collect();
    }

    /**
     *  Set edit mode `concat`
     *
     * @return \App\Services\Sox\SoxInterface
     */
    public static function concat()
    {
        return new static('concatenate');
    }

    /**
    /**
     *  Set edit mode `concat`
     *
     * @return \App\Services\Sox\SoxInterface
     */
    public static function mix()
    {
        return new static('mix');
    }

    /**
     *  Command string
     *
     * @return string
     * @throws SoxException
     */
    public function __toString()
    {
        return $this->getCommand();
    }

    /**
     *  Create SoX input instance
     * @param string $file
     * @return \App\Services\Sox\Classes\InputInterface
     */
    public static function input(string $file)
    {
        return SoxInput::make($file);
    }

    /**
     *  Add input file
     *
     * @param \App\Services\Sox\Classes\InputInterface $input File path
     * @return \App\Services\Sox\SoxInterface
     */
    public function addInput(InputInterface $input)
    {

        $this->inputs->push($input);

        return $this;
    }

    /**
     *  Output settings
     *
     * @param string $new_file
     * @param string $options
     * @return $this
     */
    public function saveAs(string $new_file, string $options = '')
    {
        $this->output['path'] = $new_file;
        $this->output['options'] = $options;

        return $this;
    }

    /**
     * @return string
     * @throws SoxException
     */
    public function getCommand()
    {
        $mode = ($this->inputs->count() > 1) ? $this->mode : '';

        /** @var  $command_format */

        $command_format = "$this->sox --clobber $mode inputs... output";

        /** @var string $inputs_string */
        $inputs_string = $this->convertInputs();

        /** @var string $output_string */
        $output_string = $this->convertOutput();

        return str_replace(['inputs...', 'output'], [$inputs_string, $output_string], $command_format);
    }

    /**
     * @throws SoxException
     */
    public function process()
    {
        /** @var \Symfony\Component\Process\Process $process */
        $process = new Process($this->getCommand());

        $process->setTimeout(0);
        $process->run();

        while ($process->isRunning()) {
            echo "SoX status => {$process->getStatus()} \r\n";
        }

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    /**
     *  Get option as string
     *
     * @return string
     * @throws SoxException
     */
    private function convertInputs()
    {
        if ($this->inputs->isEmpty()) {
            throw new SoxException('Inputs are not specified');
        }

        return collect($this->inputs)
            ->reduce(function ($string, SoxInput $input) {
                return is_null($string) ? $input->toString() : $string . ' ' . $input->toString();
            });
    }

    /**
     * @return string
     */
    private function convertOutput()
    {
        return $this->output['options'] . ' ' . $this->output['path'];
    }

    /**
     *  Set mode
     *  @url http://sox.sourceforge.net/sox.html Input File Combining
     *
     * @param string $mode
     * @throws \InvalidArgumentException
     * @return \App\Services\Sox\SoxInterface
     */
    private function setMode(string $mode)
    {
        if (in_array($mode, ['sequence', 'concatenate', 'mix', 'mix-power', 'merge', 'multiply'])) {
            $this->mode = " --combine $mode";
            return $this;
        }
        if ($mode == '−m' || $mode == '−M' || $mode == '-T') {
            $this->mode = " $mode";
            return $this;
        }

        throw new \InvalidArgumentException("Mode '$mode' is not supported");
    }
}
