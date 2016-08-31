jQuery(function($) {
    var $batchCopyMove = $("#batch-copy-move");

    if ($batchCopyMove.length) {
        $batchCopyMove.hide();

        var $batchSelector = $("#batch-category-id, #batch-menu-id, #batch-position-id, #batch-group-id");

        $batchSelector.change(function(){
            if ($batchSelector.val() != 0 || $batchSelector.val() != "") {
                $batchCopyMove.show();
            } else {
                $batchCopyMove.hide();
            }
        });
    }
});