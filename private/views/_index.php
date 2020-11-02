<?php
include('private/components/item.php');
?>
<h2>On sale!</h2>
<div class="grid grid-4">
    <?php
        $items = getFrontPageItems("percentage", true);
        foreach($items as $item) {
            comp_itemDisplay($item);
        }
    ?>
</div>
<h2>New!</h2>
<div class="grid grid-4">
    <?php
        $items = getFrontPageItems("date_added", false);
        foreach($items as $item) {
            comp_itemDisplay($item);
        }
    ?>
</div>
