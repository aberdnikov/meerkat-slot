Slot-cache module for MeerkatCMF
============

Слот - это не просто ячейка памяти в кэше, это некая прослойка, умеющая при запросе по ключу либо взять значение из кэша или в случае его отсутствия 
произвести вычисления, отдать значение и положить в его кэш.
Для создания слота необходимо наследовать Meerkat\Slot\Slot и переопределить метод load, который как раз и вычисляет (например, получает значение из БД)
~~~
namespace Meerkat\Slot;

use Meerkat\Slot\Slot;
use Meerkat\Core\Theme;
use \Kohana as Kohana;
use \Profiler as Profiler;

class Slot_PathTemplate extends Slot {

    protected $_lifetime = 1;

    function load() {
        $token = Profiler::start(__CLASS__, $this->_id);
        $themes_dirs = array();
        if ($theme = Theme::instance()->get()) {
            $themes_dirs['user'] = Theme::instance()->get();
        }
        $themes_dirs['default'] = '!';
        $path = false;
        foreach ($themes_dirs as $themes_dir) {
            $tpl_dir = 'tpl' . '/' . $themes_dir;
            $path = Kohana::find_file($tpl_dir, $this->_id, 'html');
        }
        if (!$path) {
            //print $tpl_dir. $this->_id. 'html';exit;
            throw new \Exception('Template ' . $this->_id . ' not found');
        }
        Profiler::stop($token);
        return $path;
    }

}
~~~
