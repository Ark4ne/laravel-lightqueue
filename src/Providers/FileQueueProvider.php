<?php

namespace Ark4ne\LightQueue\Provider;

use Ark4ne\LightQueue\Exception\LightQueueException;
use Illuminate\Support\Facades\Config;

class FileQueueProvider implements ProviderInterface
{
    /**
     * Path for queue file
     *
     * @var string
     */
    private $_file_path;

    /**
     * Use for check if queue file is always open when FileQueueProvider is destruct
     *
     * @var bool
     */
    private $_f_open = false;

    /**
     * @var resource
     */
    private $_handle;

    /**
     * @param string $file_queue_name Name of queue
     * @throws LightQueueException
     */
    public function __construct($file_queue_name)
    {
        $this->_file_path = Config::get('queue.lightqueue.queue_directory') . "$file_queue_name.queue";

        if (!file_exists($this->_file_path)) {
            $handle = fopen($this->_file_path, 'w');
            if (!$handle)
                throw new LightQueueException("FileQueueProvider::__construct: Can't create queue file");

            fclose($handle);
        }
    }

    /**
     * Get size of queue
     *
     * @return int
     */
    public function queueSize()
    {
        return count(file($this->_file_path));
    }

    /**
     * Got Lock for handle
     *
     * @param int $option
     * @throws LightQueueException
     */
    private function _fLock($option = LOCK_UN)
    {
        $wouldblock = true;
        while (!flock($this->_handle, $option, $wouldblock)) {
            if (!$wouldblock)
                throw new LightQueueException("FileQueueProvider::_fHandle: Can't got lock for queue file !");
            usleep(10);
        }
    }

    /**
     * Open queue file and lock it.
     *
     * @return resource
     * @throws LightQueueException
     */
    private function _fHandle()
    {
        $this->_handle = fopen($this->_file_path, "c+");
        if ($this->_handle) {
            $this->_fLock(LOCK_EX | LOCK_SH);

            $this->_f_open = true;
            return $this->_handle;
        }

        throw new LightQueueException("FileQueueProvider::_fHandle: Can't open queue file");
    }

    /**
     * UnLock queue file and close it.
     *
     * @throws LightQueueException
     */
    private function _fClose()
    {
        if ($this->_f_open) {
            $this->_fLock(LOCK_UN);
            if (!fclose($this->_handle))
                throw new LightQueueException("FileQueueProvider::_fClose: Can't close queue file");

            $this->_f_open = false;
        }
    }

    /**
     * Check if queue has command
     *
     * @return bool
     */
    public function fileSize()
    {
        /*
         * Clear filesize() cache
         */
        clearstatcache();
        return filesize($this->_file_path);
    }

    /**
     *  Close handle if is already open
     */
    public function __destruct()
    {
        if ($this->_f_open)
            @$this->_fClose();
    }

    /**
     * Push Command to queue
     *
     * @param $cmd
     * @return bool
     */
    public function push($cmd)
    {
        try {
            $this->_fHandle();

            fseek($this->_handle, 0, SEEK_END);

            fwrite($this->_handle, $cmd . PHP_EOL, strlen($cmd . PHP_EOL));
            fflush($this->_handle);

            $this->_fClose();

            return true;
        } catch (LightQueueException $lqe) {
            return false;
        }
    }

    /**
     * Check if queue has command
     *
     * @return bool
     */
    public function hasNext()
    {
        return $this->queueSize() > 0;
    }


    /**
     * Get the next command in queue
     *
     * @return string
     */
    public function next()
    {
        $line = '';
        try {
            $lines = file($this->_file_path, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
            if (count($lines) > 0) {
                if ($this->_fHandle()) {
                    rewind($this->_handle);
                    ftruncate($this->_handle, 0);
                    $line = $lines[0];
                    for ($i = 1, $lenght = count($lines); $i < $lenght; $i++) {
                        $_line = $lines[$i] . PHP_EOL;
                        $_l_line = strlen($_line);
                        if ($_l_line)
                            fwrite($this->_handle, $_line, $_l_line);
                    }

                    fflush($this->_handle);
                    $this->_fClose();
                }
            }
        } catch (\Exception $lqe) {
            $line = '';
        }

        return $line;
    }
}