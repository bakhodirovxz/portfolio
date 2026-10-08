<?php $u = current_user(); ?>
<form class="card pad narrow" method="post" action="<?= e(url('admin/account')) ?>">
  <?= csrf_field() ?>
  <h2 class="card__title">Login va parol</h2>
  <label class="field"><span>Login</span><input name="username" value="<?= e($u['username']) ?>" autocomplete="username"></label>
  <label class="field"><span>Yangi parol <small class="muted">(o'zgartirmasangiz bo'sh qoldiring)</small></span><input type="password" name="password" autocomplete="new-password" minlength="8"></label>
  <label class="field"><span>Yangi parolni takrorlang</span><input type="password" name="password2" autocomplete="new-password"></label>
  <hr>
  <label class="field"><span>Joriy parol *</span><input type="password" name="current" required autocomplete="current-password"></label>
  <button class="btn btn--primary" type="submit">Saqlash</button>
</form>
