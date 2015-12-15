<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
    <title></title>
  </head>
  <body>
    <pre>
      <?php
      include 'timer.php';

      $timer = new Timer();

      $timer->stop('test timer'); // Игнорируем - небыло такого таймера
      $timer->start('test timer'); // Стартуем таймер

      $timer->pause_off('test timer'); // Игнорируем - небыло паузы
      sleep(1); // Это время было бы засчитано, но в следующей строке сброс
      $timer->start('test timer'); // Стартуем таймер заново, значение снова ноль

      $timer->pause_on('test timer'); // Ставим таймер на паузу
      $timer->pause_off('test timer'); // Снимаем таймер с паузы

      sleep(1); // Это время будет засчитано

      $timer->pause_on('test timer'); // Ставим таймер на паузу
      sleep(1);// Это время -= НЕ =- будет засчитано
      $timer->pause_on('test timer'); // Игнорируем - уже стоит на паузе
      $timer->pause_off('test timer'); // Снимаем таймер с паузы

      sleep(1); // Это время будет засчитано

      $timer->stop('test timer');

      echo $timer->get_duration('test timer') . ' секунд' . "\n";
      ?>
    </pre>
  </body>
</html>
