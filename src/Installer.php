<?php namespace Melting\MODX\Installer;

/**
 * A helper to build & install/upgrade MODX Revolution
 */
class Installer
{
    /**
     * Absolute path to folder where Revo files belong
     *
     * @var string
     */
    protected $source = '';
    /**
     * An optional array of folders to move to custom locations
     *
     * @var array
     */
    protected $destinations = [];
    /**
     * @var null|\modX
     */
    protected $modx;

    /**
     * @param string $source - The absolute path where MODX Revolution source files are located
     * @param array $destinations - An optional array of folders to move (array key being the folder name inside Revo source, array value being the absolute path where to move that folder)
     */
    public function __construct($source, array $destinations = [])
    {
        $this->source = realpath($source);
        $this->destinations = $destinations;
    }

    /**
     * Perform an installation using the given configuration
     *
     * @param array $config
     * @param string $configKey - An optional configuration key
     *
     * @return \modX|null
     */
    public function install(array $config, $configKey = 'config')
    {
        // Validate source

        // Do whatever it takes with the source
        $this->handleSource();

        // Move folders to their destinations, if any
        $this->handleCustomFolders();

        $installed = $this->isAlreadyInstalled($configKey);
        if ($installed) {
            $core = $this->getCorePath();
            $args = "upgrade --core_path={$core}";
        } else {
            // Build configFile (XML)
            $configFile = $this->buildConfigFile($config);
            $args = "new --config={$configFile}";
        }

        // Run install script
        passthru("php {$this->source}/setup/index.php --installmode={$args}");

        if (!$installed) {
            // Remove config file
            unlink($configFile);
        }

        // Return a modX instance
        return $this->getInstance();
    }

    /**
     * Check whether or not a Revo installation is available
     *
     * @param string $key - Optional configuration key
     *
     * @return bool
     */
    protected function isAlreadyInstalled($key = 'config')
    {
        $path = $this->getCorePath();

        return file_exists(realpath("{$path}/config/{$key}.inc.php"));
    }

    /**
     * Try to find the core_path
     *
     * @return string
     */
    protected function getCorePath()
    {
        $path = $this->source . '/core/';
        if (array_key_exists('core', $this->destinations)) {
            // Custom core destination
            $path = $this->destinations['core'];
        }

        return realpath($path);
    }

    /**
     * Handle the source. Either
     *
     * Build the core.transport.zip package (git source)
     * Extract the zip archive
     *
     * @return void
     */
    protected function handleSource()
    {
        if (file_exists("{$this->source}/_build/")) {
            copy("{$this->source}/_build/build.config.sample.php", "{$this->source}/_build/build.config.php");
            copy("{$this->source}/_build/build.properties.sample.php", "{$this->source}/_build/build.properties.php");
            passthru("php {$this->source}/_build/transport.core.php");
        }
        $isZip = false;
        if ($isZip) {
            // Extract
        }
    }

    /**
     * Move some Revo folders to custom locations, if needed
     *
     * @return void
     */
    protected function handleCustomFolders()
    {
        if (!empty($this->destinations)) {
            foreach ($this->destinations as $folder => $target) {
                if (!file_exists($target)) {
                    mkdir($target);
                }
                $folder = $this->source .'/'. $folder;
                passthru("cp -rf {$folder}/* {$target}");
            }
        }
    }

    /**
     * Build an XML configuration file install Revo
     *
     * @param array $config
     *
     * @return string - The absolute path to the configuration file
     */
    protected function buildConfigFile(array $config)
    {
        $dom = new \DOMDocument();
        $xml = $dom->appendChild($dom->createElement('modx'));

        foreach ($config as $k => $v) {
            $xml->appendChild($dom->createElement($k, $v));
        }

        $dom->formatOutput = true;
        $output = $dom->saveXML();

        $file = getcwd() . '/config.xml';
        file_put_contents($file, $output);

        return realpath($file);
    }

    /**
     * Try to instantiate a modX instance
     *
     * @return \modX|null
     */
    protected function getInstance()
    {
        if ($this->modx) {
            return $this->modx;
        }

        $path = $this->getCorePath();
        require_once "{$path}/model/modx/modx.class.php";

        $this->modx = \modX::getInstance('setup');
        $this->modx->initialize('mgr');
        $this->modx->getService('error', 'error.modError');

        return $this->modx;
    }
}
