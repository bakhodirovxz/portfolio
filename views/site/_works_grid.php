<?php
/** Ishlarni assimetrik to'rda chiqaradi: 7+5, 5+7, ... toq qolgani to'liq kenglikda. */
$cnt = count($works);
$spans = [];
for ($k = 0; $k < $cnt; $k += 2) {
    if ($k + 1 >= $cnt) { $spans[] = 12; break; }
    $spans = array_merge($spans, ($k / 2) % 2 === 0 ? [7, 5] : [5, 7]);
}
?>
<div class="grid works">
  <?php foreach ($works as $i => $w) { $span = $spans[$i]; require __DIR__ . '/_work_card.php'; } ?>
</div>
