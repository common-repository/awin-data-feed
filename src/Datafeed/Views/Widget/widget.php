<?php
echo '
<form name="swFeed" id="swFeed' . $layout . '">
    <input name="title" type="hidden" value="' . $instance['title'] . '"/>
    <input name="displayCount" type="hidden" value="' . $instance['displayCount'] . '"/>
    <input name="layout" type="hidden" value="' . $instance['layout'] . '"/>
    <input name="keywords" type="hidden" value="' . $instance['keywords'] . '"/>
    <input name="action" type="hidden" value="get_sw_product"/>
</form>
<div class="widgetContent">
    <div class="ajaxResponse' . $layout . '" id="ajaxResponse' . $layout . '"></div>
    <div class="next' . $layout . '"><button id="next' . $layout . '" class="next" style="display:none"></button></div>
</div>
';
