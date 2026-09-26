<fieldset class="border rounded p-3 mb-3 banner-button-fields">
    <legend class="float-none w-auto fs-6 px-1">Banner Button</legend>
    <label class="form-check mb-2"><input class="form-check-input" type="checkbox" name="button[enabled]" value="1"> Enable clickable button</label>
    <div class="row g-2 mb-2 banner-button-main-fields">
        <label class="col-6 mb-0">Button text<input class="form-control" name="button[text]" value="Start Selling" maxlength="80"></label>
        <label class="col-6 mb-0">Button link<input class="form-control" name="button[url]" value="seller/login.php" maxlength="1000"></label>
    </div>
    <div class="row g-2 banner-button-placement-fields">
        <?php foreach (['left' => ['Left (%)', 35.1], 'top' => ['Top (%)', 71.8], 'width' => ['Width (%)', 11.3], 'height' => ['Height (%)', 14]] as $key => [$label, $value]): ?>
            <label class="col-3 mb-0"><?php echo $label; ?><input class="form-control" type="number" name="button[<?php echo $key; ?>]" min="<?php echo in_array($key, ['width', 'height']) ? 1 : 0; ?>" max="100" step="0.1" value="<?php echo $value; ?>"></label>
        <?php endforeach; ?>
    </div>
    <p class="form-text mb-2">Placement is relative to the banner. Defaults match the Start Selling design and are used on desktop and mobile.</p>
    <div class="banner-button-preview" hidden style="position:relative; line-height:0;">
        <img alt="Desktop button placement preview" style="display:block;width:100%;height:auto;">
        <span style="position:absolute;display:flex;align-items:center;justify-content:center;background:#008fbe;color:#fff;border-radius:999px;font:600 9px/1.2 sans-serif;text-align:center;overflow:hidden;">Start Selling</span>
    </div>
</fieldset>
