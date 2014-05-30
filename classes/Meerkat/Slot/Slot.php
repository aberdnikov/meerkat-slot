<?php

    namespace Meerkat\Slot;

    class Slot {

        /**
         * Calculated ID associated to this slot.
         *
         * @var string
         */
        protected $_cache_id = null;
        protected $_id = null;
        protected $_ext = null;
        /**
         * Lifetime of this slot.
         */
        protected $_lifetime;
        protected $_default = 'cache empty';

        function __construct($id, $ext = null) {
            //calc & set cache namespace
            $this->_id       = $id;
            $this->_ext      = $ext;
            $this->_cache_id = md5(APPPATH) . '|' . get_class($this) . '|' . md5(serialize($id));
        }

        /**
         *
         * @param type $key
         * @return Slot
         */
        static function factory($id, $ext = null) {
            $class = get_called_class();
            return new $class($id, $ext);
        }

        public function set($data) {
            \Cache::instance()
            ->set($this->_cache_id, $data, $this->_lifetime);
        }

        public function get_raw() {
            return \Cache::instance()
                   ->get($this->_cache_id, $this->_default);
        }

        public function get() {
            $token = \Profiler::start('Slots', get_class($this));
            $cache = $this->get_raw();
            if ($cache != $this->_default) {
                $ret = $cache;
            }
            else {
                $ret = $this->load();
                $this->set($ret);
            }
            \Profiler::stop($token);
            return $ret;
        }

        public function remove() {
            \Cache::instance()
            ->delete($this->_cache_id);
        }

        function load() {
            throw new \Exception('Implementation of the method ' . __FUNCTION__ . ' is missed');
        }

    }