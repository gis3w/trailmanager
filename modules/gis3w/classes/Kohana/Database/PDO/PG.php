<?php defined('SYSPATH') or die('No direct script access.');
/**
 * PDO database connection.
 *
 * @package    Kohana/Database
 * @category   Drivers
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Database_PDO_PG extends Kohana_Database_PDO {
    
    // VA AGGIUNTO L'IDENTIFIER ANCHE SE USIAMO PDO ALTRIMENTI LE QUERY COMPLESSE NON FUNZIONANO
    protected $_identifier = '"';
    
    public function list_columns($table, $like = NULL, $add_prefix = TRUE) {
        
        $this->_connection or $this->connect();

        $sql = 'SELECT column_name, column_default, is_nullable, data_type, character_maximum_length, numeric_precision, numeric_scale, datetime_precision'
                .' FROM information_schema.columns'
                .' WHERE table_schema = '.$this->quote($this->schema())
                .' AND table_name = '.$this->quote($add_prefix ? ($this->table_prefix().$table) : $table);

        if (is_string($like))
        {
                $sql .= ' AND column_name LIKE '.$this->quote($like);
        }

        $sql .= ' ORDER BY ordinal_position';

        $result = array();

        foreach ($this->query(Database::SELECT, $sql, FALSE) as $column)
        {
                $column = array_merge($this->datatype($column['data_type']), $column);

                $column['is_nullable'] = ($column['is_nullable'] === 'YES');

                $result[$column['column_name']] = $column;
        }

        return $result;
    }
    
    public function list_tables($like = NULL) {
        
        $this->_connection or $this->connect();

        $sql = 'SELECT table_name FROM information_schema.tables WHERE table_schema = '.$this->quote($this->schema());

        if (is_string($like))
        {
                $sql .= ' AND table_name LIKE '.$this->quote($like);
        }

        return $this->query(Database::SELECT, $sql, FALSE)->as_array(NULL, 'table_name');
    }
    
    public function schema()
    {
            return $this->_config['schema'];
    }
    
    public function query($type, $sql, $as_object = FALSE, array $params = NULL,$as_num = FALSE)
    {
            // Make sure the database is connected
            $this->_connection or $this->connect();

            if (Kohana::$profiling)
            {
                    // Benchmark this query for the current instance
                    $benchmark = Profiler::start("Database ({$this->_instance})", $sql);
            }

            try
            {
                    if ($type === Database::INSERT)
                        $sql .= " RETURNING ".$this->_config['column_primary_key'];
                    
                    $result = $this->_connection->query($sql);
            }
            catch (Exception $e)
            {
                    if (isset($benchmark))
                    {
                            // This benchmark is worthless
                            Profiler::delete($benchmark);
                    }

                    // Convert the exception in a database exception
                    throw new Database_Exception(':error [ :query ]',
                            array(
                                    ':error' => $e->getMessage(),
                                    ':query' => $sql
                            ),
                            $e->getCode());
            }

            if (isset($benchmark))
            {
                    Profiler::stop($benchmark);
            }

            // Set the last query
            $this->last_query = $sql;

            if ($type === Database::SELECT)
            {
                    // Convert the result into an array, as PDOStatement::rowCount is not reliable
                    if ($as_object === FALSE)
                    {
                            if($as_num)
                            {
                                $result->setFetchMode(PDO::FETCH_NUM);
                            }
                            else
                            {
                                $result->setFetchMode(PDO::FETCH_ASSOC);
                            }
                            
                    }
                    elseif (is_string($as_object))
                    {
                            $result->setFetchMode(PDO::FETCH_CLASS, $as_object, $params);
                    }
                    else
                    {
                            $result->setFetchMode(PDO::FETCH_CLASS, 'stdClass');
                    }

                    $result = $result->fetchAll();

                    // Return an iterator of results
                    return new Database_Result_Cached($result, $sql, $as_object, $params);
            }
            elseif ($type === Database::INSERT)
            {
                    // Return a list of insert id and rows created
                    $resInsert = $result->fetchAll();

                    return array(
                            $resInsert[0][$this->_config['column_primary_key']],
                            $result->rowCount(),
                    );
            }
            else
            {
                    // Return the number of rows affected
                    return $result->rowCount();
            }
    }
    
    /**
     * Setta i paramentri del config caricato
     * @param String $param
     * @param Varius $value
     */
    public function setConfig($param,$value)
    {
        if(isset($this->_config[$param]))
            $this->_config[$param] = $value;
    }


}
