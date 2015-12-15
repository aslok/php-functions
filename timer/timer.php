<?php
  class TimerPause
  {
    private $start;
    private $finish;

    function TimerPause()
    {
      $this->start = $this->microtime();
      $this->finish = 0;
    }

    function stop()
    {
      if ($this->finish || !$this->start)
      {
        return false;
      }
      $this->finish = $this->microtime();
    }

    function status()
    {
      return $this->finish != 0;
    }

    function get_duration()
    {
      return $this->finish - $this->start;
    }

    function microtime()
    {
      static $incr = 0;
      $incr++;
      list($usec, $sec) = explode(' ', microtime());
      return ((float) $usec + (float) $sec + ($incr * 0.0001));
    }
  }

  class TimerPoint
  {
    var $name;
    var $start;
    var $finish;
    var $delays;

    function TimerPoint($name, $do_not_start = false)
    {

      $this->name = $name;
      $this->start = $do_not_start ? 0 : $this->microtime();
      $this->finish = 0;
      $this->delays = array ();
    }

    function start()
    {
      if ($this->finish || $this->start)
      {
        return false;
      }
      $this->finish = $this->microtime();
    }

    function stop()
    {
      if ($this->finish || !$this->start)
      {
        return false;
      }
      $this->finish = $this->microtime();
    }

    function reset($do_not_start = false)
    {
      $this->start = $do_not_start ? 0 : $this->microtime();
      $this->finish = 0;
    }

    function pause_on()
    {
      $last_pause = end($this->delays);
      if ($last_pause && !$last_pause->status())
      {
        return false;
      }
      $this->delays[] = new TimerPause();
    }

    function pause_off()
    {
      if (!count($this->delays))
      {
        return false;
      }
      $last_pause =& $this->delays[count($this->delays) - 1];
      if ($last_pause->status())
      {
        return false;
      }
      $last_pause->stop();
    }

    function get_name()
    {
      return $this->name;
    }

    function get_duration()
    {
      $delays = 0;
      foreach ($this->delays as $delay)
      {
        $delays += $delay->get_duration();
      }
      return $this->finish - $this->start - $delays;
    }

    function microtime()
    {
      static $incr = 0;
      $incr++;
      list($usec, $sec) = explode(' ', microtime());
      return ((float) $usec + (float) $sec + ($incr * 0.0001));
    }
  }

  class Timer
  {
    private $points;

    function Timer()
    {
      $this->points = array ();
    }

    function start($name)
    {
      if (!isset ($this->points[$name]))
      {
        $this->points[$name] = new TimerPoint();
      }
      else
      {
        $this->points[$name]->reset();
      }
    }

    function stop($name)
    {
      if (!isset ($this->points[$name]))
      {
        return false;
      }
      $this->points[$name]->stop();
    }

    function pause_on($name)
    {
      if (!isset ($this->points[$name]))
      {
        return false;
      }
      $this->points[$name]->pause_on();
    }

    function pause_off($name)
    {
      if (!isset ($this->points[$name]))
      {
        return false;
      }
      $this->points[$name]->pause_off();
    }

    function get_duration($name)
    {
      if (!isset ($this->points[$name]))
      {
        return false;
      }
      return $this->points[$name]->get_duration();
    }
  }
?>
