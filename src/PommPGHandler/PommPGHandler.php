<?php

namespace PommPGHandler;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

/**
 * This class is a handler for Monolog, which is used
 * to log records to a PostgreSQL database via Pomm
 */
class PommPGHandler extends AbstractProcessingHandler {

    /**
     * @var bool defines whether the Postgres connection is been initialized
     */
    private $initialized = false;

    /**
     * @var resource
     */
    protected $pomm;

    /**
     * @var string pg statement name
     */
    private $statement;

    /**
     * @var string The default log storage table
     */
    private $table = 'logs';

    /**
     * @param resource $connection
     * @param string $table
     * @param integer $level
     * @param bool $bubble
     */
    public function __construct($pomm, $table, $level = Logger::DEBUG, $bubble = true)
    {
        if (get_class($pomm) != 'PommProject\ModelManager\Session') {
            throw new \InvalidArgumentException('A connection must be a POMM Session.');
        }
        $this->pomm = $pomm;
        $this->table = $table;
        parent::__construct($level, $bubble);
    }

    /**
     * Initializes this handler by creating the table if it not exists
     */
    private function initialize() {
        $this->pomm->getQueryManager()->query(
            'CREATE TABLE IF NOT EXISTS '.$this->table.' ('
            . 'channel varchar(255),'
            . 'level_name varchar(10),'
            . 'message text,'
            . 'context json,'
            . 'extra json,'
            . 'datetime timestamp'
            . ')'
        );

        $this->statement = $this->pomm->getPreparedQuery('INSERT INTO '.$this->table.' (channel, level_name, message, context, extra, datetime) VALUES ($1, $2, $3, $4, $5, $6)');

        $this->initialized = true;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  $record[]
     * @return void
     */
    protected function write(array $record)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        $content = [
            'channel'    => $record['channel'],
            'level_name' => $record['level_name'],
            'message'    => $record['message'],
            'context'    => json_encode($record['context']),
            'extra'      => json_encode($record['extra']),
            'datetime'   => $record['datetime']->format('Y-m-d G:i:s'),
        ];
        $this->statement->execute($content);
    }
}
