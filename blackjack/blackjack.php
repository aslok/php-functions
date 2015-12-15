<?php
  ini_set('memory_limit', '500M');
  ini_set('max_execution_time', '7200');

  function get_deck()
  {
    /*
      Трефы — clubs
      Бубны — diamonds
      Червы — hearts
      Пики  — spades

      «Валет»  = «J» — Jack
      «Дама»   = «Q» — Queen
      «Король» = «K» — King
      «Туз»    = «A» — Ace
     */
    $num_cards = array ('2', '3', '4', '5', '6', '7', '8', '9', '10');
    $pics_cards = array ('J', 'Q', 'K', 'A');
    $suits = array ('S', 'H', 'D', 'C');

    $deck = array ();

    foreach (array_merge($num_cards, $pics_cards) as $no_suits)
    {
      foreach ($suits as $suit)
      {
        $deck[] = array ('title' => $no_suits . $suit,
                         'name' => $no_suits,
                         'suit' => $suit,
                         'val_l' => in_array($no_suits, $num_cards) ?
                                      (int) $no_suits :
                                      ($no_suits == 'A' ? 11 : 10),
                         'val_m' => in_array($no_suits, $num_cards) ?
                                      (int) $no_suits :
                                      ($no_suits == 'A' ? 1 : 10));
      }
    }
    shuffle($deck);
    return $deck;
  }

  function get_cards_val_l($cards)
  {
    $sum = 0;
    foreach ($cards as $card)
    {
      $sum += $card['val_l'];
    }
    return $sum;
  }

  function get_cards_val_m($cards)
  {
    $sum = 0;
    foreach ($cards as $card)
    {
      $sum += $card['val_m'];
    }
    return $sum;
  }

  function get_cards_val($cards)
  {
    $sum = get_cards_val_l($cards);

    if ($sum > 21)
    {
      // Считаем что сумма больше чем 21
      $sum = get_cards_val_m($cards);
    }
    return $sum;
  }
?><!DOCTYPE html>
<html>
  <head>
    <title>Black Jack</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script type="text/javascript">
    </script>
  </head>
  <body>
    <pre><?php
      // Количество игроков
      $players = 1;
      // До скольки стоит брать
      $max_val = 16;
      // Стоит ли рисковать если у диллера первая карта - туз
      $risk = FALSE;


      $verbose = TRUE;
      /*$max_cash = array ();
      for ($f = $players + 1; $f--; )
      {
        $max_cash[$f] = array ('val' => -999999, 'max' => 0);
      }*/

      $cash = array ();
      $counter = array ();

      for ($f = $players + 1; $f--; )
      {
        $cash[$f] = 0;
        $counter[$f] = 0;
      }

      for ($fn = 0; $fn < 1; $fn++)
      {
        if ($verbose)
        {
          echo 'Игра №' . $fn . "\n" . str_repeat('-', 30) . "\n";
        }
        // Тусуем колоду
        $deck = get_deck();

        $cards = array ();
        $result = array ();
        // Делаем ставки
        for ($f = $players + 1; $f-- > 1; )
        {
          $cash[$f] -= 1;
          $cash[0] += 1;
        }

        // Раздаем карты
        for ($f = $players + 1; $f--; )
        {
          // Карты в руке
          $cards[$f] = array (array_shift($deck), array_shift($deck));
        }

        // Обходим игроков
        for ($f = $players + 1; $f--; )
        {
          // Хотим ли больше?
          $more = false;
          // Считаем и решаем
          do {
            if ($more)
            {
              // Раздаем ещё карту
              $cards[$f][] = array_shift($deck);
            }
            // Считаем сумму
            $result[$f] = array ('l' => get_cards_val_l($cards[$f]), 's' => get_cards_val($cards[$f]));
            if (count($cards[$f]) == 2 && $result[$f]['s'] == 21)
            {
              break;
            }
            // Если надо взять ещё или хватит
            $more = !$f ?
                      $result[$f]['s'] < 17 :
                      $result[$f]['s'] <= $max_val;
          }
          while($more);
          if ($verbose)
          {
            echo $f . ' => ';
            foreach ($cards[$f] as $card)
            {
              echo $card['title'] . ' (' . ($result[$f]['l'] > 21 ? $card['val_m'] : $card['val_l']) . '), ';
            }
            echo "\n" . 'Сумма: ' . $result[$f]['s'] . ';' . "\n";
          }
        }
        $end = FALSE;
        for ($f = $players + 1; $f--; )
        {
          if ($cards[0][0]['val_l'] >= 10 && $result[$f]['s'] == 21 && count($cards[$f]) == 2 && !$risk)
          {
            $cash[$f] += 2;
            $cash[0] -= 2;
            $end = TRUE;
          }
        }
        if ($end)
        {
          if ($verbose)
          {
            echo "\n" . 'Итоги: Диллер получил картинку - не рискуем' . "\n";
          }
        }
        else
        {
          // Результат диллера
          $dialer_val = get_cards_val($cards[0]);
          if ($verbose)
          {
            echo "\n" . 'Итоги: ';
          }
          // Обходим остальных игроков
          for ($f = $players + 1; $f-- > 1; )
          {
            // Считаем результат
            $val = get_cards_val($cards[$f]);
            // Если у игрока перебор
            if ($val > 21)
            {
              if ($verbose)
              {
                echo $f . ' - перебор; ';
              }
              $counter[$f]--;
              continue;
            }
            // Если у игрока блекджек, а у диллера нет
            if ($val == 21 && count($cards[$f]) == 2 &&
                ($dialer_val != 21 || count($cards[0]) != 2))
            {
              if ($verbose)
              {
                echo $f . ' - блекджек; ';
              }
              $cash[$f] += 2.5;
              $cash[0] -= 2.5;
              $counter[$f]++;
              continue;
            }
            // Если у диллера перебор
            if ($dialer_val > 21)
            {
              if ($verbose)
              {
                echo $f . ' - выиграл; ';
              }
              $cash[$f] += 2;
              $cash[0] -= 2;
              $counter[$f]++;
              continue;
            }
            // Если у диллера блекджек, а у игрока нет
            if ($dialer_val == 21 && count($cards[0]) == 2 &&
                ($val != 21 || count($cards[$f]) != 2))
            {
              if ($verbose)
              {
                echo $f . ' - проиграл; ';
              }
              $counter[$f]--;
              continue;
            }
            // Если игрок выиграл
            if ($val > $dialer_val)
            {
              if ($verbose)
              {
                echo $f . ' - выиграл; ';
              }
              $cash[$f] += 2;
              $cash[0] -= 2;
              $counter[$f]++;
              continue;
            }
            if ($val == $dialer_val)
            {
              if ($verbose)
              {
                echo $f . ' - ровно; ';
              }
              $cash[$f] += 1;
              $cash[0] -= 1;
              continue;
            }
            if ($verbose)
            {
              echo $f . ' - проиграл; ';
            }
            $counter[$f]--;
          }
          if ($verbose)
          {
            echo "\n";
          }
        }
      }
      for ($f = $players + 1; $f-- > 1; )
      {
        echo 'Игрок #' . $f . ' => ' . $cash[$f] . ' (' . $counter[$f] . ')' . "\n";
      }
      echo 'Казино => ' . $cash[$f] . "\n";
    ?></pre>
  </body>
</html>