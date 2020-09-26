<?php
/**
 * @var \App\View\AppView $this
 * @var array $params
 * @var string $message
 */
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="w3-panel w3-red w3-display-container"><span onclick="this.parentElement.style.display='none'"
  class="w3-button w3-large w3-display-topright">&times;</span><p><?= $message ?></p></div>
