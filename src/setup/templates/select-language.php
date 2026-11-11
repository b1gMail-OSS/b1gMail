<h1><?php echo SetupH($lang_setup['selectlanguage']); ?></h1>
<p class="text-secondary"><?php echo $lang_setup['selectlanguage_text']; ?></p>
<div class="form-selectgroup form-selectgroup-boxes setup-langs">
    <?php foreach (['deutsch' => ['DE', 'Deutsch', 'German', 'de'], 'english' => ['EN', 'English', 'Englisch', 'en']] as $code => [$badge, $title, $subtitle, $class]): ?>
    <label class="form-selectgroup-item">
        <input class="form-selectgroup-input" type="radio" id="lang_<?php echo SetupH($code); ?>" name="lng" value="<?php echo SetupH($code); ?>"<?php if ($lang === $code) { echo ' checked="checked"'; } ?> />
        <span class="form-selectgroup-label setup-lang-card">
            <span class="setup-lang-badge setup-lang-badge-<?php echo SetupH($class); ?>" aria-hidden="true"><?php echo SetupH($badge); ?></span>
            <span class="setup-lang-meta"><span class="form-selectgroup-title"><?php echo SetupH($title); ?></span><span class="text-secondary small"><?php echo SetupH($subtitle); ?></span></span>
            <span class="form-selectgroup-check"></span>
        </span>
    </label>
    <?php endforeach; ?>
</div>
