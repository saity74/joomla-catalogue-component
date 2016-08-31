function isInt(value) {
    return !isNaN(value) && parseInt(Number(value)) == value && !isNaN(parseInt(value, 10));
}

jQuery(function ($) {
    var $catSelect = $('#jform_catid'),
        $attrGroups = $('.cat-attr-group'),
        $noAttrsAlert = $('#noAttrsAlert'),
        attrGroupsByCategies = {},
        activeAttrGroups = [];

    $attrGroups.each(function(i, e) {
        var $attrGroup = $(e),
            catIds = $attrGroup.data('catIds');


        if (isInt(catIds)) {
            if ( ! $.isArray(attrGroupsByCategies[catIds]) ) {
                attrGroupsByCategies[catIds] = [];
            }

            attrGroupsByCategies[catIds].push($attrGroup);
        } else {
            $.each(catIds.split(','), function(i, catId) {
                if ( ! $.isArray(attrGroupsByCategies[catId]) ) {
                    attrGroupsByCategies[catId] = [];
                }
                attrGroupsByCategies[catId].push($attrGroup);
            });
        }
    });

    filterAttrGroups();

    $catSelect.change(filterAttrGroups);

    function filterAttrGroups() {
        $attrGroups.hide();

        var catId = $catSelect.val();

        if (catId in attrGroupsByCategies) {
            $noAttrsAlert.hide();

            $.each(attrGroupsByCategies[catId], function(i, $e) {
                $e.show();
            });

            activeAttrGroups = attrGroupsByCategies[catId];
        } else {
            $noAttrsAlert.show();
            activeAttrGroups = null;
        }
    }

    $('#item-form').submit(clearNonActive);

    function clearNonActive() {
        $attrGroups
            .filter(function(i, e) {
                for ( var j = 0; j < activeAttrGroups.length; j++) {
                    var $e = activeAttrGroups[j];
                    if (e === $e[0]) {
                        return false;
                    }
                }
                return true;
            })
            .each(function(i, e) {
                $(e).find('input').val('');
            });
    }
});