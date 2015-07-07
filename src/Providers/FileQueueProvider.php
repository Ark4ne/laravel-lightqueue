<?php

namespace Ark4ne\LightQueue\Provider;

use Ark4ne\LightQueue\Exception\LightQueueException;
use Illuminate\Support\Facades\Config;

class FileLockable
{
    /**
     * @var resource
     */
    private $handle;

    /**
     * @var bool
     */
    private $open;

    /**
     * @var bool
     */
    private $lock;

    /**
     * @var string
     */
    public $path;

    /**
     * @param $path string
     * @throws LightQueueException
     */
    public function __construct($path)
    {
        $this->open = false;
        $this->lock = false;
        $this->path = $path;

        if (!file_exists($this->path)) {
            $handle = @fopen($this->path, 'w');
            if (!$handle)
                throw new LightQueueException("FileLockable::__construct: Can't create queue file");

            fclose($handle);
        }
    }

    /**
     * Call flock for handle
     *
     * @param $option
     * @throws LightQueueException
     */
    private function _flock($option)
    {
        $try = 0;
        $maxTry = 8;

        $wouldblock = true;
        while (!flock($this->handle, $option | LOCK_NB, $wouldblock)) {
            if (($try > $maxTry) || !$wouldblock)
                throw new LightQueueException("FileLockable::_flock: Can't got lock for queue file !");
            usleep(10);
            $try++;
        }
    }

    public function handle()
    {
        if (!$this->isOpen()) {
            $this->handle = @fopen($this->path, "c+");
            if ($this->handle) {
                $this->open = true;
                return $this->handle;
            }
        } else {
            return $this->handle;
        }

        throw new LightQueueException("FileLockable::_fHandle: Can't open queue file");

    }

    /**
     * @return boolean
     */
    public function isLock()
    {
        return $this->lock;
    }

    /**
     * @return boolean
     */
    public function isOpen()
    {
        return $this->open;
    }

    /**
     * Lock handle
     *
     * @throws LightQueueException
     */
    public function lock()
    {
        if ($this->isOpen() && !$this->isLock()) {
            $this->_flock(LOCK_EX);

            $this->lock = true;

            return true;
        }
        return false;
    }

    /**
     * UnLock handle
     *
     * @throws LightQueueException
     */
    public function unlock()
    {
        if ($this->isOpen() && $this->isLock()) {
            $this->_flock(LOCK_UN);

            $this->lock = false;
            return true;
        }
        return false;
    }

    /**
     * UnLock queue file and close it.
     *
     * @throws LightQueueException
     */
    public function close()
    {
        if ($this->isOpen()) {
            $this->unlock();

            if (!fclose($this->handle))
                throw new LightQueueException("FileLockable::_fClose: Can't close queue file");

            $this->open = false;
        }
    }

    /**
     *  Close handle if is already open
     */
    public function __destruct()
    {
        $this->close();
    }
}

class FileQueueProvider implements ProviderInterface
{
    /**
     * @var FileLockable
     */
    private $file;

    /**
     * @param string $file_queue_name Name of queue
     * @throws LightQueueException
     */
    public function __construct($file_queue_name)
    {
        $this->file = new FileLockable(Config::get('queue.lightqueue.queue_directory') . md5($file_queue_name) . ".queue");
    }

    /**
     * Get size of queue
     *
     * @return int
     */
    public function queueSize()
    {
        return count(file($this->file->path));
    }

    /**
     *  Close handle if is already open
     */
    public function __destruct()
    {
        $this->file->close();
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
            $handle = $this->file->handle();

            fseek($handle, 0, SEEK_END);

            fwrite($handle, $cmd . PHP_EOL, strlen($cmd . PHP_EOL));
            fflush($handle);

            $this->file->close();

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
        $line = null;
        try {
            $lines = file($this->file->path, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
            if (count($lines) > 0) {
                if ($this->file->handle()) {
                    rewind($this->file->handle());
                    ftruncate($this->file->handle(), 0);
                    $line = $lines[0];
                    for ($i = 1, $lenght = count($lines); $i < $lenght; $i++) {
                        $_line = $lines[$i] . PHP_EOL;
                        $_l_line = strlen($_line);
                        if ($_l_line)
                            fwrite($this->file->handle(), $_line, $_l_line);
                    }

                    fflush($this->file->handle());
                    $this->file->close();
                }
            }
        } catch (\Exception $lqe) {
            $line = null;
        }

        return $line;
    }
}