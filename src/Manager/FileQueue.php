<?php

namespace Ark4ne\LightQueue;

class FileQueue
{
    /**
     * Path for queue file
     *
     * @var string
     */
    private $_file_path;

    /**
     * Use for check if queue file is always open when FileQueue is destruct
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
     */
    public function __construct($file_queue_name)
    {
        $this->_file_path = storage_path() . "/logs/$file_queue_name.queue";
    }

    /**
     * Open queue file and lock it.
     *
     * @return resource
     * @throws LightQueueException
     */
    private function _fHandle()
    {
        $wouldblock = true;
        $this->_handle = fopen($this->_file_path, "c+");
        if ($this->_handle) {
            while (!flock($this->_handle, LOCK_EX | LOCK_SH, $wouldblock)) {
                if (!$wouldblock)
                    throw new LightQueueException("FileQueue::_fHandle: Can't got lock for queue file !");
                usleep(10);
            }
            $this->_f_open = true;

            return $this->_handle;
        }

        throw new LightQueueException("FileQueue::_fHandle: Can't open queue file");
    }

    /**
     * UnLock queue file and close it.
     *
     * @throws LightQueueException
     */
    private function _fClose()
    {
        $wouldblock = true;
        if ($this->_f_open) {
            while (!flock($this->_handle, LOCK_UN, $wouldblock)) {
                if (!$wouldblock)
                    throw new LightQueueException("FileQueue::_fClose: Can't unlock queue file !");
                usleep(10);
            }
            if (!fclose($this->_handle)) {
                throw new LightQueueException("FileQueue::_fClose: Can't close queue file");
            }

            $this->_f_open = false;
        }
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
        return filesize($this->_file_path) > 1;
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