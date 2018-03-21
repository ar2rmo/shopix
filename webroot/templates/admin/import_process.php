<? /*

array (size=4)
  'file' => string './data/import--2015-12-18--13-20-11.xlsx' (length=40)
  'method' => null
  'state' => string 'READ' (length=4)
  'offset' => int 0
float 66.287466049194
array (size=4)
  'file' => string './data/import--2015-12-18--13-20-11.xlsx' (length=40)
  'method' => null
  'state' => string 'READ' (length=4)
  'offset' => int 500

*/ ?>

<? if ($after['state']=='READ') { ?>
Чтение исходного файла. Обработано <?=$after['offset'] ?> строк.
<br/><br/>
<a href="?stop" class="delete">Отменить</a>
<? } elseif ($after['state']=='READ_DONE') { ?>
Обновление данных
<br/><br/>
<a href="?stop" class="delete">Отменить</a>
<? } ?>