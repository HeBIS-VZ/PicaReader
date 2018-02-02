<?php

/**
 * The PicaPlainReader class file.
 *
 * This file is part of PicaReader.
 *
 * PicaReader is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PicaReader is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PicaReader.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   PicaReader
 * @author    David Maus <maus@hab.de>
 * @copyright Copyright (c) 2012 - 2016 by Herzog August Bibliothek Wolfenbüttel
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License v3
 */

namespace HAB\Pica\Reader;

use HAB\Pica\Parser\PicaPlainParserInterface;
use HAB\Pica\Parser\PicaPlainParser;

class PicaPlainReader extends Reader
{

    /**
     * Current input data.
     *
     * @var array
     */
    protected $_data;

    /**
     * Parser instance.
     *
     * @var PicaPlainParser
     */
    private $_parser;

    /**
     * Constructor.
     *
     * @param  PicaPlainParserInterface $parser Optional parser instance
     */
    public function __construct (PicaPlainParserInterface $parser = null)
    {
        $this->_parser = $parser ?: new PicaPlainParser();
    }

    /**
     * Open the reader with input stream.
     *
     * @param  resource|string $data
     * @return void
     */
    public function open ($data)
    {
        $this->_data = preg_split("/(?:\n\r|[\n\r])/", $data);
    }

    /**
     * {@inheritDoc}
     */
    protected function next ()
    {
        $record = false;
        if (current($this->_data) !== false) {
            $record = array('fields' => array());
            for ($i = 0; $i < count($this->_data); ++$i) {
                $line = $this->_data[$i];
                if (empty($line)) {
                    continue;
                }
                //adapted to output à la shwdb cg 20180202
                if (preg_match('#^lok\:\s[0-9]+\s([0-9]+)#Du', $line, $match)) {
                    $line = '101@/00 $a' . $match[1];
                }
                $field = $this->_parser->parseField($line);
                if ($field !== false) {
                    $record['fields'] []= $field;
                }
            }
        }
        return $record;
    }

    /**
     * Close reader.
     *
     * @return void
     */
    public function close ()
    {
        $this->_data = null;
    }
}
